<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap claude-admin-wrap">
    <h1>🤖 Claude AI Summarizer</h1>
    
    <form method="post" action="options.php">
        <?php settings_fields('claude_summarizer_settings'); ?>
        
        <div class="claude-admin-section">
            <h2>הגדרות API</h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="claude_api_key">Claude API Key</label>
                    </th>
                    <td>
                        <input type="password" 
                               id="claude_api_key" 
                               name="claude_api_key" 
                               value="<?php echo esc_attr(get_option('claude_api_key', '')); ?>" 
                               class="regular-text" 
                               required />
                        <p class="description">
                            קבל API Key מ-<a href="https://console.anthropic.com/" target="_blank">Anthropic Console</a>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="claude_model">מודל</label>
                    </th>
                    <td>
                        <select id="claude_model" name="claude_model">
                            <option value="claude-3-5-sonnet-20241022" <?php selected(get_option('claude_model', 'claude-3-5-sonnet-20241022'), 'claude-3-5-sonnet-20241022'); ?>>
                                Claude 3.5 Sonnet (מומלץ)
                            </option>
                            <option value="claude-3-opus-20240229" <?php selected(get_option('claude_model', 'claude-3-5-sonnet-20241022'), 'claude-3-opus-20240229'); ?>>
                                Claude 3 Opus
                            </option>
                            <option value="claude-3-sonnet-20240229" <?php selected(get_option('claude_model', 'claude-3-5-sonnet-20241022'), 'claude-3-sonnet-20240229'); ?>>
                                Claude 3 Sonnet
                            </option>
                            <option value="claude-3-haiku-20240307" <?php selected(get_option('claude_model', 'claude-3-5-sonnet-20241022'), 'claude-3-haiku-20240307'); ?>>
                                Claude 3 Haiku (מהיר)
                            </option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="claude_summary_length">אורך סיכום</label>
                    </th>
                    <td>
                        <select id="claude_summary_length" name="claude_summary_length">
                            <option value="short" <?php selected(get_option('claude_summary_length', 'medium'), 'short'); ?>>
                                קצר (2-3 משפטים)
                            </option>
                            <option value="medium" <?php selected(get_option('claude_summary_length', 'medium'), 'medium'); ?>>
                                בינוני (פסקה אחת)
                            </option>
                            <option value="long" <?php selected(get_option('claude_summary_length', 'medium'), 'long'); ?>>
                                ארוך (מספר פסקאות)
                            </option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="claude-admin-section">
            <h2>הגדרות כפתור</h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">הצגת כפתור אוטומטית</th>
                    <td>
                        <label>
                            <input type="checkbox" 
                                   name="claude_auto_button" 
                                   value="1" 
                                   <?php checked(get_option('claude_auto_button', '1'), '1'); ?> />
                            הצג כפתור "סכם עם AI" בפוסטים
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="claude_button_position">מיקום כפתור</label>
                    </th>
                    <td>
                        <select id="claude_button_position" name="claude_button_position">
                            <option value="bottom-left" <?php selected(get_option('claude_button_position', 'bottom-left'), 'bottom-left'); ?>>
                                פינה שמאלית תחתונה
                            </option>
                            <option value="bottom-right" <?php selected(get_option('claude_button_position', 'bottom-left'), 'bottom-right'); ?>>
                                פינה ימנית תחתונה
                            </option>
                            <option value="top-left" <?php selected(get_option('claude_button_position', 'bottom-left'), 'top-left'); ?>>
                                פינה שמאלית עליונה
                            </option>
                            <option value="top-right" <?php selected(get_option('claude_button_position', 'bottom-left'), 'top-right'); ?>>
                                פינה ימנית עליונה
                            </option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="claude_button_color">צבע כפתור</label>
                    </th>
                    <td>
                        <input type="color" 
                               id="claude_button_color" 
                               name="claude_button_color" 
                               value="<?php echo esc_attr(get_option('claude_button_color', '#667eea')); ?>" 
                               style="width: 100px; height: 40px;" />
                        <input type="text" 
                               id="claude_button_color_text" 
                               value="<?php echo esc_attr(get_option('claude_button_color', '#667eea')); ?>" 
                               style="width: 100px; margin-right: 10px;" 
                               placeholder="#667eea" />
                        <p class="description">
                            בחר צבע לכפתור הסיכום
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="claude_button_text">טקסט כפתור</label>
                    </th>
                    <td>
                        <input type="text" 
                               id="claude_button_text" 
                               name="claude_button_text" 
                               value="<?php echo esc_attr(get_option('claude_button_text', 'סכם עם AI')); ?>" 
                               class="regular-text" />
                        <p class="description">
                            הטקסט שיופיע על הכפתור
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label>אייקון כפתור</label>
                    </th>
                    <td>
                        <?php 
                        $current_icon = get_option('claude_button_icon', '');
                        if ($current_icon):
                        ?>
                            <div style="margin-bottom: 10px;">
                                <img src="<?php echo esc_url($current_icon); ?>" 
                                     alt="Current Icon" 
                                     style="max-width: 50px; max-height: 50px; border: 1px solid #ddd; padding: 5px; background: #fff;" />
                                <br>
                                <button type="button" class="button button-small" id="claude-remove-icon" style="margin-top: 5px;">
                                    הסר אייקון
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data" id="claude-icon-upload-form">
                            <?php wp_nonce_field('claude_upload_icon'); ?>
                            <input type="hidden" name="action" value="claude_upload_icon">
                            <input type="file" 
                                   name="claude_icon_file" 
                                   id="claude_icon_file" 
                                   accept="image/*" 
                                   style="margin-bottom: 10px;" />
                            <button type="submit" class="button">
                                העלה אייקון
                            </button>
                            <p class="description">
                                העלה תמונה לאייקון (PNG, JPG, SVG, WebP). מומלץ: 32x32 עד 64x64 פיקסלים
                            </p>
                        </form>
                        
                        <?php if (isset($_GET['icon_uploaded'])): ?>
                            <div class="notice notice-success inline" style="margin-top: 10px;">
                                <p>אייקון הועלה בהצלחה!</p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_GET['icon_upload_error'])): ?>
                            <div class="notice notice-error inline" style="margin-top: 10px;">
                                <p>שגיאה בהעלאת אייקון. ודא שזה קובץ תמונה תקין.</p>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="claude-admin-section">
            <h2>עדכון אוטומטי</h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="claude_github_repo">GitHub Repository</label>
                    </th>
                    <td>
                        <input type="text" 
                               id="claude_github_repo" 
                               name="claude_github_repo" 
                               value="<?php echo esc_attr(get_option('claude_github_repo', '')); ?>" 
                               class="regular-text" 
                               placeholder="username/repo-name" />
                        <p class="description">
                            שם ה-repository ב-GitHub (לעדכון אוטומטי)
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">עדכון אוטומטי</th>
                    <td>
                        <label>
                            <input type="checkbox" 
                                   name="claude_auto_update" 
                                   value="1" 
                                   <?php checked(get_option('claude_auto_update', '0'), '1'); ?> />
                            אפשר עדכון אוטומטי מ-GitHub
                        </label>
                        <p class="description">
                            ה-plugin יבדוק אוטומטית לעדכונים כל שעתיים
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">התקנה אוטומטית</th>
                    <td>
                        <label>
                            <input type="checkbox" 
                                   name="claude_auto_install" 
                                   value="1" 
                                   <?php checked(get_option('claude_auto_install', '0'), '1'); ?> />
                            התקן עדכונים אוטומטית (מומלץ רק אם אתה סומך על ה-repository)
                        </label>
                        <p class="description">
                            ⚠️ זה יוריד ויתקין עדכונים אוטומטית ללא אישור
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="claude_webhook_secret">Webhook Secret</label>
                    </th>
                    <td>
                        <input type="text" 
                               id="claude_webhook_secret" 
                               name="claude_webhook_secret" 
                               value="<?php echo esc_attr(get_option('claude_webhook_secret', '')); ?>" 
                               class="regular-text" 
                               placeholder="הכנס secret מ-GitHub" />
                        <p class="description">
                            Secret מ-GitHub Webhook (אופציונלי, לאבטחה)
                            <br>
                            <strong>Webhook URL:</strong> 
                            <code><?php echo esc_url(rest_url('claude/v1/webhook')); ?></code>
                            <button type="button" class="button button-small" onclick="navigator.clipboard.writeText('<?php echo esc_js(rest_url('claude/v1/webhook')); ?>')">
                                העתק
                            </button>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="claude-admin-section">
            <h2>סטטוס עדכון</h2>
            
            <?php
            $update_available = get_option('claude_update_available', '0');
            $update_version = get_option('claude_update_version', '');
            $last_check = get_option('claude_last_update_check', 0);
            ?>
            
            <table class="form-table">
                <tr>
                    <th>גרסה נוכחית</th>
                    <td><strong><?php echo esc_html(CLAUDE_SUMMARIZER_VERSION); ?></strong></td>
                </tr>
                <tr>
                    <th>עדכון זמין</th>
                    <td>
                        <?php if ($update_available === '1' && $update_version): ?>
                            <span style="color: #46b450;">✓ גרסה חדשה זמינה: <?php echo esc_html($update_version); ?></span>
                            <br>
                            <button type="button" class="button button-primary" id="claude-install-update">
                                התקן עדכון עכשיו
                            </button>
                        <?php else: ?>
                            <span style="color: #666;">אין עדכונים זמינים</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>בדיקה אחרונה</th>
                    <td>
                        <?php echo $last_check ? date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $last_check) : __('Never', 'claude-ai-summarizer'); ?>
                        <br>
                        <button type="button" class="button button-secondary" id="claude-check-update-now">
                            בדוק עכשיו
                        </button>
                    </td>
                </tr>
            </table>
        </div>
        
        <?php submit_button(); ?>
    </form>
    
    <div class="claude-admin-section">
        <h2>שימוש</h2>
        
        <h3>Shortcode</h3>
        <p>השתמש ב-shortcode להצגת סיכום:</p>
        <code>[claude_summary]</code>
        <p>או עם פרמטרים:</p>
        <code>[claude_summary post_id="123" length="long"]</code>
        
        <h3>Gutenberg Block</h3>
        <p>חפש "Claude Summary" ב-Gutenberg editor והוסף את ה-block.</p>
        
        <h3>כפתור אוטומטי</h3>
        <p>אם הפעלת "הצגת כפתור אוטומטית", הכפתור יופיע אוטומטית בכל פוסט.</p>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Check for updates
    $('#claude-check-update-now').on('click', function() {
        var $button = $(this);
        var originalText = $button.text();
        
        $button.prop('disabled', true).text('בודק...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'claude_check_update_manual',
                nonce: '<?php echo wp_create_nonce('claude_check_update'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert('בדיקה הושלמה. רענן את הדף לראות תוצאות.');
                    location.reload();
                } else {
                    alert('שגיאה: ' + (response.data.message || 'שגיאה לא ידועה'));
                }
            },
            error: function() {
                alert('שגיאה בבדיקת עדכונים');
            },
            complete: function() {
                $button.prop('disabled', false).text(originalText);
            }
        });
    });
    
    // Install update
    $('#claude-install-update').on('click', function() {
        if (!confirm('האם אתה בטוח שברצונך להתקין את העדכון? זה יעדכן את ה-plugin אוטומטית.')) {
            return;
        }
        
        var $button = $(this);
        var originalText = $button.text();
        
        $button.prop('disabled', true).text('מתקין...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'claude_install_update',
                nonce: '<?php echo wp_create_nonce('claude_install_update'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert('העדכון הותקן בהצלחה! הדף ירענן בעוד 3 שניות...');
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                } else {
                    alert('שגיאה: ' + (response.data.message || 'שגיאה לא ידועה'));
                    $button.prop('disabled', false).text(originalText);
                }
            },
            error: function() {
                alert('שגיאה בהתקנת עדכון');
                $button.prop('disabled', false).text(originalText);
            }
        });
    });
});
</script>
