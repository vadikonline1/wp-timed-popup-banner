<?php
class TPB_Banner_Handler {
    
    public function __construct() {
        if (get_option('tpb_banner_enabled', 1)) {
            add_action('wp_enqueue_scripts', array($this, 'enqueue_banner_assets'));
            add_action('wp_body_open', array($this, 'render_top_banner'));
            add_action('wp_footer', array($this, 'add_banner_script'));
        }
    }
    
    public function enqueue_banner_assets() {
        // Enqueue Bootstrap if not already loaded
        if (!wp_script_is('bootstrap', 'enqueued')) {
            wp_enqueue_style(
                'bootstrap-css',
                'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
                array(),
                '5.3.0'
            );
            
            wp_enqueue_script(
                'bootstrap-js',
                'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
                array('jquery'),
                '5.3.0',
                true
            );
        }
    }
    
    public function render_top_banner() {
        ?>
        <div class="banner-top fixed-top alert alert-danger alert-dismissible fade show" role="alert" id="topBanner" style="display: none;">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-7 col-lg-10">
                        <p><?php echo esc_html(get_option('tpb_banner_text', 'Textul personalizat')); ?></p>
                    </div>
                    <div class="col-5 col-lg-2">
                        <?php if (!empty(get_option('tpb_banner_button_url', '#'))): ?>
                        <a href="<?php echo esc_url(get_option('tpb_banner_button_url', '#')); ?>" 
                           class="btn btn-link" 
                           target="_blank">
                            <?php echo esc_html(get_option('tpb_banner_button_text', 'URL + Textul')); ?>
                        </a>
                        <?php endif; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function add_banner_script() {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var banner = document.getElementById('topBanner');
            if (banner) {
                // Check if banner was dismissed
                if (!localStorage.getItem('tpb_banner_dismissed')) {
                    banner.style.display = 'block';
                }
                
                // Handle close button
                var closeBtn = banner.querySelector('.btn-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', function() {
                        localStorage.setItem('tpb_banner_dismissed', 'true');
                        banner.style.display = 'none';
                    });
                }
            }
        });
        </script>
        <?php
    }
}
