/**
 * Claude AI Summarizer - Frontend Script
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        const $button = $('#claude-summarize-btn');
        const $panel = $('<div>').attr('id', 'claude-summary-panel').addClass('claude-summary-panel claude-position-' + claudeFrontend.position);
        
        if ($button.length === 0) {
            return;
        }
        
        // Apply custom button color if set
        if (claudeFrontend.buttonColor) {
            $button.css('background', claudeFrontend.buttonColor);
            $button.on('mouseenter', function() {
                $(this).css('background', adjustBrightness(claudeFrontend.buttonColor, -20));
            });
            $button.on('mouseleave', function() {
                $(this).css('background', claudeFrontend.buttonColor);
            });
        }
        
        // Apply custom icon if set
        if (claudeFrontend.buttonIcon) {
            $button.find('.claude-btn-icon').html('<img src="' + claudeFrontend.buttonIcon + '" alt="Icon" style="width: 18px; height: 18px; object-fit: contain;" />');
        }
        
        // Apply custom text if set
        if (claudeFrontend.buttonText) {
            $button.find('.claude-btn-text').text(claudeFrontend.buttonText);
        }
        
        // Helper function to adjust brightness
        function adjustBrightness(hex, steps) {
            steps = Math.max(-255, Math.min(255, steps));
            hex = hex.replace('#', '');
            var r = parseInt(hex.substr(0, 2), 16);
            var g = parseInt(hex.substr(2, 2), 16);
            var b = parseInt(hex.substr(4, 2), 16);
            
            r = Math.max(0, Math.min(255, r + steps));
            g = Math.max(0, Math.min(255, g + steps));
            b = Math.max(0, Math.min(255, b + steps));
            
            return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
        }
        
        // Create summary panel
        $panel.html(`
            <div class="claude-panel-header">
                <h3>📝 סיכום AI</h3>
                <button class="claude-close-btn" aria-label="סגור">×</button>
            </div>
            <div class="claude-panel-content">
                <p>טוען סיכום...</p>
            </div>
            <div class="claude-panel-footer">
                <button class="claude-copy-btn">העתק</button>
                <button class="claude-close-btn-footer">סגור</button>
            </div>
        `);
        
        $('body').append($panel);
        
        const $panelContent = $panel.find('.claude-panel-content');
        const $copyBtn = $panel.find('.claude-copy-btn');
        const $closeBtns = $panel.find('.claude-close-btn, .claude-close-btn-footer');
        
        // Button click handler
        $button.on('click', function() {
            if ($button.prop('disabled')) {
                return;
            }
            
            const originalText = $button.find('.claude-btn-text').text();
            
            // Show loading
            $button.prop('disabled', true);
            $button.find('.claude-btn-text').text(claudeFrontend.loadingText);
            $panel.addClass('active');
            $panelContent.html('<p>מסכם את הפוסט...</p>');
            
            // AJAX request
            $.ajax({
                url: claudeFrontend.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'claude_summarize',
                    post_id: claudeFrontend.postId,
                    nonce: claudeFrontend.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $panelContent.html('<p>' + response.data.summary + '</p>');
                        
                        // Store summary for copy
                        $panel.data('summary', response.data.summary);
                    } else {
                        $panelContent.html('<p style="color: #c33;">שגיאה: ' + (response.data.message || 'שגיאה לא ידועה') + '</p>');
                    }
                },
                error: function() {
                    $panelContent.html('<p style="color: #c33;">שגיאה בקבלת סיכום. אנא נסה שוב.</p>');
                },
                complete: function() {
                    $button.prop('disabled', false);
                    $button.find('.claude-btn-text').text(originalText);
                }
            });
        });
        
        // Copy button
        $copyBtn.on('click', function() {
            const summary = $panel.data('summary');
            if (!summary) {
                return;
            }
            
            // Copy to clipboard
            if (navigator.clipboard) {
                navigator.clipboard.writeText(summary).then(function() {
                    $copyBtn.text('הועתק!');
                    setTimeout(function() {
                        $copyBtn.text('העתק');
                    }, 2000);
                });
            } else {
                // Fallback for older browsers
                const textarea = $('<textarea>').val(summary).appendTo('body');
                textarea.select();
                document.execCommand('copy');
                textarea.remove();
                $copyBtn.text('הועתק!');
                setTimeout(function() {
                    $copyBtn.text('העתק');
                }, 2000);
            }
        });
        
        // Close buttons
        $closeBtns.on('click', function() {
            $panel.removeClass('active');
        });
        
        // Close on outside click
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#claude-summary-button, #claude-summary-panel').length) {
                $panel.removeClass('active');
            }
        });
        
        // Close on ESC key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $panel.hasClass('active')) {
                $panel.removeClass('active');
            }
        });
    });
})(jQuery);
