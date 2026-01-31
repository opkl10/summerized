<?php
/**
 * Plugin Name: Claude AI Summarizer
 * Plugin URI: https://github.com/YOUR_USERNAME/claude-ai-summarizer
 * Description: סיכום פוסטים ומאמרים חכם באמצעות Claude AI. מוסיף כפתור סיכום אוטומטי לכל פוסט.
 * Version: 1.2.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: claude-ai-summarizer
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CLAUDE_SUMMARIZER_VERSION', '1.2.0');
define('CLAUDE_SUMMARIZER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CLAUDE_SUMMARIZER_PLUGIN_URL', plugin_dir_url(__FILE__));

class Claude_AI_Summarizer {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Load text domain for translations
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // Admin hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // Handle form submission via admin_post hook
        add_action('admin_post_claude_save_settings', array($this, 'handle_settings_save'));
        
        // Frontend hooks
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_action('wp_footer', array($this, 'add_summary_button'));
        
        // Add button inside content for specific positions
        add_filter('the_content', array($this, 'add_button_to_content'), 10, 1);
        
        // AJAX hooks
        add_action('wp_ajax_claude_summarize', array($this, 'ajax_summarize'));
        add_action('wp_ajax_nopriv_claude_summarize', array($this, 'ajax_summarize'));
        add_action('wp_ajax_claude_check_update_manual', array($this, 'ajax_check_update_manual'));
        add_action('wp_ajax_claude_install_update', array($this, 'ajax_install_update'));
        add_action('wp_ajax_claude_remove_icon', array($this, 'ajax_remove_icon'));
        add_action('wp_ajax_claude_upload_icon_ajax', array($this, 'ajax_upload_icon'));
        
        // Shortcode
        add_shortcode('claude_summary', array($this, 'summary_shortcode'));
        
        // Gutenberg block
        add_action('init', array($this, 'register_gutenberg_block'));
        
        // Auto-update from GitHub - check on admin pages only (every 2 hours)
        add_action('admin_init', array($this, 'check_for_updates'));
        
        // Also check when visiting the settings page
        add_action('load-settings_page_claude-ai-summarizer', array($this, 'check_for_updates'));
        
        // Webhook endpoint for GitHub updates
        add_action('rest_api_init', array($this, 'register_webhook_endpoint'));
        
        // Scheduled update check
        add_action('claude_check_updates_cron', array($this, 'check_for_updates'));
        
        // Activation hook - schedule update checks
        register_activation_hook(__FILE__, array($this, 'activate_plugin'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate_plugin'));
    }
    
    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'claude-ai-summarizer',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('Claude AI Summarizer', 'claude-ai-summarizer'),
            __('Claude Summarizer', 'claude-ai-summarizer'),
            'manage_options',
            'claude-ai-summarizer',
            array($this, 'render_admin_page')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('claude_summarizer_settings', 'claude_api_key', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        ));
        register_setting('claude_summarizer_settings', 'claude_model', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'claude-3-5-sonnet-20241022'
        ));
        register_setting('claude_summarizer_settings', 'claude_summary_length', array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_summary_length'),
            'default' => 'medium'
        ));
        register_setting('claude_summarizer_settings', 'claude_auto_button', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '1'
        ));
        register_setting('claude_summarizer_settings', 'claude_button_position', array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_button_position'),
            'default' => 'bottom-left'
        ));
        register_setting('claude_summarizer_settings', 'claude_show_icon', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '1'
        ));
        register_setting('claude_summarizer_settings', 'claude_button_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#667eea'
        ));
        register_setting('claude_summarizer_settings', 'claude_button_icon', array(
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'default' => ''
        ));
        register_setting('claude_summarizer_settings', 'claude_button_text', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'סכם עם AI'
        ));
        
        register_setting('claude_summarizer_settings', 'claude_github_repo', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        ));
        register_setting('claude_summarizer_settings', 'claude_auto_update', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '0'
        ));
        register_setting('claude_summarizer_settings', 'claude_auto_install', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '0'
        ));
        register_setting('claude_summarizer_settings', 'claude_webhook_secret', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        ));
    }
    
    /**
     * Sanitize summary length
     */
    public function sanitize_summary_length($value) {
        $allowed = array('short', 'medium', 'long');
        return in_array($value, $allowed, true) ? $value : 'medium';
    }
    
    /**
     * Sanitize button position
     */
    public function sanitize_button_position($value) {
        $allowed = array(
            'bottom-left', 'bottom-right', 'top-left', 'top-right',
            'before-content', 'after-content', 'inside-content-top', 'inside-content-bottom'
        );
        return in_array($value, $allowed, true) ? $value : 'bottom-left';
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'settings_page_claude-ai-summarizer') {
            return;
        }
        
        wp_enqueue_style(
            'claude-admin-style',
            CLAUDE_SUMMARIZER_PLUGIN_URL . 'assets/admin.css',
            array(),
            CLAUDE_SUMMARIZER_VERSION
        );
        
        wp_enqueue_script(
            'claude-admin-script',
            CLAUDE_SUMMARIZER_PLUGIN_URL . 'assets/admin.js',
            array('jquery', 'wp-color-picker'),
            CLAUDE_SUMMARIZER_VERSION,
            true
        );
        
        wp_localize_script('claude-admin-script', 'claudeAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('claude_admin_nonce')
        ));
        
        // Add color picker
        wp_enqueue_style('wp-color-picker');
    }
    
    /**
     * Enqueue frontend scripts
     */
    public function enqueue_frontend_scripts() {
        if (!is_singular('post')) {
            return;
        }
        
        $auto_button = get_option('claude_auto_button', '1');
        if ($auto_button !== '1') {
            return;
        }
        
        wp_enqueue_style(
            'claude-frontend-style',
            CLAUDE_SUMMARIZER_PLUGIN_URL . 'assets/frontend.css',
            array(),
            CLAUDE_SUMMARIZER_VERSION
        );
        
        wp_enqueue_script(
            'claude-frontend-script',
            CLAUDE_SUMMARIZER_PLUGIN_URL . 'assets/frontend.js',
            array('jquery'),
            CLAUDE_SUMMARIZER_VERSION,
            true
        );
        
        $button_color = get_option('claude_button_color', '#667eea');
        $button_icon = get_option('claude_button_icon', '');
        $button_text = get_option('claude_button_text', 'סכם עם AI');
        $show_icon = get_option('claude_show_icon', '1');
        
        wp_localize_script('claude-frontend-script', 'claudeFrontend', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('claude_summarize_nonce'),
            'postId' => get_the_ID(),
            'buttonText' => $button_text ?: __('🤖 Summarize with AI', 'claude-ai-summarizer'),
            'loadingText' => __('Summarizing...', 'claude-ai-summarizer'),
            'position' => get_option('claude_button_position', 'bottom-left'),
            'buttonColor' => $button_color,
            'buttonIcon' => $button_icon,
            'showIcon' => $show_icon === '1',
            'i18n' => array(
                'error' => __('Error occurred. Please try again.', 'claude-ai-summarizer'),
                'copied' => __('Copied!', 'claude-ai-summarizer'),
                'copy' => __('Copy', 'claude-ai-summarizer'),
                'close' => __('Close', 'claude-ai-summarizer')
            )
        ));
        
        // Add inline CSS for button color (will be overridden by JS if needed)
        if ($button_color) {
            $custom_css = "
                .claude-btn {
                    background: {$button_color} !important;
                }
            ";
            wp_add_inline_style('claude-frontend-style', $custom_css);
        }
    }
    
    /**
     * Add summary button to posts (for fixed positions)
     */
    public function add_summary_button() {
        if (!is_singular('post')) {
            return;
        }
        
        $auto_button = get_option('claude_auto_button', '1');
        if ($auto_button !== '1') {
            return;
        }
        
        $position = get_option('claude_button_position', 'bottom-left');
        
        // Only show in footer for fixed positions
        $fixed_positions = array('bottom-left', 'bottom-right', 'top-left', 'top-right');
        if (!in_array($position, $fixed_positions, true)) {
            return;
        }
        
        $this->render_button($position);
    }
    
    /**
     * Add button to content (for content positions)
     */
    public function add_button_to_content($content) {
        if (!is_singular('post')) {
            return $content;
        }
        
        $auto_button = get_option('claude_auto_button', '1');
        if ($auto_button !== '1') {
            return $content;
        }
        
        $position = get_option('claude_button_position', 'bottom-left');
        
        // Only add to content for specific positions
        $content_positions = array('before-content', 'after-content', 'inside-content-top', 'inside-content-bottom');
        if (!in_array($position, $content_positions, true)) {
            return $content;
        }
        
        ob_start();
        $this->render_button($position, true);
        $button_html = ob_get_clean();
        
        switch ($position) {
            case 'before-content':
                return $button_html . $content;
            case 'after-content':
                return $content . $button_html;
            case 'inside-content-top':
                // Add after first paragraph
                $content_parts = preg_split('/(<\/p>)/', $content, 2, PREG_SPLIT_DELIM_CAPTURE);
                if (count($content_parts) > 2) {
                    return $content_parts[0] . $content_parts[1] . $button_html . $content_parts[2];
                }
                return $button_html . $content;
            case 'inside-content-bottom':
                // Add before last paragraph
                $content_parts = preg_split('/(<p[^>]*>)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
                if (count($content_parts) > 2) {
                    $last_p = array_pop($content_parts);
                    $before_last_p = array_pop($content_parts);
                    return implode('', $content_parts) . $button_html . $before_last_p . $last_p;
                }
                return $content . $button_html;
            default:
                return $content;
        }
    }
    
    /**
     * Render button HTML
     */
    private function render_button($position, $inline = false) {
        $button_icon = get_option('claude_button_icon', '');
        $button_text = get_option('claude_button_text', 'סכם עם AI');
        $show_icon = get_option('claude_show_icon', '1');
        
        $wrapper_class = $inline ? 'claude-summary-button-inline' : 'claude-summary-button';
        ?>
        <div id="claude-summary-button" class="<?php echo esc_attr($wrapper_class); ?> claude-position-<?php echo esc_attr($position); ?>">
            <button id="claude-summarize-btn" class="claude-btn">
                <?php if ($show_icon === '1'): ?>
                    <?php if ($button_icon): ?>
                        <span class="claude-btn-icon"><img src="<?php echo esc_url($button_icon); ?>" alt="Icon" style="width: 18px; height: 18px; object-fit: contain;" /></span>
                    <?php else: ?>
                        <span class="claude-btn-icon">🤖</span>
                    <?php endif; ?>
                <?php endif; ?>
                <span class="claude-btn-text"><?php echo esc_html($button_text); ?></span>
            </button>
        </div>
        <?php
    }
    
    /**
     * AJAX handler for summarization
     */
    public function ajax_summarize() {
        check_ajax_referer('claude_summarize_nonce', 'nonce');
        
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        
        if (!$post_id) {
            wp_send_json_error(array(
                'message' => __('Post ID required', 'claude-ai-summarizer')
            ));
            return;
        }
        
        // Verify user can read this post
        $post = get_post($post_id);
        if (!$post) {
            wp_send_json_error(array(
                'message' => __('Post not found', 'claude-ai-summarizer')
            ));
            return;
        }
        
        // Check if post is published or user can edit it
        if ($post->post_status !== 'publish' && !current_user_can('edit_post', $post_id)) {
            wp_send_json_error(array(
                'message' => __('You do not have permission to summarize this post', 'claude-ai-summarizer')
            ));
            return;
        }
        
        // Get post content - sanitize and prepare
        $content = $post->post_content;
        
        // Remove HTML tags and decode entities
        $content = wp_strip_all_tags($content);
        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
        
        // Remove shortcodes
        $content = strip_shortcodes($content);
        
        // Apply filters
        $content = apply_filters('claude_summarizer_content', $content, $post_id);
        
        // Limit content length (Claude API has token limits)
        if (mb_strlen($content, 'UTF-8') > 10000) {
            $content = mb_substr($content, 0, 10000, 'UTF-8') . '...';
        }
        
        // Sanitize final content
        $content = sanitize_text_field($content);
        
        // Get settings
        $api_key = get_option('claude_api_key');
        $model = get_option('claude_model', 'claude-3-5-sonnet-20241022');
        $length = get_option('claude_summary_length', 'medium');
        
        if (!$api_key) {
            wp_send_json_error(array(
                'message' => __('API Key not configured. Please configure it in Settings → Claude Summarizer', 'claude-ai-summarizer')
            ));
            return;
        }
        
        // Check for cached summary
        $cached_summary = get_post_meta($post_id, '_claude_summary', true);
        $cache_time = get_post_meta($post_id, '_claude_summary_time', true);
        
        // Use cache if less than 24 hours old
        if ($cached_summary && $cache_time && (time() - $cache_time) < 86400) {
            wp_send_json_success(array(
                'summary' => $cached_summary,
                'cached' => true
            ));
            return;
        }
        
        // Call Claude API
        $summary = $this->summarize_with_claude($content, $api_key, $model, $length);
        
        if (is_wp_error($summary)) {
            wp_send_json_error(array('message' => $summary->get_error_message()));
            return;
        }
        
        // Sanitize and cache the summary
        $summary = wp_kses_post($summary);
        update_post_meta($post_id, '_claude_summary', $summary);
        update_post_meta($post_id, '_claude_summary_time', time());
        
        wp_send_json_success(array(
            'summary' => $summary,
            'cached' => false
        ));
    }
    
    /**
     * Summarize content with Claude API
     */
    private function summarize_with_claude($text, $api_key, $model, $length) {
        $length_instructions = array(
            'short' => 'סכם בקצרה ב-2-3 משפטים',
            'medium' => 'סכם בפסקה אחת',
            'long' => 'סכם בפירוט במספר פסקאות'
        );
        
        $prompt = ($length_instructions[$length] ?? $length_instructions['medium']) . "\n\nטקסט לסיכום:\n" . $text;
        
        $response = wp_remote_post('https://api.anthropic.com/v1/messages', array(
            'timeout' => 30,
            'headers' => array(
                'Content-Type' => 'application/json',
                'x-api-key' => $api_key,
                'anthropic-version' => '2023-06-01'
            ),
            'body' => json_encode(array(
                'model' => $model,
                'max_tokens' => 1024,
                'messages' => array(
                    array(
                        'role' => 'user',
                        'content' => $prompt
                    )
                )
            ))
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        $status_code = wp_remote_retrieve_response_code($response);
        
        if ($status_code !== 200) {
            $error_message = isset($data['error']['message']) 
                ? $data['error']['message'] 
                : 'Unknown error from Claude API';
            return new WP_Error('claude_api_error', $error_message);
        }
        
        if (isset($data['content'][0]['text'])) {
            return $data['content'][0]['text'];
        }
        
        return new WP_Error('claude_api_error', 'Invalid response from Claude API');
    }
    
    /**
     * Adjust color brightness
     */
    private function adjust_brightness($hex, $steps) {
        $steps = max(-255, min(255, $steps));
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));
        
        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . 
                   str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . 
                   str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }
    
    /**
     * Handle icon upload
     */
    public function handle_icon_upload() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission', 'claude-ai-summarizer'));
        }
        
        check_admin_referer('claude_upload_icon');
        
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        
        $uploadedfile = $_FILES['claude_icon_file'];
        $upload_overrides = array(
            'test_form' => false,
            'mimes' => array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'gif' => 'image/gif',
                'png' => 'image/png',
                'svg' => 'image/svg+xml',
                'webp' => 'image/webp'
            )
        );
        
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
        
        if ($movefile && !isset($movefile['error'])) {
            update_option('claude_button_icon', $movefile['url']);
            wp_redirect(admin_url('options-general.php?page=claude-ai-summarizer&icon_uploaded=1'));
            exit;
        } else {
            wp_redirect(admin_url('options-general.php?page=claude-ai-summarizer&icon_upload_error=1'));
            exit;
        }
    }
    
    /**
     * Handle settings save via admin_post hook
     */
    public function handle_settings_save() {
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to access this page.', 'claude-ai-summarizer'));
        }
        
        // Verify nonce
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'claude_summarizer_settings-options')) {
            wp_die(__('Security check failed.', 'claude-ai-summarizer'));
        }
        
        // Save all settings
        if (isset($_POST['claude_api_key'])) {
            update_option('claude_api_key', sanitize_text_field($_POST['claude_api_key']));
        }
        if (isset($_POST['claude_model'])) {
            update_option('claude_model', sanitize_text_field($_POST['claude_model']));
        }
        if (isset($_POST['claude_summary_length'])) {
            $length = sanitize_text_field($_POST['claude_summary_length']);
            $allowed = array('short', 'medium', 'long');
            update_option('claude_summary_length', in_array($length, $allowed) ? $length : 'medium');
        }
        if (isset($_POST['claude_auto_button'])) {
            update_option('claude_auto_button', '1');
        } else {
            update_option('claude_auto_button', '0');
        }
        if (isset($_POST['claude_button_position'])) {
            $position = sanitize_text_field($_POST['claude_button_position']);
            $allowed = array(
                'bottom-left', 'bottom-right', 'top-left', 'top-right',
                'before-content', 'after-content', 'inside-content-top', 'inside-content-bottom'
            );
            update_option('claude_button_position', in_array($position, $allowed) ? $position : 'bottom-left');
        }
        if (isset($_POST['claude_button_color'])) {
            $color = sanitize_text_field($_POST['claude_button_color']);
            update_option('claude_button_color', preg_match('/^#[a-fA-F0-9]{6}$/', $color) ? $color : '#667eea');
        }
        if (isset($_POST['claude_button_text'])) {
            update_option('claude_button_text', sanitize_text_field($_POST['claude_button_text']));
        }
        if (isset($_POST['claude_github_repo'])) {
            update_option('claude_github_repo', sanitize_text_field($_POST['claude_github_repo']));
        }
        if (isset($_POST['claude_auto_update'])) {
            update_option('claude_auto_update', '1');
        } else {
            update_option('claude_auto_update', '0');
        }
        if (isset($_POST['claude_auto_install'])) {
            update_option('claude_auto_install', '1');
        } else {
            update_option('claude_auto_install', '0');
        }
        if (isset($_POST['claude_webhook_secret'])) {
            update_option('claude_webhook_secret', sanitize_text_field($_POST['claude_webhook_secret']));
        }
        if (isset($_POST['claude_show_icon'])) {
            update_option('claude_show_icon', '1');
        } else {
            update_option('claude_show_icon', '0');
        }
        
        // Redirect back to settings page with success message
        wp_safe_redirect(admin_url('options-general.php?page=claude-ai-summarizer&settings-updated=true'));
        exit;
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        include CLAUDE_SUMMARIZER_PLUGIN_DIR . 'templates/admin-page.php';
    }
    
    /**
     * Summary shortcode
     */
    public function summary_shortcode($atts) {
        $atts = shortcode_atts(array(
            'post_id' => get_the_ID(),
            'length' => 'medium'
        ), $atts);
        
        $post_id = intval($atts['post_id']);
        $post = get_post($post_id);
        
        if (!$post) {
            return '';
        }
        
        $summary = get_post_meta($post_id, '_claude_summary', true);
        
        if (!$summary) {
            return '<p>' . esc_html__('Summary not available. Click the "Summarize with AI" button to create a summary.', 'claude-ai-summarizer') . '</p>';
        }
        
        return '<div class="claude-summary-content">' . wp_kses_post($summary) . '</div>';
    }
    
    /**
     * Register Gutenberg block
     */
    public function register_gutenberg_block() {
        if (!function_exists('register_block_type')) {
            return;
        }
        
        register_block_type('claude-ai/summary', array(
            'render_callback' => array($this, 'render_summary_block'),
            'attributes' => array(
                'postId' => array(
                    'type' => 'number',
                    'default' => 0
                )
            )
        ));
    }
    
    /**
     * Render summary block
     */
    public function render_summary_block($attributes) {
        $post_id = isset($attributes['postId']) && $attributes['postId'] > 0 
            ? $attributes['postId'] 
            : get_the_ID();
        
        $summary = get_post_meta($post_id, '_claude_summary', true);
        
        if (!$summary) {
            return '<div class="claude-summary-block"><p>' . esc_html__('Summary not available', 'claude-ai-summarizer') . '</p></div>';
        }
        
        return '<div class="claude-summary-block">' . wp_kses_post($summary) . '</div>';
    }
    
    /**
     * Activate plugin - schedule update checks
     */
    public function activate_plugin() {
        if (!wp_next_scheduled('claude_check_updates_cron')) {
            wp_schedule_event(time(), 'twicedaily', 'claude_check_updates_cron');
        }
    }
    
    /**
     * Deactivate plugin - clear scheduled events
     */
    public function deactivate_plugin() {
        wp_clear_scheduled_hook('claude_check_updates_cron');
    }
    
    /**
     * Register REST API endpoint for GitHub webhook
     */
    public function register_webhook_endpoint() {
        register_rest_route('claude/v1', '/webhook', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_webhook'),
            'permission_callback' => array($this, 'webhook_permission_check'),
        ));
    }
    
    /**
     * Check webhook permission
     */
    public function webhook_permission_check($request) {
        // Get secret from settings
        $webhook_secret = get_option('claude_webhook_secret', '');
        
        if (empty($webhook_secret)) {
            return false;
        }
        
        // Verify GitHub signature
        $signature = $request->get_header('X-Hub-Signature-256');
        $payload = $request->get_body();
        $expected_signature = 'sha256=' . hash_hmac('sha256', $payload, $webhook_secret);
        
        return hash_equals($expected_signature, $signature);
    }
    
    /**
     * Handle GitHub webhook
     */
    public function handle_webhook($request) {
        $event = $request->get_header('X-GitHub-Event');
        $payload = json_decode($request->get_body(), true);
        
        if ($event === 'release' && isset($payload['action']) && $payload['action'] === 'published') {
            // New release published
            $this->process_release_update($payload['release']);
            return new WP_REST_Response(array('message' => 'Update processed'), 200);
        }
        
        if ($event === 'push' && isset($payload['ref']) && $payload['ref'] === 'refs/heads/main') {
            // Push to main branch - check if we should auto-update
            $auto_update = get_option('claude_auto_update', '0');
            if ($auto_update === '1') {
                $this->check_and_update_from_github();
            }
            return new WP_REST_Response(array('message' => 'Push received'), 200);
        }
        
        return new WP_REST_Response(array('message' => 'Event ignored'), 200);
    }
    
    /**
     * Process release update
     */
    private function process_release_update($release) {
        $version = ltrim($release['tag_name'], 'v');
        $current_version = CLAUDE_SUMMARIZER_VERSION;
        
        if (version_compare($version, $current_version, '>')) {
            // New version available
            update_option('claude_update_available', '1');
            update_option('claude_update_version', $version);
            update_option('claude_update_download_url', $this->get_release_download_url($release));
            
            // Send admin notice
            set_transient('claude_update_notice', array(
                'version' => $version,
                'message' => sprintf(__('New version %s available!', 'claude-ai-summarizer'), $version)
            ), 3600);
        }
    }
    
    /**
     * Get release download URL
     */
    private function get_release_download_url($release) {
        if (isset($release['assets']) && is_array($release['assets'])) {
            foreach ($release['assets'] as $asset) {
                if (strpos($asset['content_type'], 'zip') !== false || 
                    strpos($asset['name'], '.zip') !== false) {
                    return $asset['browser_download_url'];
                }
            }
        }
        return '';
    }
    
    /**
     * Check for updates from GitHub
     */
    public function check_for_updates() {
        $auto_update = get_option('claude_auto_update', '0');
        if ($auto_update !== '1') {
            return;
        }
        
        $github_repo = get_option('claude_github_repo', '');
        if (!$github_repo) {
            return;
        }
        
        // Check once every 2 hours (unless webhook triggered)
        $last_check = get_option('claude_last_update_check', 0);
        if ((time() - $last_check) < 7200) { // Check at most once per 2 hours
            return;
        }
        
        $this->check_and_update_from_github();
    }
    
    /**
     * Check and update from GitHub
     */
    private function check_and_update_from_github() {
        $github_repo = get_option('claude_github_repo', '');
        if (!$github_repo) {
            return;
        }
        
        // Parse repo name (username/repo)
        $repo_parts = explode('/', $github_repo);
        if (count($repo_parts) !== 2) {
            return;
        }
        
        $username = $repo_parts[0];
        $repo = $repo_parts[1];
        
        // Check GitHub API for latest release
        $url = sprintf('https://api.github.com/repos/%s/%s/releases/latest', $username, $repo);
        
        $response = wp_remote_get($url, array(
            'timeout' => 10,
            'headers' => array(
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'WordPress/Claude-AI-Summarizer'
            )
        ));
        
        if (is_wp_error($response)) {
            return;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (!isset($data['tag_name'])) {
            return;
        }
        
        $latest_version = ltrim($data['tag_name'], 'v');
        $current_version = CLAUDE_SUMMARIZER_VERSION;
        
        update_option('claude_last_update_check', time());
        
        if (version_compare($latest_version, $current_version, '>')) {
            // New version available
            $download_url = $this->get_release_download_url($data);
            
            update_option('claude_update_available', '1');
            update_option('claude_update_version', $latest_version);
            update_option('claude_update_download_url', $download_url);
            
            // Auto-install if enabled
            $auto_install = get_option('claude_auto_install', '0');
            if ($auto_install === '1' && !empty($download_url)) {
                $this->install_update($download_url, $latest_version);
            }
        } else {
            update_option('claude_update_available', '0');
        }
    }
    
    /**
     * Install update from URL
     */
    private function install_update($download_url, $version) {
        if (empty($download_url)) {
            return false;
        }
        
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        
        // Download the update
        $temp_file = download_url($download_url);
        
        if (is_wp_error($temp_file)) {
            return false;
        }
        
        // Create upgrader
        $upgrader = new Plugin_Upgrader();
        
        // Install the update
        $result = $upgrader->install($temp_file, array(
            'clear_destination' => true,
            'clear_working' => true
        ));
        
        // Clean up temp file
        @unlink($temp_file);
        
        if ($result) {
            update_option('claude_update_available', '0');
            update_option('claude_last_update_version', $version);
            
            // Activate plugin if needed
            if (!is_plugin_active(plugin_basename(__FILE__))) {
                activate_plugin(plugin_basename(__FILE__));
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * AJAX handler for manual update check
     */
    public function ajax_check_update_manual() {
        check_ajax_referer('claude_check_update', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('You do not have permission', 'claude-ai-summarizer')));
            return;
        }
        
        $this->check_and_update_from_github();
        
        wp_send_json_success(array('message' => __('Update check completed', 'claude-ai-summarizer')));
    }
    
    /**
     * AJAX handler for installing update
     */
    public function ajax_install_update() {
        check_ajax_referer('claude_install_update', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('You do not have permission', 'claude-ai-summarizer')));
            return;
        }
        
        $download_url = get_option('claude_update_download_url', '');
        $version = get_option('claude_update_version', '');
        
        if (empty($download_url)) {
            wp_send_json_error(array('message' => __('No update available', 'claude-ai-summarizer')));
            return;
        }
        
        $result = $this->install_update($download_url, $version);
        
        if ($result) {
            wp_send_json_success(array('message' => __('Update installed successfully', 'claude-ai-summarizer')));
        } else {
            wp_send_json_error(array('message' => __('Failed to install update', 'claude-ai-summarizer')));
        }
    }
    
    /**
     * AJAX handler for removing icon
     */
    public function ajax_remove_icon() {
        check_ajax_referer('claude_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('You do not have permission', 'claude-ai-summarizer')));
            return;
        }
        
        delete_option('claude_button_icon');
        wp_send_json_success(array('message' => __('Icon removed', 'claude-ai-summarizer')));
    }
    
    /**
     * AJAX handler for uploading icon
     */
    public function ajax_upload_icon() {
        check_ajax_referer('claude_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('You do not have permission', 'claude-ai-summarizer')));
            return;
        }
        
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        
        if (empty($_FILES['icon_file'])) {
            wp_send_json_error(array('message' => __('No file uploaded', 'claude-ai-summarizer')));
            return;
        }
        
        $uploadedfile = $_FILES['icon_file'];
        $upload_overrides = array(
            'test_form' => false,
            'mimes' => array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'gif' => 'image/gif',
                'png' => 'image/png',
                'svg' => 'image/svg+xml',
                'webp' => 'image/webp'
            )
        );
        
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
        
        if ($movefile && !isset($movefile['error'])) {
            update_option('claude_button_icon', $movefile['url']);
            wp_send_json_success(array('message' => __('Icon uploaded successfully', 'claude-ai-summarizer'), 'url' => $movefile['url']));
        } else {
            $error = isset($movefile['error']) ? $movefile['error'] : __('Unknown error', 'claude-ai-summarizer');
            wp_send_json_error(array('message' => $error));
        }
    }
}

// Initialize plugin
Claude_AI_Summarizer::get_instance();
