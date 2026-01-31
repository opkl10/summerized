/**
 * Claude AI Summarizer - Frontend Script
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        const $button = $('#claude-summarize-btn');
        
        // Determine panel position based on button position
        let panelPosition = claudeFrontend.position;
        const fixedPositions = ['bottom-left', 'bottom-right', 'top-left', 'top-right'];
        if (!fixedPositions.includes(claudeFrontend.position)) {
            // For inline positions, use bottom-right as default
            panelPosition = 'bottom-right';
        }
        
        const $panel = $('<div>').attr('id', 'claude-summary-panel').addClass('claude-summary-panel claude-position-' + panelPosition);
        
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
        
        // Apply custom panel color if set
        if (claudeFrontend.panelColor) {
            $panel.find('.claude-panel-header').css('background', 'linear-gradient(135deg, ' + claudeFrontend.panelColor + ' 0%, ' + adjustBrightness(claudeFrontend.panelColor, -30) + ' 100%)');
        }
        
        // Apply custom icon if set and show_icon is enabled
        if (claudeFrontend.showIcon) {
            if (claudeFrontend.buttonIcon) {
                $button.find('.claude-btn-icon').html('<img src="' + claudeFrontend.buttonIcon + '" alt="Icon" style="width: 18px; height: 18px; object-fit: contain;" />');
            }
        } else {
            // Hide icon if disabled
            $button.find('.claude-btn-icon').hide();
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
                        var summary = response.data.summary;
                        
                        // Format summary based on length
                        if (claudeFrontend.summaryLength === 'short') {
                            // Convert to bullet points if not already formatted
                            if (summary.indexOf('-') === -1 && summary.indexOf('•') === -1 && summary.indexOf('*') === -1) {
                                // Split by sentences and create bullets
                                var sentences = summary.split(/[.!?]\s+/).filter(function(s) { return s.trim().length > 0; });
                                if (sentences.length > 0) {
                                    summary = '<ul style="list-style: none; padding-right: 20px; margin: 10px 0; line-height: 1.8;">';
                                    sentences.forEach(function(sentence) {
                                        if (sentence.trim().length > 0) {
                                            summary += '<li style="margin-bottom: 10px; padding-right: 20px; position: relative;">' + 
                                                      '<span style="position: absolute; right: 0; color: ' + (claudeFrontend.panelColor || '#667eea') + '; font-size: 18px; font-weight: bold;">•</span>' + 
                                                      '<span style="display: block; padding-right: 15px;">' + sentence.trim() + '</span>' + 
                                                      '</li>';
                                        }
                                    });
                                    summary += '</ul>';
                                }
                            } else {
                                // Already has bullets, format them nicely
                                summary = summary.replace(/^[-•*]\s*/gm, '');
                                var lines = summary.split(/\n/).filter(function(l) { return l.trim().length > 0; });
                                if (lines.length > 0) {
                                    summary = '<ul style="list-style: none; padding-right: 20px; margin: 10px 0; line-height: 1.8;">';
                                    lines.forEach(function(line) {
                                        if (line.trim().length > 0) {
                                            summary += '<li style="margin-bottom: 10px; padding-right: 20px; position: relative;">' + 
                                                      '<span style="position: absolute; right: 0; color: ' + (claudeFrontend.panelColor || '#667eea') + '; font-size: 18px; font-weight: bold;">•</span>' + 
                                                      '<span style="display: block; padding-right: 15px;">' + line.trim() + '</span>' + 
                                                      '</li>';
                                        }
                                    });
                                    summary += '</ul>';
                                } else {
                                    summary = '<p style="line-height: 1.8;">' + summary + '</p>';
                                }
                            }
                        } else {
                            summary = '<p style="line-height: 1.8;">' + summary + '</p>';
                        }
                        
                        $panelContent.html(summary);
                        
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
