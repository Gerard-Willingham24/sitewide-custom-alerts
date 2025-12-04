/**
 * Site Alert Banner Frontend JavaScript
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        var $alert = $('.site-alert-banner');
        
        if ($alert.length === 0) {
            return;
        }
        
        var alertHash = $alert.data('alert-hash');
        var dismissedKey = 'site_alert_dismissed_' + alertHash;
        
        // Check if alert was previously dismissed
        if (localStorage.getItem(dismissedKey)) {
            return;
        }
        
        // Show the alert with animation
        showAlert();
        
        // Handle close button click
        $alert.on('click', '.alert-close', function(e) {
            e.preventDefault();
            hideAlert();
        });
        
        // Handle escape key
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27 && $alert.hasClass('alert-dismissible')) {
                hideAlert();
            }
        });
        
        function showAlert() {
            $alert.addClass('alert-show').show();
            
            // Add body class for positioning adjustments
            if ($alert.hasClass('alert-top')) {
                $('body').addClass('alert-banner-top-active');
            } else if ($alert.hasClass('alert-bottom')) {
                $('body').addClass('alert-banner-bottom-active');
            }
        }
        
        function hideAlert() {
            $alert.addClass('alert-hide');
            
            // Store dismissal in localStorage
            if (alertHash) {
                localStorage.setItem(dismissedKey, 'true');
            }
            
            // Remove body classes
            $('body').removeClass('alert-banner-top-active alert-banner-bottom-active');
            
            // Hide after animation
            setTimeout(function() {
                $alert.hide();
            }, 300);
        }
    });
    
})(jQuery);