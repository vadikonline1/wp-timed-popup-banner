jQuery(document).ready(function($) {
    
    // Check if popup is enabled
    if (!tpb_settings.popup_enabled || !tpb_settings.popup_image) {
        return;
    }
    
    // Check if popup was already shown in this session
    if (sessionStorage.getItem('tpb_popup_shown')) {
        return;
    }
    
    // Show popup after delay
    setTimeout(function() {
        $('#tpb-popup-overlay').fadeIn();
        
        // Mark as shown
        sessionStorage.setItem('tpb_popup_shown', 'true');
        
        // Set cookie for 24 hours
        var date = new Date();
        date.setTime(date.getTime() + (24 * 60 * 60 * 1000));
        document.cookie = "tpb_popup_shown=true; expires=" + date.toUTCString() + "; path=/";
        
    }, tpb_settings.popup_delay);
    
    // Close popup on X click
    $('.tpb-popup-close').click(function() {
        $('#tpb-popup-overlay').fadeOut();
    });
    
    // Close popup on overlay click
    $('#tpb-popup-overlay').click(function(e) {
        if (e.target === this) {
            $(this).fadeOut();
        }
    });
    
    // Handle escape key
    $(document).keyup(function(e) {
        if (e.key === "Escape") {
            $('#tpb-popup-overlay').fadeOut();
        }
    });
});
