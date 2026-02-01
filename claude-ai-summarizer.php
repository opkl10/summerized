<?php
/**
 * Plugin Name: Claude AI Summarizer
 * Plugin URI: https://github.com/YOUR_USERNAME/claude-ai-summarizer
 * Description: סיכום פוסטים ומאמרים חכם באמצעות Claude AI. מוסיף כפתור סיכום אוטומטי לכל פוסט.
 * Version: 3.0.0
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
define('CLAUDE_SUMMARIZER_VERSION', '3.0.0');
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
        register_setting('claude_summarizer_settings', 'claude_panel_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#667eea'
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
        register_setting('claude_summarizer_settings', 'claude_github_token', array(
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
        $panel_color = get_option('claude_panel_color', '#667eea');
        $button_icon = get_option('claude_button_icon', '');
        $button_text = get_option('claude_button_text', 'סכם עם AI');
        $show_icon = get_option('claude_show_icon', '1');
        $summary_length = get_option('claude_summary_length', 'medium');
        
        wp_localize_script('claude-frontend-script', 'claudeFrontend', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('claude_summarize_nonce'),
            'postId' => get_the_ID(),
            'buttonText' => $button_text ?: __('🤖 Summarize with AI', 'claude-ai-summarizer'),
            'loadingText' => __('Summarizing...', 'claude-ai-summarizer'),
            'position' => get_option('claude_button_position', 'bottom-left'),
            'buttonColor' => $button_color,
            'panelColor' => $panel_color,
            'buttonIcon' => $button_icon,
            'showIcon' => $show_icon === '1',
            'summaryLength' => $summary_length,
            'i18n' => array(
                'error' => __('Error occurred. Please try again.', 'claude-ai-summarizer'),
                'copied' => __('Copied!', 'claude-ai-summarizer'),
                'copy' => __('Copy', 'claude-ai-summarizer'),
                'close' => __('Close', 'claude-ai-summarizer')
            )
        ));
        
        // Add inline CSS for button and panel colors
        $custom_css = "";
        if ($button_color) {
            $button_darker = $this->adjust_brightness($button_color, -20);
            $custom_css .= "
                .claude-btn {
                    background: linear-gradient(135deg, {$button_color} 0%, {$button_darker} 100%) !important;
                    background-image: linear-gradient(135deg, {$button_color} 0%, {$button_darker} 100%) !important;
                }
            ";
        }
        if ($panel_color) {
            $panel_darker = $this->adjust_brightness($panel_color, -30);
            $custom_css .= "
                .claude-panel-header {
                    background: linear-gradient(135deg, {$panel_color} 0%, {$panel_darker} 100%) !important;
                    background-image: linear-gradient(135deg, {$panel_color} 0%, {$panel_darker} 100%) !important;
                }
            ";
        }
        if ($custom_css) {
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
            'short' => 'סכם בקצרה ב-2-3 נקודות עיקריות בלבד. חשוב מאוד: כל נקודה חייבת להיות בשורה נפרדת, כל שורה מתחילה עם מקף (-) או בולט (•), ואחריו רווח. כל נקודה צריכה להיות משפט קצר אחד. דוגמה לפורמט:\n- נקודה ראשונה\n- נקודה שנייה\n- נקודה שלישית\n\nאל תכתוב פסקה אחת - רק נקודות נפרדות!',
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
            )),
            'sslverify' => true
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        $status_code = wp_remote_retrieve_response_code($response);
        
        if ($status_code !== 200) {
            $error_message = 'Unknown error from Claude API';
            
            // Log full response for debugging
            error_log('Claude AI Summarizer API Response Code: ' . $status_code);
            error_log('Claude AI Summarizer API Response Body: ' . $body);
            error_log('Claude AI Summarizer API Model Used: ' . $model);
            
            if (isset($data['error'])) {
                if (isset($data['error']['message'])) {
                    $error_message = $data['error']['message'];
                } elseif (isset($data['error']['type'])) {
                    $error_message = $data['error']['type'];
                } elseif (is_string($data['error'])) {
                    $error_message = $data['error'];
                }
            }
            
            // Check for specific error types
            if (isset($data['error']['type'])) {
                $error_type = $data['error']['type'];
                if ($error_type === 'invalid_request_error' && isset($data['error']['message'])) {
                    // Model might not be available or API key issue
                    if (strpos($data['error']['message'], 'model') !== false || strpos($data['error']['message'], $model) !== false) {
                        $error_message = sprintf(
                            __('המודל %s לא זמין או לא תקין. נסה מודל אחר או בדוק את ה-API Key שלך ב-Anthropic Console.', 'claude-ai-summarizer'),
                            $model
                        );
                    } else {
                        $error_message = $data['error']['message'];
                    }
                }
            }
            
            // Add model info to error message if available
            if ($model && strpos($error_message, $model) === false) {
                $error_message = sprintf(__('Model: %s. Error: %s', 'claude-ai-summarizer'), $model, $error_message);
            }
            
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
        if (isset($_POST['claude_github_token'])) {
            update_option('claude_github_token', sanitize_text_field($_POST['claude_github_token']));
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
        if (!isset($release['assets']) || !is_array($release['assets']) || empty($release['assets'])) {
            // Log for debugging
            error_log('Claude AI Summarizer: No assets found in release. Release tag: ' . (isset($release['tag_name']) ? $release['tag_name'] : 'unknown'));
            return '';
        }
        
        // First, try to find a ZIP file with "claude" in the name
        foreach ($release['assets'] as $asset) {
            if (isset($asset['browser_download_url']) && isset($asset['name'])) {
                $name = strtolower($asset['name']);
                if (strpos($name, '.zip') !== false && 
                    (strpos($name, 'claude') !== false || strpos($name, 'summarizer') !== false)) {
                    return $asset['browser_download_url'];
                }
            }
        }
        
        // If no specific match, return first ZIP file
        foreach ($release['assets'] as $asset) {
            if (isset($asset['browser_download_url'])) {
                $content_type = isset($asset['content_type']) ? strtolower($asset['content_type']) : '';
                $name = isset($asset['name']) ? strtolower($asset['name']) : '';
                if (strpos($content_type, 'zip') !== false || strpos($name, '.zip') !== false) {
                    return $asset['browser_download_url'];
                }
            }
        }
        
        // Log for debugging
        error_log('Claude AI Summarizer: No ZIP file found in release assets. Available assets: ' . print_r(array_map(function($a) { return isset($a['name']) ? $a['name'] : 'unknown'; }, $release['assets']), true));
        return '';
    }
    
    /**
     * Check for updates from GitHub
     */
    public function check_for_updates() {
        $github_repo = get_option('claude_github_repo', '');
        if (!$github_repo) {
            error_log('Claude AI Summarizer: GitHub repository not configured for update check');
            return;
        }
        
        // Check once every 2 hours (unless webhook triggered)
        $last_check = get_option('claude_last_update_check', 0);
        if ((time() - $last_check) < 7200) { // Check at most once per 2 hours
            error_log('Claude AI Summarizer: Update check skipped (last check was less than 2 hours ago)');
            return;
        }
        
        error_log('Claude AI Summarizer: Starting scheduled update check');
        $this->check_and_update_from_github();
    }
    
    /**
     * Check and update from GitHub
     */
    private function check_and_update_from_github() {
        $github_repo = get_option('claude_github_repo', '');
        if (!$github_repo) {
            update_option('claude_update_error', __('GitHub Repository not configured', 'claude-ai-summarizer'));
            return;
        }
        
        // Parse repo name (username/repo)
        $repo_parts = explode('/', $github_repo);
        if (count($repo_parts) !== 2) {
            update_option('claude_update_error', __('Invalid repository format. Use: username/repo-name', 'claude-ai-summarizer'));
            return;
        }
        
        $username = trim($repo_parts[0]);
        $repo = trim($repo_parts[1]);
        
        // Check GitHub API for latest release
        $url = sprintf('https://api.github.com/repos/%s/%s/releases/latest', $username, $repo);
        
        // Prepare headers
        $headers = array(
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'WordPress/Claude-AI-Summarizer'
        );
        
        // Add GitHub token if available (for higher rate limit)
        $github_token = get_option('claude_github_token', '');
        if (!empty($github_token)) {
            $headers['Authorization'] = 'token ' . $github_token;
        }
        
        $response = wp_remote_get($url, array(
            'timeout' => 15,
            'headers' => $headers
        ));
        
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            update_option('claude_update_error', sprintf(__('Error connecting to GitHub: %s', 'claude-ai-summarizer'), $error_message));
            return;
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            $response_body = wp_remote_retrieve_body($response);
            $response_data = json_decode($response_body, true);
            $api_message = isset($response_data['message']) ? $response_data['message'] : '';
            
            $error_message = sprintf(__('GitHub API returned error code: %d', 'claude-ai-summarizer'), $response_code);
            if ($response_code === 404) {
                // Check if it's repository not found or no releases
                if (strpos($api_message, 'Not Found') !== false) {
                    // First check if repository exists
                    $repo_check_url = sprintf('https://api.github.com/repos/%s/%s', $username, $repo);
                    $repo_check_response = wp_remote_get($repo_check_url, array(
                        'timeout' => 10,
                        'headers' => $headers
                    ));
                    
                    $repo_check_code = wp_remote_retrieve_response_code($repo_check_response);
                    if ($repo_check_code === 200) {
                        // Repository exists but no releases
                        $error_message = sprintf(
                            __('Repository %s/%s exists but has no releases. Create a release on GitHub first.', 'claude-ai-summarizer'),
                            $username,
                            $repo
                        );
                    } else {
                        // Repository doesn't exist
                        $error_message = sprintf(
                            __('Repository not found: %s/%s. Check the repository name in settings. Make sure it exists and is public.', 'claude-ai-summarizer'),
                            $username,
                            $repo
                        );
                    }
                } else {
                    $error_message = sprintf(
                        __('No releases found in repository %s/%s. Create a release on GitHub first.', 'claude-ai-summarizer'),
                        $username,
                        $repo
                    );
                }
            } elseif ($response_code === 403) {
                // Check rate limit headers
                $rate_limit_remaining = wp_remote_retrieve_header($response, 'x-ratelimit-remaining');
                $rate_limit_reset = wp_remote_retrieve_header($response, 'x-ratelimit-reset');
                
                if ($rate_limit_remaining === '0' && $rate_limit_reset) {
                    $reset_time = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $rate_limit_reset);
                    $error_message = sprintf(
                        __('GitHub API rate limit exceeded. Limit resets at %s. Add a GitHub Personal Access Token in settings to increase the limit.', 'claude-ai-summarizer'),
                        $reset_time
                    );
                } else {
                    $error_message = __('GitHub API rate limit exceeded or access denied. Add a GitHub Personal Access Token in settings to increase the limit.', 'claude-ai-summarizer');
                }
            }
            update_option('claude_update_error', $error_message);
            return;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (!isset($data['tag_name'])) {
            update_option('claude_update_error', __('No releases found in repository', 'claude-ai-summarizer'));
            update_option('claude_update_available', '0');
            return;
        }
        
        // Clear any previous errors
        delete_option('claude_update_error');
        
        $latest_version = ltrim($data['tag_name'], 'v');
        $current_version = CLAUDE_SUMMARIZER_VERSION;
        
        // Normalize versions (remove any non-numeric characters except dots)
        $latest_version_clean = preg_replace('/[^0-9.]/', '', $latest_version);
        $current_version_clean = preg_replace('/[^0-9.]/', '', $current_version);
        
        // Log for debugging
        error_log('Claude AI Summarizer: Version check - Latest: ' . $latest_version . ' (' . $latest_version_clean . '), Current: ' . $current_version . ' (' . $current_version_clean . ')');
        
        // Compare versions
        $version_comparison = version_compare($latest_version_clean, $current_version_clean);
        
        // Log comparison result
        error_log('Claude AI Summarizer: Version comparison result: ' . $version_comparison . ' (1=newer, 0=same, -1=older)');
        
        update_option('claude_last_update_check', time());
        update_option('claude_last_check_result', array(
            'latest_version' => $latest_version,
            'latest_version_clean' => $latest_version_clean,
            'current_version' => $current_version,
            'current_version_clean' => $current_version_clean,
            'check_time' => current_time('mysql'),
            'version_comparison' => $version_comparison > 0 ? 'newer' : ($version_comparison < 0 ? 'older' : 'same'),
            'comparison_result' => $version_comparison
        ));
        
        if ($version_comparison > 0) {
            // New version available
            $download_url = $this->get_release_download_url($data);
            
            if (empty($download_url)) {
                update_option('claude_update_error', __('Release found but no ZIP file available. Make sure the release includes a ZIP file.', 'claude-ai-summarizer'));
                update_option('claude_update_available', '0');
                return;
            }
            
            update_option('claude_update_available', '1');
            update_option('claude_update_version', $latest_version);
            update_option('claude_update_download_url', $download_url);
            
            // Auto-install if enabled
            $auto_install = get_option('claude_auto_install', '0');
            if ($auto_install === '1' && !empty($download_url)) {
                $this->install_update($download_url, $latest_version);
            }
        } elseif ($version_comparison === 0) {
            // Same version
            update_option('claude_update_available', '0');
            delete_option('claude_update_error');
        } else {
            // Current version is newer (shouldn't happen, but handle it)
            update_option('claude_update_available', '0');
            update_option('claude_update_error', sprintf(
                __('Current version (%s) is newer than GitHub release (%s).', 'claude-ai-summarizer'),
                $current_version,
                $latest_version
            ));
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
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        
        // Get plugin file path
        $plugin_file = plugin_basename(__FILE__);
        $plugin_dir = dirname($plugin_file);
        $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_dir;
        
        // Download the update
        $temp_file = download_url($download_url, 300);
        
        if (is_wp_error($temp_file)) {
            error_log('Claude AI Summarizer: Failed to download update. Error: ' . $temp_file->get_error_message());
            return false;
        }
        
        // Create temporary extraction directory
        $temp_dir = get_temp_dir();
        $extract_to = $temp_dir . 'claude-update-' . uniqid();
        
        // Create the extraction directory
        if (!wp_mkdir_p($extract_to)) {
            error_log('Claude AI Summarizer: Failed to create extraction directory: ' . $extract_to);
            @unlink($temp_file);
            return false;
        }
        
        // Extract ZIP file
        if (!class_exists('ZipArchive')) {
            error_log('Claude AI Summarizer: ZipArchive class not available');
            @unlink($temp_file);
            return false;
        }
        
        $zip = new ZipArchive();
        $zip_result = $zip->open($temp_file);
        
        if ($zip_result !== true) {
            error_log('Claude AI Summarizer: Failed to open ZIP file. Error code: ' . $zip_result);
            @unlink($temp_file);
            return false;
        }
        
        // Extract to temporary directory
        if (!$zip->extractTo($extract_to)) {
            error_log('Claude AI Summarizer: Failed to extract ZIP file');
            $zip->close();
            @unlink($temp_file);
            return false;
        }
        
        $zip->close();
        
        // Find the plugin directory in extracted files
        $extracted_plugin_dir = '';
        $files = scandir($extract_to);
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $full_path = $extract_to . '/' . $file;
            if (is_dir($full_path) && $file === $plugin_dir) {
                $extracted_plugin_dir = $full_path;
                break;
            } elseif (is_dir($full_path)) {
                // Check if plugin file is inside this directory
                if (file_exists($full_path . '/' . basename($plugin_file))) {
                    $extracted_plugin_dir = $full_path;
                    break;
                }
            }
        }
        
        // If not found, check if files are directly in extract_to
        if (empty($extracted_plugin_dir) && file_exists($extract_to . '/' . basename($plugin_file))) {
            $extracted_plugin_dir = $extract_to;
        }
        
        if (empty($extracted_plugin_dir)) {
            error_log('Claude AI Summarizer: Plugin directory not found in extracted files');
            $this->delete_directory($extract_to);
            @unlink($temp_file);
            return false;
        }
        
        // Copy files from extracted directory to plugin directory
        error_log('Claude AI Summarizer: Copying files from ' . $extracted_plugin_dir . ' to ' . $plugin_path);
        $copy_result = $this->copy_directory($extracted_plugin_dir, $plugin_path);
        
        if (!$copy_result) {
            error_log('Claude AI Summarizer: Failed to copy files to plugin directory');
            $this->delete_directory($extract_to);
            @unlink($temp_file);
            return false;
        }
        
        // Verify main plugin file was copied
        $main_plugin_file = $plugin_path . '/' . basename($plugin_file);
        if (!file_exists($main_plugin_file)) {
            error_log('Claude AI Summarizer: Main plugin file not found after copy: ' . $main_plugin_file);
            $this->delete_directory($extract_to);
            @unlink($temp_file);
            return false;
        }
        
        // Verify version was updated in the copied file
        $plugin_data = get_file_data($main_plugin_file, array('Version' => 'Version'), 'plugin');
        $new_version = !empty($plugin_data['Version']) ? $plugin_data['Version'] : '';
        
        if (empty($new_version)) {
            error_log('Claude AI Summarizer: Could not read version from updated file');
            // Try to read from define
            $file_content = file_get_contents($main_plugin_file);
            if (preg_match("/define\s*\(\s*['\"]CLAUDE_SUMMARIZER_VERSION['\"]\s*,\s*['\"]([^'\"]+)['\"]\s*\)/", $file_content, $matches)) {
                $new_version = $matches[1];
            }
        }
        
        if (empty($new_version)) {
            error_log('Claude AI Summarizer: Version not found in updated file, using provided version: ' . $version);
            $new_version = $version;
        }
        
        // Verify the version actually changed
        $current_version = CLAUDE_SUMMARIZER_VERSION;
        if ($new_version === $current_version) {
            error_log('Claude AI Summarizer: WARNING - Version did not change after update! Current: ' . $current_version . ', New: ' . $new_version);
            // Still continue, but log the warning
        } else {
            error_log('Claude AI Summarizer: Version updated from ' . $current_version . ' to ' . $new_version);
        }
        
        // Clean up
        $this->delete_directory($extract_to);
        @unlink($temp_file);
        
        // Verify plugin file exists
        if (!file_exists(WP_PLUGIN_DIR . '/' . $plugin_file)) {
            error_log('Claude AI Summarizer: Plugin file not found after upgrade');
            return false;
        }
        
        // Mark as updated
        update_option('claude_update_available', '0');
        update_option('claude_last_update_version', $new_version);
        delete_option('claude_update_download_url');
        delete_option('claude_update_error');
        
        // Clear last check to force fresh check next time
        delete_option('claude_last_update_check');
        
        // Activate plugin if needed
        if (!is_plugin_active($plugin_file)) {
            activate_plugin($plugin_file);
        }
        
        // Clear any caches
        wp_cache_flush();
        
        // Force WordPress to reload plugin data
        delete_transient('claude_plugin_data');
        
        // Log success
        error_log('Claude AI Summarizer: Successfully upgraded to version ' . $new_version);
        error_log('Claude AI Summarizer: Plugin file path: ' . WP_PLUGIN_DIR . '/' . $plugin_file);
        error_log('Claude AI Summarizer: Plugin file exists: ' . (file_exists(WP_PLUGIN_DIR . '/' . $plugin_file) ? 'Yes' : 'No'));
        
        return true;
    }
    
    /**
     * Copy directory recursively
     */
    private function copy_directory($source, $destination) {
        if (!is_dir($source)) {
            error_log('Claude AI Summarizer: Source directory does not exist: ' . $source);
            return false;
        }
        
        if (!is_dir($destination)) {
            if (!wp_mkdir_p($destination)) {
                error_log('Claude AI Summarizer: Failed to create destination directory: ' . $destination);
                return false;
            }
        }
        
        $dir = opendir($source);
        if (!$dir) {
            error_log('Claude AI Summarizer: Failed to open source directory: ' . $source);
            return false;
        }
        
        $success = true;
        $copied_files = 0;
        $failed_files = 0;
        
        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $source_file = $source . '/' . $file;
            $dest_file = $destination . '/' . $file;
            
            if (is_dir($source_file)) {
                if (!$this->copy_directory($source_file, $dest_file)) {
                    $success = false;
                    $failed_files++;
                } else {
                    $copied_files++;
                }
            } else {
                // Check if destination file exists and is writable
                if (file_exists($dest_file) && !is_writable($dest_file)) {
                    // Try to make it writable
                    @chmod($dest_file, 0644);
                }
                
                // Copy the file
                $copy_result = @copy($source_file, $dest_file);
                
                if (!$copy_result) {
                    error_log('Claude AI Summarizer: Failed to copy file: ' . $source_file . ' to ' . $dest_file);
                    $success = false;
                    $failed_files++;
                } else {
                    // Verify the file was copied
                    if (!file_exists($dest_file)) {
                        error_log('Claude AI Summarizer: File copy reported success but file does not exist: ' . $dest_file);
                        $success = false;
                        $failed_files++;
                    } else {
                        // Verify file sizes match
                        $source_size = filesize($source_file);
                        $dest_size = filesize($dest_file);
                        if ($source_size !== $dest_size) {
                            error_log('Claude AI Summarizer: File sizes do not match after copy. Source: ' . $source_size . ', Dest: ' . $dest_size);
                            $success = false;
                            $failed_files++;
                        } else {
                            $copied_files++;
                            // Set proper permissions
                            @chmod($dest_file, 0644);
                        }
                    }
                }
            }
        }
        
        closedir($dir);
        
        error_log('Claude AI Summarizer: Copy completed. Files copied: ' . $copied_files . ', Failed: ' . $failed_files);
        
        return $success;
    }
    
    /**
     * Delete directory recursively
     */
    private function delete_directory($dir) {
        if (!is_dir($dir)) {
            return false;
        }
        
        $files = array_diff(scandir($dir), array('.', '..'));
        
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->delete_directory($path);
            } else {
                @unlink($path);
            }
        }
        
        return @rmdir($dir);
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
        
        // Force check (ignore time limit)
        $github_repo = get_option('claude_github_repo', '');
        if (!$github_repo) {
            wp_send_json_error(array('message' => __('GitHub Repository not configured', 'claude-ai-summarizer')));
            return;
        }
        
        // Clear last check time to force immediate check
        delete_option('claude_last_update_check');
        
        // Also clear any cached update status to force fresh check
        delete_option('claude_update_available');
        delete_option('claude_update_version');
        delete_option('claude_update_download_url');
        delete_option('claude_update_error');
        
        // Perform check
        $this->check_and_update_from_github();
        
        // Get results
        $update_available = get_option('claude_update_available', '0');
        $update_version = get_option('claude_update_version', '');
        $update_error = get_option('claude_update_error', '');
        $last_check_result = get_option('claude_last_check_result', array());
        
        $message = __('Update check completed', 'claude-ai-summarizer');
        if ($update_error) {
            $message .= ': ' . $update_error;
        } elseif ($update_available === '1' && $update_version) {
            $message = sprintf(__('New version %s available!', 'claude-ai-summarizer'), $update_version);
        } elseif (!empty($last_check_result) && isset($last_check_result['latest_version'])) {
            $current_version = CLAUDE_SUMMARIZER_VERSION;
            $latest_version = $last_check_result['latest_version'];
            
            if (isset($last_check_result['version_comparison'])) {
                if ($last_check_result['version_comparison'] === 'same') {
                    $message = sprintf(__('Current version (%s) is the same as the latest release (%s)', 'claude-ai-summarizer'), $current_version, $latest_version);
                } elseif ($last_check_result['version_comparison'] === 'older') {
                    $message = sprintf(__('Current version (%s) is newer than GitHub release (%s)', 'claude-ai-summarizer'), $current_version, $latest_version);
                } else {
                    $message = sprintf(__('Latest GitHub release: %s (Current: %s)', 'claude-ai-summarizer'), $latest_version, $current_version);
                }
            } else {
                $message = sprintf(__('Latest GitHub release: %s (Current: %s). No update needed.', 'claude-ai-summarizer'), $latest_version, $current_version);
            }
        } else {
            $current_version = CLAUDE_SUMMARIZER_VERSION;
            $latest_version = $last_check_result['latest_version'] ?? $current_version;
            if (version_compare($latest_version, $current_version, '>')) {
                $message = sprintf(__('Latest version is %s, but download URL not found', 'claude-ai-summarizer'), $latest_version);
            } else {
                $message = __('You are using the latest version', 'claude-ai-summarizer');
            }
        }
        
        wp_send_json_success(array(
            'message' => $message,
            'update_available' => $update_available === '1',
            'update_version' => $update_version,
            'current_version' => CLAUDE_SUMMARIZER_VERSION,
            'latest_version' => !empty($last_check_result) && isset($last_check_result['latest_version']) ? $last_check_result['latest_version'] : '',
            'version_comparison' => !empty($last_check_result) && isset($last_check_result['version_comparison']) ? $last_check_result['version_comparison'] : '',
            'error' => $update_error,
            'details' => $last_check_result
        ));
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
        
        // Increase time limit for large downloads
        @set_time_limit(300);
        
        // Disable output buffering
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Store error handler to catch any errors
        $error_handler = function($errno, $errstr, $errfile, $errline) {
            error_log("Claude AI Summarizer Update Error: [$errno] $errstr in $errfile on line $errline");
        };
        set_error_handler($error_handler);
        
        try {
            $result = $this->install_update($download_url, $version);
            
            restore_error_handler();
            
            if ($result) {
                // Reload plugin data to get new version
                $plugin_file = plugin_basename(__FILE__);
                $plugin_data = get_file_data(WP_PLUGIN_DIR . '/' . $plugin_file, array('Version' => 'Version'), 'plugin');
                $installed_version = !empty($plugin_data['Version']) ? $plugin_data['Version'] : $version;
                
                wp_send_json_success(array(
                    'message' => sprintf(__('Update installed successfully! Version %s is now active. Please refresh the page to see the changes.', 'claude-ai-summarizer'), $installed_version),
                    'version' => $installed_version,
                    'reload' => true
                ));
            } else {
                $error_message = __('Failed to install update. ', 'claude-ai-summarizer');
                
                // Check if there are specific error messages in logs
                $last_error = error_get_last();
                if ($last_error && (strpos($last_error['message'], 'Claude AI Summarizer') !== false || strpos($last_error['file'], 'claude-ai-summarizer') !== false)) {
                    $error_message .= $last_error['message'];
                } else {
                    $error_message .= __('Please check WordPress debug logs for details. Common issues: insufficient file permissions, missing ZipArchive class, or network timeout.', 'claude-ai-summarizer');
                }
                
                wp_send_json_error(array('message' => $error_message));
            }
        } catch (Exception $e) {
            restore_error_handler();
            error_log('Claude AI Summarizer: Exception during update: ' . $e->getMessage());
            error_log('Claude AI Summarizer: Exception trace: ' . $e->getTraceAsString());
            wp_send_json_error(array(
                'message' => __('Update failed with exception: ', 'claude-ai-summarizer') . $e->getMessage()
            ));
        } catch (Error $e) {
            restore_error_handler();
            error_log('Claude AI Summarizer: Fatal error during update: ' . $e->getMessage());
            error_log('Claude AI Summarizer: Error trace: ' . $e->getTraceAsString());
            wp_send_json_error(array(
                'message' => __('Update failed with fatal error: ', 'claude-ai-summarizer') . $e->getMessage()
            ));
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
