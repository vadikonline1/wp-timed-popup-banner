<?php
class TPB_Admin_Settings {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    public function add_admin_menu() {
        add_options_page(
            'Popup & Banner Settings',
            'Popup & Banner',
            'manage_options',
            'pup-up-settings',
            array($this, 'render_settings_page')
        );
    }
    
    public function enqueue_admin_assets($hook) {
        if ('settings_page_pup-up-settings' !== $hook) {
            return;
        }
        
        // Enqueue WordPress media uploader
        wp_enqueue_media();
        
        // Enqueue admin styles and scripts
        wp_enqueue_style(
            'tpb-admin-style',
            TPB_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            TPB_VERSION
        );
        
        wp_enqueue_script(
            'tpb-admin-preview',
            TPB_PLUGIN_URL . 'assets/js/admin-preview.js',
            array('jquery'),
            TPB_VERSION,
            true
        );
        
        wp_localize_script('tpb-admin-preview', 'tpb_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tpb_nonce')
        ));
    }
    
    public function register_settings() {
        // Register settings for popup
        register_setting('tpb_popup_settings', 'tpb_popup_enabled');
        register_setting('tpb_popup_settings', 'tpb_popup_delay');
        register_setting('tpb_popup_settings', 'tpb_popup_image_id');
        register_setting('tpb_popup_settings', 'tpb_popup_redirect_type');
        register_setting('tpb_popup_settings', 'tpb_popup_redirect_url');
        register_setting('tpb_popup_settings', 'tpb_popup_redirect_page');
        
        // Register settings for banner
        register_setting('tpb_banner_settings', 'tpb_banner_enabled');
        register_setting('tpb_banner_settings', 'tpb_banner_text');
        register_setting('tpb_banner_settings', 'tpb_banner_button_text');
        register_setting('tpb_banner_settings', 'tpb_banner_button_url');
    }
    
    public function render_settings_page() {
        ?>
        <div class="wrap tpb-settings-wrap">
            <h1><?php echo esc_html__('Popup & Banner Settings', 'timed-popup-banner'); ?></h1>
            
            <?php settings_errors(); ?>
            
            <!-- Live Preview Section -->
            <div id="tpb-preview-section" class="tpb-preview-section">
                <h2><?php echo esc_html__('Live Preview', 'timed-popup-banner'); ?></h2>
                <div class="tpb-preview-container">
                    <!-- Popup Preview -->
                    <div id="tpb-popup-preview" class="tpb-popup-preview">
                        <h3><?php echo esc_html__('Popup Preview', 'timed-popup-banner'); ?></h3>
                        <div class="tpb-popup-content">
                            <?php $this->render_popup_preview(); ?>
                        </div>
                    </div>
                    
                    <!-- Banner Preview -->
                    <div id="tpb-banner-preview" class="tpb-banner-preview">
                        <h3><?php echo esc_html__('Top Banner Preview', 'timed-popup-banner'); ?></h3>
                        <div class="tpb-banner-content">
                            <?php $this->render_banner_preview(); ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <form method="post" action="options.php">
                <!-- Popup Settings -->
                <div class="tpb-settings-section">
                    <h2><?php echo esc_html__('Popup Settings', 'timed-popup-banner'); ?></h2>
                    
                    <?php settings_fields('tpb_popup_settings'); ?>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="tpb_popup_enabled"><?php echo esc_html__('Enable Popup', 'timed-popup-banner'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" 
                                       id="tpb_popup_enabled" 
                                       name="tpb_popup_enabled" 
                                       value="1" 
                                       <?php checked(1, get_option('tpb_popup_enabled', 1)); ?>>
                                <label for="tpb_popup_enabled"><?php echo esc_html__('Activate popup display', 'timed-popup-banner'); ?></label>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="tpb_popup_delay"><?php echo esc_html__('Display Delay (seconds)', 'timed-popup-banner'); ?></label>
                            </th>
                            <td>
                                <input type="number" 
                                       id="tpb_popup_delay" 
                                       name="tpb_popup_delay" 
                                       value="<?php echo esc_attr(get_option('tpb_popup_delay', 5)); ?>" 
                                       min="1" 
                                       step="1">
                                <p class="description"><?php echo esc_html__('Number of seconds before popup appears', 'timed-popup-banner'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="tpb_popup_image"><?php echo esc_html__('Popup Image', 'timed-popup-banner'); ?></label>
                            </th>
                            <td>
                                <?php
                                $image_id = get_option('tpb_popup_image_id');
                                $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
                                ?>
                                <div class="tpb-image-upload-wrapper">
                                    <input type="hidden" 
                                           id="tpb_popup_image_id" 
                                           name="tpb_popup_image_id" 
                                           value="<?php echo esc_attr($image_id); ?>">
                                    <input type="text" 
                                           id="tpb_popup_image_url" 
                                           class="regular-text" 
                                           value="<?php echo esc_url($image_url); ?>" 
                                           readonly>
                                    <button type="button" 
                                            class="button tpb-upload-image-button">
                                        <?php echo esc_html__('Upload Image', 'timed-popup-banner'); ?>
                                    </button>
                                    <button type="button" 
                                            class="button tpb-remove-image-button" 
                                            style="<?php echo empty($image_url) ? 'display:none;' : ''; ?>">
                                        <?php echo esc_html__('Remove', 'timed-popup-banner'); ?>
                                    </button>
                                    <?php if ($image_url): ?>
                                    <div class="tpb-image-preview">
                                        <img src="<?php echo esc_url($image_url); ?>" 
                                             style="max-width: 200px; height: auto; margin-top: 10px;">
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="tpb_popup_redirect_type"><?php echo esc_html__('Click Action', 'timed-popup-banner'); ?></label>
                            </th>
                            <td>
                                <select id="tpb_popup_redirect_type" name="tpb_popup_redirect_type">
                                    <option value="none" <?php selected(get_option('tpb_popup_redirect_type'), 'none'); ?>>
                                        <?php echo esc_html__('None', 'timed-popup-banner'); ?>
                                    </option>
                                    <option value="url" <?php selected(get_option('tpb_popup_redirect_type'), 'url'); ?>>
                                        <?php echo esc_html__('Redirect to URL', 'timed-popup-banner'); ?>
                                    </option>
                                    <option value="page" <?php selected(get_option('tpb_popup_redirect_type'), 'page'); ?>>
                                        <?php echo esc_html__('Redirect to Page', 'timed-popup-banner'); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        
                        <tr id="tpb_redirect_url_row" style="<?php echo get_option('tpb_popup_redirect_type') !== 'url' ? 'display:none;' : ''; ?>">
                            <th scope="row">
                                <label for="tpb_popup_redirect_url"><?php echo esc_html__('Redirect URL', 'timed-popup-banner'); ?></label>
                            </th>
                            <td>
                                <input type="url" 
                                       id="tpb_popup_redirect_url" 
                                       name="tpb_popup_redirect_url" 
                                       value="<?php echo esc_url(get_option('tpb_popup_redirect_url')); ?>" 
                                       class="regular-text">
                            </td>
                        </tr>
                        
                        <tr id="tpb_redirect_page_row" style="<?php echo get_option('tpb_popup_redirect_type') !== 'page' ? 'display:none;' : ''; ?>">
                            <th scope="row">
                                <label for="tpb_popup_redirect_page"><?php echo esc_html__('Select Page', 'timed-popup-banner'); ?></label>
                            </th>
                            <td>
                                <?php
                                wp_dropdown_pages(array(
                                    'name' => 'tpb_popup_redirect_page',
                                    'id' => 'tpb_popup_redirect_page',
                                    'selected' => get_option('tpb_popup_redirect_page'),
                                    'show_option_none' => '— Select —',
                                    'option_none_value' => ''
                                ));
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Banner Settings -->
                <div class="tpb-settings-section">
                    <h2><?php echo esc_html__('Top Banner Settings', 'timed-popup-banner'); ?></h2>
                    
                    <?php settings_fields('tpb_banner_settings'); ?>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="tpb_banner_enabled"><?php echo esc_html__('Enable Banner', 'timed-popup-banner'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" 
                                       id="tpb_banner_enabled" 
                                       name="tpb_banner_enabled" 
                                       value="1" 
                                       <?php checked(1, get_option('tpb_banner_enabled', 1)); ?>>
                                <label for="tpb_banner_enabled"><?php echo esc_html__('Activate top banner', 'timed-popup-banner'); ?></label>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="tpb_banner_text"><?php echo esc_html__('Banner Text', 'timed-popup-banner'); ?></label>
                            </th>
                            <td>
                                <input type="text" 
                                       id="tpb_banner_text" 
                                       name="tpb_banner_text" 
                                       value="<?php echo esc_attr(get_option('tpb_banner_text', 'Textul personalizat')); ?>" 
                                       class="regular-text">
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="tpb_banner_button_text"><?php echo esc_html__('Button Text', 'timed-popup-banner'); ?></label>
                            </th>
                            <td>
                                <input type="text" 
                                       id="tpb_banner_button_text" 
                                       name="tpb_banner_button_text" 
                                       value="<?php echo esc_attr(get_option('tpb_banner_button_text', 'URL + Textul')); ?>" 
                                       class="regular-text">
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="tpb_banner_button_url"><?php echo esc_html__('Button URL', 'timed-popup-banner'); ?></label>
                            </th>
                            <td>
                                <input type="url" 
                                       id="tpb_banner_button_url" 
                                       name="tpb_banner_button_url" 
                                       value="<?php echo esc_url(get_option('tpb_banner_button_url', '#')); ?>" 
                                       class="regular-text">
                            </td>
                        </tr>
                    </table>
                </div>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    
    private function render_popup_preview() {
        $image_id = get_option('tpb_popup_image_id');
        $image_url = $image_id ? wp_get_attachment_url($image_id) : TPB_PLUGIN_URL . 'assets/images/placeholder.jpg';
        ?>
        <div class="tpb-popup-preview-content">
            <img src="<?php echo esc_url($image_url); ?>" 
                 alt="Popup Preview" 
                 style="max-width: 100%; height: auto;">
        </div>
        <?php
    }
    
    private function render_banner_preview() {
        ?>
        <div class="banner-top alert alert-danger alert-dismissible fade show" role="alert" id="tpb-banner-preview-content">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-7 col-lg-10">
                        <p><?php echo esc_html(get_option('tpb_banner_text', 'Textul personalizat')); ?></p>
                    </div>
                    <div class="col-5 col-lg-2">
                        <a href="<?php echo esc_url(get_option('tpb_banner_button_url', '#')); ?>" 
                           class="btn btn-link" 
                           target="_blank">
                            <?php echo esc_html(get_option('tpb_banner_button_text', 'URL + Textul')); ?>
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
