jQuery(document).ready(function($) {
    
    // Handle image upload
    $('.tpb-upload-image-button').click(function(e) {
        e.preventDefault();
        
        var button = $(this);
        var custom_uploader = wp.media({
            title: 'Select Image',
            library: {
                type: 'image'
            },
            button: {
                text: 'Use this image'
            },
            multiple: false
        }).on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#tpb_popup_image_id').val(attachment.id);
            $('#tpb_popup_image_url').val(attachment.url);
            $('.tpb-image-preview').html('<img src="' + attachment.url + '" style="max-width: 200px; height: auto; margin-top: 10px;">');
            $('.tpb-remove-image-button').show();
            
            // Update preview
            updatePopupPreview(attachment.url);
        }).open();
    });
    
    // Handle image removal
    $('.tpb-remove-image-button').click(function(e) {
        e.preventDefault();
        $('#tpb_popup_image_id').val('');
        $('#tpb_popup_image_url').val('');
        $('.tpb-image-preview').html('');
        $(this).hide();
        
        // Update preview with placeholder
        updatePopupPreview(tpb_admin.plugin_url + 'assets/images/placeholder.jpg');
    });
    
    // Handle redirect type change
    $('#tpb_popup_redirect_type').change(function() {
        var val = $(this).val();
        
        if (val === 'url') {
            $('#tpb_redirect_url_row').show();
            $('#tpb_redirect_page_row').hide();
        } else if (val === 'page') {
            $('#tpb_redirect_url_row').hide();
            $('#tpb_redirect_page_row').show();
        } else {
            $('#tpb_redirect_url_row').hide();
            $('#tpb_redirect_page_row').hide();
        }
    });
    
    // Update banner preview on input change
    $('#tpb_banner_text, #tpb_banner_button_text, #tpb_banner_button_url').on('input', function() {
        updateBannerPreview();
    });
    
    // Update popup preview on delay change
    $('#tpb_popup_delay').on('input', function() {
        // Just update the settings, popup will use these when shown
    });
    
    function updatePopupPreview(imageUrl) {
        $('#tpb-popup-preview .tpb-popup-preview-content img').attr('src', imageUrl);
    }
    
    function updateBannerPreview() {
        var bannerText = $('#tpb_banner_text').val();
        var buttonText = $('#tpb_banner_button_text').val();
        var buttonUrl = $('#tpb_banner_button_url').val();
        
        $('#tpb-banner-preview-content p').text(bannerText);
        $('#tpb-banner-preview-content .btn-link').text(buttonText).attr('href', buttonUrl);
    }
    
    // Initial banner preview update
    updateBannerPreview();
});
