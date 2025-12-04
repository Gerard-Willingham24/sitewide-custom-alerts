/**
 * Site Alert Banner Admin JavaScript
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Add admin page class for styling
        $('.wrap').addClass('site-alert-admin-page');
        
        // Create preview section
        createPreviewSection();
        
        // Update preview when settings change
        $('#site_alert_content, select[name*="[type]"], input[name*="[dismissible]"]').on('change keyup', updatePreview);
        $('input[name*="[position]"], input[name*="[width]"]').on('change', updatePreview);
        
        // Initial preview update
        updatePreview();
        
        function createPreviewSection() {
            var previewHtml = '<div class="alert-preview">' +
                '<h3>Preview</h3>' +
                '<div class="alert-preview-banner" id="alert-preview-banner">' +
                '<div class="alert-content">' +
                '<p class="preview-content">Your alert message will appear here...</p>' +
                '<button class="alert-preview-close" style="display: none;">&times;</button>' +
                '</div>' +
                '</div>' +
                '<p class="description">This is how your alert will appear to visitors.</p>' +
                '</div>';
            
            $('.form-table').after(previewHtml);
        }
        
        function updatePreview() {
            var $preview = $('#alert-preview-banner');
            var $closeBtn = $preview.find('.alert-preview-close');
            var $content = $preview.find('.preview-content');
            
            // Get current settings
            var alertType = $('select[name*="[type]"]').val() || 'info';
            var isDismissible = $('input[name*="[dismissible]"]').is(':checked');
            var content = getEditorContent();
            
            // Update preview classes
            $preview.removeClass('preview-info preview-success preview-warning preview-error');
            $preview.addClass('preview-' + alertType);
            
            // Update content - strip paragraph tags if content already has them to avoid double wrapping
            if (content.trim()) {
                // Remove outer <p> tags if they exist since we're wrapping in our own <p>
                var cleanContent = content.replace(/^<p[^>]*>|<\/p>$/g, '');
                $content.html(cleanContent);
            } else {
                $content.text('Your alert message will appear here...');
            }
            
            // Show/hide close button
            if (isDismissible) {
                $closeBtn.show();
            } else {
                $closeBtn.hide();
            }
        }
        
        function getEditorContent() {
            // Try to get content from TinyMCE editor first
            if (typeof tinyMCE !== 'undefined' && tinyMCE.get('site_alert_content')) {
                return tinyMCE.get('site_alert_content').getContent();
            }
            
            // Fallback to textarea
            return $('#site_alert_content').val() || '';
        }
        
        // Handle TinyMCE editor events
        $(document).on('tinymce-editor-init', function(event, editor) {
            if (editor.id === 'site_alert_content') {
                editor.on('keyup change', function() {
                    setTimeout(updatePreview, 100);
                });
            }
        });
        
        // Form validation
        $('form').on('submit', function(e) {
            var content = getEditorContent();
            var isEnabled = $('input[name*="[enabled]"]').is(':checked');
            
            if (isEnabled && !content.trim()) {
                e.preventDefault();
                alert('Please enter an alert message before enabling the alert.');
                return false;
            }
        });
    });
    
})(jQuery);