<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap claude-admin-wrap">
    <h1>🤖 Claude AI Summarizer</h1>
    
    <?php
    // Show success message
    if (isset($_GET['settings-updated'])) {
        echo '<div class="notice notice-success is-dismissible"><p>ההגדרות נשמרו בהצלחה!</p></div>';
    }
    ?>
    
    <!-- Welcome Section -->
    <div class="claude-welcome-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px; margin: 20px 0; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
        <h2 style="margin-top: 0; color: white; font-size: 28px; margin-bottom: 15px;">
            👋 ברוכים הבאים ל-Claude AI Summarizer!
        </h2>
        <p style="font-size: 16px; line-height: 1.8; margin-bottom: 20px; color: rgba(255, 255, 255, 0.95);">
            הפלאגין החכם שלך לסיכום אוטומטי של פוסטים ומאמרים באמצעות Claude AI. 
            פשוט הגדר את ה-API Key שלך והתחל להשתמש - הכפתור יופיע אוטומטית בכל פוסט!
        </p>
        <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 20px;">
            <div style="flex: 1; min-width: 200px; background: rgba(255, 255, 255, 0.15); padding: 15px; border-radius: 8px; backdrop-filter: blur(10px);">
                <strong style="display: block; margin-bottom: 8px; font-size: 18px;">⚡ מהיר וקל</strong>
                <span style="font-size: 14px; opacity: 0.9;">סיכום אוטומטי בלחיצה אחת</span>
            </div>
            <div style="flex: 1; min-width: 200px; background: rgba(255, 255, 255, 0.15); padding: 15px; border-radius: 8px; backdrop-filter: blur(10px);">
                <strong style="display: block; margin-bottom: 8px; font-size: 18px;">🎨 מותאם אישית</strong>
                <span style="font-size: 14px; opacity: 0.9;">צבעים, טקסט ומיקום</span>
            </div>
            <div style="flex: 1; min-width: 200px; background: rgba(255, 255, 255, 0.15); padding: 15px; border-radius: 8px; backdrop-filter: blur(10px);">
                <strong style="display: block; margin-bottom: 8px; font-size: 18px;">🔄 עדכון אוטומטי</strong>
                <span style="font-size: 14px; opacity: 0.9;">מתעדכן מ-GitHub אוטומטית</span>
            </div>
        </div>
        <p style="margin-top: 20px; font-size: 14px; opacity: 0.9; border-top: 1px solid rgba(255, 255, 255, 0.2); padding-top: 15px;">
            💡 <strong>טיפ:</strong> התחל בהגדרת ה-API Key למטה, ואז התאם את המראה וההתנהגות של הכפתור לפי הטעם שלך.
        </p>
    </div>
    
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="claude_save_settings">
        <?php wp_nonce_field('claude_summarizer_settings-options'); ?>
        
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
                            <option value="claude-3-5-haiku-20241022" <?php selected(get_option('claude_model', 'claude-3-5-sonnet-20241022'), 'claude-3-5-haiku-20241022'); ?>>
                                Claude 3.5 Haiku (מהיר)
                            </option>
                            <option value="claude-3-opus-20240229" <?php selected(get_option('claude_model', 'claude-3-5-sonnet-20241022'), 'claude-3-opus-20240229'); ?>>
                                Claude 3 Opus (ייתכן ולא זמין)
                            </option>
                            <option value="claude-3-sonnet-20240229" <?php selected(get_option('claude_model', 'claude-3-5-sonnet-20241022'), 'claude-3-sonnet-20240229'); ?>>
                                Claude 3 Sonnet
                            </option>
                            <option value="claude-3-haiku-20240307" <?php selected(get_option('claude_model', 'claude-3-5-sonnet-20241022'), 'claude-3-haiku-20240307'); ?>>
                                Claude 3 Haiku
                            </option>
                        </select>
                        <p class="description">
                            ⚠️ אם אתה מקבל שגיאה עם מודל מסוים, נסה מודל אחר. Claude 3.5 Sonnet הוא המומלץ והזמין ביותר.
                        </p>
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
                            <optgroup label="בקצוות המסך (Fixed)">
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
                            </optgroup>
                            <optgroup label="בתוך הפוסט">
                                <option value="before-content" <?php selected(get_option('claude_button_position', 'bottom-left'), 'before-content'); ?>>
                                    לפני התוכן
                                </option>
                                <option value="after-content" <?php selected(get_option('claude_button_position', 'bottom-left'), 'after-content'); ?>>
                                    אחרי התוכן
                                </option>
                                <option value="inside-content-top" <?php selected(get_option('claude_button_position', 'bottom-left'), 'inside-content-top'); ?>>
                                    בתחילת התוכן
                                </option>
                                <option value="inside-content-bottom" <?php selected(get_option('claude_button_position', 'bottom-left'), 'inside-content-bottom'); ?>>
                                    בסוף התוכן
                                </option>
                            </optgroup>
                        </select>
                        <p class="description">
                            בחר היכן להציג את הכפתור. "בקצוות" = כפתור צף, "בתוך הפוסט" = כפתור בתוך התוכן
                        </p>
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
                        <label for="claude_panel_color">צבע חלונית</label>
                    </th>
                    <td>
                        <input type="color" 
                               id="claude_panel_color" 
                               name="claude_panel_color" 
                               value="<?php echo esc_attr(get_option('claude_panel_color', '#667eea')); ?>" 
                               style="width: 100px; height: 40px;" />
                        <input type="text" 
                               id="claude_panel_color_text" 
                               value="<?php echo esc_attr(get_option('claude_panel_color', '#667eea')); ?>" 
                               style="width: 100px; margin-right: 10px;" 
                               placeholder="#667eea" />
                        <p class="description">
                            בחר צבע לראש החלונית שנפתחת
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
                    <th scope="row">הצגת אייקון</th>
                    <td>
                        <label>
                            <input type="checkbox" 
                                   name="claude_show_icon" 
                                   value="1" 
                                   <?php checked(get_option('claude_show_icon', '1'), '1'); ?> />
                            הצג אייקון בכפתור
                        </label>
                        <p class="description">
                            סמן כדי להציג אייקון בכפתור (אם הועלה אייקון מותאם אישית)
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
                        
                        <div id="claude-icon-upload-wrapper">
                            <input type="file" 
                                   name="claude_icon_file" 
                                   id="claude_icon_file" 
                                   accept="image/*" 
                                   style="margin-bottom: 10px;" />
                            <button type="button" class="button" id="claude-upload-icon-btn">
                                העלה אייקון
                            </button>
                            <div id="claude-icon-upload-message" style="margin-top: 10px;"></div>
                            <p class="description">
                                העלה תמונה לאייקון (PNG, JPG, SVG, WebP). מומלץ: 32x32 עד 64x64 פיקסלים
                            </p>
                        </div>
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
                            שם ה-repository ב-GitHub (לעדכון אוטומטי)<br>
                            <strong>פורמט:</strong> <code>username/repo-name</code> (לדוגמה: <code>opkl10/summerized</code>)<br>
                            <strong>חשוב:</strong> ודא שה-repository קיים ויש בו Release עם קובץ ZIP
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="claude_github_token">GitHub Token (אופציונלי)</label>
                    </th>
                    <td>
                        <input type="password" 
                               id="claude_github_token" 
                               name="claude_github_token" 
                               value="<?php echo esc_attr(get_option('claude_github_token', '')); ?>" 
                               class="regular-text" 
                               placeholder="ghp_xxxxxxxxxxxx" />
                        <p class="description">
                            Personal Access Token מ-GitHub (אופציונלי)<br>
                            <strong>למה זה עוזר:</strong> מגדיל את ה-rate limit מ-60 ל-5,000 בקשות לשעה<br>
                            <strong>איך ליצור:</strong> <a href="https://github.com/settings/tokens" target="_blank">GitHub Settings → Developer settings → Personal access tokens</a><br>
                            <strong>Scopes נדרשים:</strong> רק <code>public_repo</code> (או <code>repo</code> אם ה-repository פרטי)
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
                            <?php 
                            $last_check_result = get_option('claude_last_check_result', array());
                            if (!empty($last_check_result) && isset($last_check_result['latest_version'])): 
                            ?>
                                <br>
                                <small style="color: #999;">
                                    גרסה אחרונה ב-GitHub: <?php echo esc_html($last_check_result['latest_version']); ?>
                                    <?php if (isset($last_check_result['version_comparison'])): ?>
                                        <?php if ($last_check_result['version_comparison'] === 'same'): ?>
                                            (זהה לגרסה הנוכחית)
                                        <?php elseif ($last_check_result['version_comparison'] === 'older'): ?>
                                            (ישנה יותר מהגרסה הנוכחית)
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </small>
                            <?php endif; ?>
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
                        <?php 
                        $last_check_result = get_option('claude_last_check_result', array());
                        if (!empty($last_check_result) && isset($last_check_result['latest_version'])): 
                        ?>
                            <br><br>
                            <details style="margin-top: 10px;">
                                <summary style="cursor: pointer; color: #0073aa;">🔍 פרטי בדיקה אחרונה</summary>
                                <div style="margin-top: 10px; padding: 10px; background: #f5f5f5; border-radius: 4px; font-size: 12px;">
                                    <strong>גרסה אחרונה ב-GitHub:</strong> <?php echo esc_html($last_check_result['latest_version']); ?><br>
                                    <strong>גרסה נוכחית:</strong> <?php echo esc_html($last_check_result['current_version']); ?><br>
                                    <?php if (isset($last_check_result['comparison_result'])): ?>
                                        <strong>תוצאת השוואה:</strong> 
                                        <?php 
                                        $comp = $last_check_result['comparison_result'];
                                        if ($comp > 0) {
                                            echo '<span style="color: green;">✓ גרסה חדשה זמינה</span>';
                                        } elseif ($comp === 0) {
                                            echo '<span style="color: orange;">= גרסה זהה</span>';
                                        } else {
                                            echo '<span style="color: red;">✗ גרסה ישנה יותר</span>';
                                        }
                                        ?>
                                        <br>
                                    <?php endif; ?>
                                    <strong>זמן בדיקה:</strong> <?php echo esc_html($last_check_result['check_time'] ?? 'לא זמין'); ?>
                                </div>
                            </details>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php
                $update_error = get_option('claude_update_error', '');
                $last_check_result = get_option('claude_last_check_result', array());
                ?>
                <?php if ($update_error): ?>
                <tr>
                    <th>שגיאה</th>
                    <td>
                        <span style="color: #dc3232;">⚠️ <?php echo esc_html($update_error); ?></span>
                        <p class="description">
                            ודא ש-GitHub Repository מוגדר נכון (format: username/repo-name)
                        </p>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($last_check_result)): ?>
                <tr>
                    <th>פרטי בדיקה אחרונה</th>
                    <td>
                        <p class="description">
                            גרסה נוכחית: <strong><?php echo esc_html($last_check_result['current_version'] ?? CLAUDE_SUMMARIZER_VERSION); ?></strong><br>
                            גרסה אחרונה ב-GitHub: <strong><?php echo esc_html($last_check_result['latest_version'] ?? 'לא ידוע'); ?></strong><br>
                            זמן בדיקה: <?php echo esc_html($last_check_result['check_time'] ?? 'לא ידוע'); ?>
                        </p>
                    </td>
                </tr>
                <?php endif; ?>
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
                    var message = response.data.message || 'בדיקה הושלמה';
                    var details = '';
                    
                    if (response.data.update_available) {
                        message = '✓ ' + message + '\n\nגרסה חדשה: ' + response.data.update_version + '\nגרסה נוכחית: ' + response.data.current_version + '\n\nרענן את הדף כדי להתקין את העדכון.';
                    } else {
                        if (response.data.latest_version) {
                            details = '\n\nגרסה אחרונה ב-GitHub: ' + response.data.latest_version;
                            details += '\nגרסה נוכחית: ' + response.data.current_version;
                            
                            if (response.data.version_comparison === 'same') {
                                details += '\n\nהגרסה הנוכחית זהה לגרסה ב-GitHub.';
                            } else if (response.data.version_comparison === 'older') {
                                details += '\n\nהגרסה הנוכחית חדשה יותר מהגרסה ב-GitHub.';
                            } else if (response.data.error) {
                                details += '\n\nשגיאה: ' + response.data.error;
                            }
                        }
                        
                        if (response.data.error) {
                            message = '⚠️ ' + message + details;
                        } else {
                            message = '✓ ' + message + details;
                        }
                    }
                    alert(message);
                    location.reload();
                } else {
                    var errorMsg = 'שגיאה: ' + (response.data.message || 'שגיאה לא ידועה');
                    if (response.data.details && response.data.details.latest_version) {
                        errorMsg += '\n\nגרסה אחרונה ב-GitHub: ' + response.data.details.latest_version;
                        errorMsg += '\nגרסה נוכחית: ' + response.data.details.current_version;
                    }
                    alert(errorMsg);
                    $button.prop('disabled', false).text(originalText);
                }
            },
            error: function() {
                alert('שגיאה בבדיקת עדכונים. ודא שיש חיבור לאינטרנט.');
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
        var $status = $('<div>').css({
            'margin-top': '10px',
            'padding': '10px',
            'background': '#f0f0f0',
            'border-radius': '4px',
            'min-height': '40px'
        });
        $button.after($status);
        
        $button.prop('disabled', true).text('מתקין...');
        $status.html('📥 מוריד את העדכון...');
        
        var startTime = Date.now();
        var progressInterval = setInterval(function() {
            var elapsed = Math.floor((Date.now() - startTime) / 1000);
            if (elapsed > 10) {
                $status.html('📥 מוריד את העדכון... (' + elapsed + ' שניות)');
            }
        }, 1000);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            timeout: 300000, // 5 minutes timeout
            data: {
                action: 'claude_install_update',
                nonce: '<?php echo wp_create_nonce('claude_install_update'); ?>'
            },
            success: function(response) {
                clearInterval(progressInterval);
                
                // Check if response is valid
                if (!response) {
                    $status.html('❌ שגיאה: תגובה לא תקינה מהשרת');
                    $status.css('background', '#f8d7da').css('color', '#721c24');
                    $button.prop('disabled', false).text(originalText);
                    return;
                }
                
                if (response.success) {
                    var successMsg = 'העדכון הותקן בהצלחה!';
                    if (response.data && response.data.message) {
                        successMsg = response.data.message;
                    }
                    $status.html('✅ ' + successMsg);
                    $status.css('background', '#d4edda').css('color', '#155724');
                    $button.text('✅ הותקן!');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    var errorMsg = 'שגיאה לא ידועה';
                    if (response.data && response.data.message) {
                        errorMsg = response.data.message;
                    } else if (response.data) {
                        errorMsg = JSON.stringify(response.data);
                    }
                    $status.html('❌ שגיאה: ' + errorMsg);
                    $status.css('background', '#f8d7da').css('color', '#721c24');
                    $button.prop('disabled', false).text(originalText);
                }
            },
            error: function(xhr, status, error) {
                clearInterval(progressInterval);
                var errorMsg = 'שגיאה בהתקנת עדכון';
                if (status === 'timeout') {
                    errorMsg = 'התהליך לקח יותר מדי זמן (5 דקות). נסה שוב או עדכן ידנית דרך Plugins → Add New → Upload Plugin.';
                } else if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                    errorMsg = xhr.responseJSON.data.message;
                } else if (xhr.status === 0) {
                    errorMsg = 'אין חיבור לשרת. בדוק את החיבור לאינטרנט.';
                } else if (xhr.status >= 500) {
                    errorMsg = 'שגיאת שרת. בדוק את ה-logs של WordPress.';
                }
                $status.html('❌ ' + errorMsg + '<br><small>אם הבעיה נמשכת, עדכן ידנית דרך Plugins → Add New → Upload Plugin</small>');
                $status.css('background', '#f8d7da').css('color', '#721c24');
                $button.prop('disabled', false).text(originalText);
            }
        });
    });
});
</script>
