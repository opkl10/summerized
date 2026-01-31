/**
 * Claude AI Summarizer - Admin Script
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Initialize color picker for button
        if ($.fn.wpColorPicker) {
            $('#claude_button_color').wpColorPicker({
                change: function(event, ui) {
                    $('#claude_button_color_text').val(ui.color.toString());
                }
            });
            
            // Sync text input with color picker
            $('#claude_button_color_text').on('input', function() {
                var color = $(this).val();
                if (/^#[0-9A-F]{6}$/i.test(color)) {
                    $('#claude_button_color').wpColorPicker('color', color);
                }
            });
            
            // Initialize color picker for panel
            $('#claude_panel_color').wpColorPicker({
                change: function(event, ui) {
                    $('#claude_panel_color_text').val(ui.color.toString());
                }
            });
            
            // Sync text input with panel color picker
            $('#claude_panel_color_text').on('input', function() {
                var color = $(this).val();
                if (/^#[0-9A-F]{6}$/i.test(color)) {
                    $('#claude_panel_color').wpColorPicker('color', color);
                }
            });
        }
        
        // Remove icon
        $('#claude-remove-icon').on('click', function() {
            if (confirm('האם אתה בטוח שברצונך להסיר את האייקון?')) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'claude_remove_icon',
                        nonce: claudeAdmin.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    }
                });
            }
        });
        
        // Upload icon via AJAX
        $('#claude-upload-icon-btn').on('click', function() {
            var fileInput = $('#claude_icon_file')[0];
            if (!fileInput.files.length) {
                alert('אנא בחר קובץ תמונה');
                return;
            }
            
            var formData = new FormData();
            formData.append('action', 'claude_upload_icon_ajax');
            formData.append('nonce', claudeAdmin.nonce);
            formData.append('icon_file', fileInput.files[0]);
            
            var $btn = $(this);
            var $message = $('#claude-icon-upload-message');
            
            $btn.prop('disabled', true).text('מעלה...');
            $message.html('');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $message.html('<div class="notice notice-success inline"><p>אייקון הועלה בהצלחה!</p></div>');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        $message.html('<div class="notice notice-error inline"><p>' + (response.data.message || 'שגיאה בהעלאת אייקון') + '</p></div>');
                        $btn.prop('disabled', false).text('העלה אייקון');
                    }
                },
                error: function() {
                    $message.html('<div class="notice notice-error inline"><p>שגיאה בהעלאת אייקון. ודא שזה קובץ תמונה תקין.</p></div>');
                    $btn.prop('disabled', false).text('העלה אייקון');
                }
            });
        });
        
        // Preview button color
        $('#claude_button_color').on('change', function() {
            updateButtonPreview();
        });
        
        $('#claude_button_text').on('input', function() {
            updateButtonPreview();
        });
        
        function updateButtonPreview() {
            var color = $('#claude_button_color').val();
            var text = $('#claude_button_text').val() || 'סכם עם AI';
            var icon = $('#claude_icon_file').val() ? '📷' : '🤖';
            
            if (!$('#claude-button-preview').length) {
                $('.claude-admin-section:first').after('<div class="claude-admin-section"><h2>תצוגה מקדימה</h2><div id="claude-button-preview" style="padding: 20px; text-align: center;"></div></div>');
            }
            
            $('#claude-button-preview').html(
                '<button class="claude-btn" style="background: ' + color + '; padding: 12px 24px; border: none; border-radius: 25px; color: white; font-size: 14px; font-weight: 600; cursor: pointer;">' +
                '<span style="margin-left: 8px;">' + icon + '</span>' +
                '<span>' + text + '</span>' +
                '</button>'
            );
        }
        
        // Initial preview
        updateButtonPreview();
    });
})(jQuery);
