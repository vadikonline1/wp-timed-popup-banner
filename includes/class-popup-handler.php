<?php
class TPB_Popup_Handler {
    
    public function __construct() {
        if (get_option('tpb_popup_enabled', 1)) {
            add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
            add_action('wp_footer', array($this, 'render_popup'));
        }
    }
    
    public function enqueue_frontend_assets() {
        // Enqueue frontend styles
        wp_enqueue_style(
            'tpb-frontend-style',
            TPB_PLUGIN_URL . 'assets/css/frontend-style.css',
            array(),
            TPB_VERSION
        );
        
        // Enqueue frontend scripts
        wp_enqueue_script(
            'tpb-frontend-popup',
            TPB_PLUGIN_URL . 'assets/js/frontend-popup.js',
            array('jquery'),
            TPB_VERSION,
            true
        );
        
        // Localize script with settings
        wp_localize_script('tpb-frontend-popup', 'tpb_settings', array(
            'popup_enabled' => get_option('tpb_popup_enabled', 1),
            'popup_delay' => get_option('tpb_popup_delay', 5) * 1000, // Convert to milliseconds
            'popup_image' => wp_get_attachment_url(get_option('tpb_popup_image_id')),
            'redirect_type' => get_option('tpb_popup_redirect_type', 'none'),
            'redirect_url' => get_option('tpb_popup_redirect_url', ''),
            'redirect_page' => get_option('tpb_popup_redirect_page', '')
        ));
    }
    
    public function render_popup() {
        $image_id = get_option('tpb_popup_image_id');
        $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
        
        if (empty($image_url)) {
            return;
        }
        
        // Get redirect URL
        $redirect_url = '';
        $redirect_type = get_option('tpb_popup_redirect_type', 'none');
        
        if ($redirect_type === 'url') {
            $redirect_url = get_option('tpb_popup_redirect_url', '');
        } elseif ($redirect_type === 'page') {
            $page_id = get_option('tpb_popup_redirect_page');
            if ($page_id) {
                $redirect_url = get_permalink($page_id);
            }
        }
        
        ?>
        <div id="tpb-popup-overlay" class="tpb-popup-overlay" style="display: none;">
            <div class="tpb-popup-container">
                <div class="tpb-popup-content">
                    <button type="button" class="tpb-popup-close">&times;</button>
                    
                    <?php if ($redirect_type !== 'none' && !empty($redirect_url)): ?>
                    <a href="<?php echo esc_url($redirect_url); ?>" 
                       class="tpb-popup-link" 
                       <?php echo $redirect_type === 'url' ? 'target="_blank"' : ''; ?>>
                    <?php endif; ?>
                    
                        <img src="<?php echo esc_url($image_url); ?>" 
                             alt="Popup Image" 
                             class="tpb-popup-image">
                    
                    <?php if ($redirect_type !== 'none' && !empty($redirect_url)): ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
}
