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
        
        if (localStorage.getItem(dismissedKey)) {
            return;
        }
        
        showAlert();
        
        $alert.on('click', '.alert-close', function(e) {
            e.preventDefault();
            hideAlert();
        });
        
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27 && $alert.hasClass('alert-dismissible')) {
                hideAlert();
            }
        });
        
        function showAlert() {
            $alert.addClass('alert-show').show();
            
            if ($alert.hasClass('alert-top')) {
                $('body').addClass('alert-banner-top-active');
            } else if ($alert.hasClass('alert-bottom')) {
                $('body').addClass('alert-banner-bottom-active');
            }
        }
        
        function hideAlert() {
            $alert.addClass('alert-hide');
            
            if (alertHash) {
                localStorage.setItem(dismissedKey, 'true');
            }
            
            $('body').removeClass('alert-banner-top-active alert-banner-bottom-active');
            
            setTimeout(function() {
                $alert.hide();
            }, 300);
        }
    });
    
})(jQuery);