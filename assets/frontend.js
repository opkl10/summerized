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
            var buttonDarkerColor = adjustBrightness(claudeFrontend.buttonColor, -20);
            var buttonGradient = 'linear-gradient(135deg, ' + claudeFrontend.buttonColor + ' 0%, ' + buttonDarkerColor + ' 100%)';
            
            // Apply with !important using inline style
            var buttonCurrentStyle = $button.attr('style') || '';
            $button.attr('style', buttonCurrentStyle + ' background: ' + buttonGradient + ' !important; background-image: ' + buttonGradient + ' !important;');
            
            $button.on('mouseenter', function() {
                var hoverDarker = adjustBrightness(claudeFrontend.buttonColor, -30);
                var hoverGradient = 'linear-gradient(135deg, ' + adjustBrightness(claudeFrontend.buttonColor, -10) + ' 0%, ' + hoverDarker + ' 100%)';
                $(this).attr('style', buttonCurrentStyle + ' background: ' + hoverGradient + ' !important; background-image: ' + hoverGradient + ' !important;');
            });
            $button.on('mouseleave', function() {
                $(this).attr('style', buttonCurrentStyle + ' background: ' + buttonGradient + ' !important; background-image: ' + buttonGradient + ' !important;');
            });
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
        
        // Apply custom panel color AFTER panel is created
        if (claudeFrontend.panelColor) {
            var panelHeader = $panel.find('.claude-panel-header');
            if (panelHeader.length > 0) {
                var darkerColor = adjustBrightness(claudeFrontend.panelColor, -30);
                var gradient = 'linear-gradient(135deg, ' + claudeFrontend.panelColor + ' 0%, ' + darkerColor + ' 100%)';
                
                // Get existing style (preserve padding, display, etc.)
                var existingStyle = panelHeader.attr('style') || '';
                // Remove any existing background styles
                existingStyle = existingStyle.replace(/background[^;]*;?/gi, '').replace(/background-image[^;]*;?/gi, '');
                
                // Apply new background with !important using inline style
                panelHeader.attr('style', existingStyle + ' background: ' + gradient + ' !important; background-image: ' + gradient + ' !important;');
                
                // Also try CSS method as backup
                panelHeader.css({
                    'background': gradient,
                    'background-image': gradient
                });
            }
        }
        
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
                        var summaryLength = claudeFrontend.summaryLength || 'medium';
                        
                        if (summaryLength === 'short') {
                            // Always format as bullet points for short summaries
                            var formattedSummary = '';
                            
                            // First, try to split by newlines (if Claude created separate lines)
                            var lines = summary.split(/\n+/).filter(function(l) { 
                                return l.trim().length > 0; 
                            });
                            
                            // If we have multiple lines, use them
                            if (lines.length > 1) {
                                formattedSummary = '<ul style="list-style: none; padding-right: 20px; margin: 10px 0; line-height: 1.8;">';
                                lines.forEach(function(line) {
                                    // Remove bullet markers if present
                                    line = line.replace(/^[-•*]\s*/, '').trim();
                                    if (line.length > 0) {
                                        formattedSummary += '<li style="margin-bottom: 12px; padding-right: 20px; position: relative;">' + 
                                                          '<span style="position: absolute; right: 0; color: ' + (claudeFrontend.panelColor || '#667eea') + '; font-size: 18px; font-weight: bold; top: 2px;">•</span>' + 
                                                          '<span style="display: block; padding-right: 15px;">' + line + '</span>' + 
                                                          '</li>';
                                    }
                                });
                                formattedSummary += '</ul>';
                            } else {
                                // Single line or paragraph - split by sentences
                                var text = lines.length > 0 ? lines[0] : summary;
                                
                                // Remove any existing bullet markers
                                text = text.replace(/^[-•*]\s*/, '').trim();
                                
                                // Split by sentence endings - more robust regex
                                // Match sentence endings followed by space or end of string
                                var sentenceRegex = /([^.!?]+[.!?]+(?:\s+|$))/g;
                                var matches = text.match(sentenceRegex);
                                
                                var finalSentences = [];
                                
                                if (matches && matches.length > 0) {
                                    // Use matched sentences
                                    finalSentences = matches.map(function(s) {
                                        return s.trim();
                                    }).filter(function(s) {
                                        return s.length > 0;
                                    });
                                } else {
                                    // Fallback: split by common sentence endings
                                    finalSentences = text.split(/[.!?]+\s+/).map(function(s) {
                                        s = s.trim();
                                        if (s.length > 0 && !/[.!?]$/.test(s)) {
                                            s += '.';
                                        }
                                        return s;
                                    }).filter(function(s) {
                                        return s.length > 0;
                                    });
                                }
                                
                                // Limit to 3 sentences max
                                finalSentences = finalSentences.slice(0, 3);
                                
                                if (finalSentences.length > 0) {
                                    formattedSummary = '<ul style="list-style: none; padding-right: 20px; margin: 10px 0; line-height: 1.8;">';
                                    finalSentences.forEach(function(sentence) {
                                        sentence = sentence.trim();
                                        if (sentence.length > 0) {
                                            formattedSummary += '<li style="margin-bottom: 12px; padding-right: 20px; position: relative;">' + 
                                                              '<span style="position: absolute; right: 0; color: ' + (claudeFrontend.panelColor || '#667eea') + '; font-size: 18px; font-weight: bold; top: 2px;">•</span>' + 
                                                              '<span style="display: block; padding-right: 15px;">' + sentence + '</span>' + 
                                                              '</li>';
                                        }
                                    });
                                    formattedSummary += '</ul>';
                                }
                            }
                            
                            // Fallback to paragraph if all else fails
                            if (!formattedSummary) {
                                formattedSummary = '<p style="line-height: 1.8;">' + summary + '</p>';
                            }
                            
                            summary = formattedSummary;
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
