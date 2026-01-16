<?php
/**
 * Plugin Name: Timed Popup & Top Banner
 * Plugin URI: https://github.com/vadikonline1/wp-timed-popup-banner/
 * Description: A plugin that displays a popup after X seconds and a top banner
 * Version: 1.0.1
 * Author: Steel..xD
 * License: GPL v2 or later
 * Text Domain: timed-popup-banner
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
add_action('admin_init', function() {
    // Only run in admin area
    if (!is_admin()) return;
    
    if (!function_exists('is_plugin_active')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    
    $required_plugin = 'github-plugin-manager/github-plugin-manager.php';
    $current_plugin = plugin_basename(__FILE__);
    
    // If current plugin is active but required plugin is not
    if (is_plugin_active($current_plugin) && !is_plugin_active($required_plugin)) {
        // Deactivate current plugin
        deactivate_plugins($current_plugin);
        
        // Show admin notice
        add_action('admin_notices', function() {
            $plugin_name = get_plugin_data(__FILE__)['Name'] ?? 'This plugin';
            ?>
            <div class="notice notice-error">
                <p>
                    <strong><?php echo esc_html($plugin_name); ?></strong> has been deactivated.
                    <br>
                    This plugin requires <strong>GitHub Plugin Manager</strong> to function properly.
                </p>
                <p>
                    <strong>How to fix:</strong>
                    <ol style="margin-left: 20px;">
                        <li>Download <a href="https://github.com/vadikonline1/github-plugin-manager" target="_blank">GitHub Plugin Manager from GitHub</a></li>
                        <li>Go to WordPress Admin → Plugins → Add New → Upload Plugin</li>
                        <li>Upload the downloaded ZIP file and activate it</li>
                        <li>Reactivate <?php echo esc_html($plugin_name); ?></li>
                    </ol>
                </p>
                <p>
                    <a href="https://github.com/vadikonline1/github-plugin-manager/archive/refs/heads/main.zip" 
                       class="button button-primary"
                       style="margin-right: 10px;">
                        ⬇️ Download Plugin (ZIP)
                    </a>
                    <a href="<?php echo admin_url('plugin-install.php?tab=upload'); ?>" 
                       class="button">
                        📤 Upload to WordPress
                    </a>
                </p>
            </div>
            <?php
        });
    }
});

// Prevent activation without required plugin
register_activation_hook(__FILE__, function() {
    if (!function_exists('is_plugin_active')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    
    if (!is_plugin_active('github-plugin-manager/github-plugin-manager.php')) {
        $plugin_name = get_plugin_data(__FILE__)['Name'] ?? 'This plugin';
        
        // Create a user-friendly error message
        $error_message = '
        <div style="max-width: 700px; margin: 50px auto; padding: 30px; background: #fff; border: 2px solid #d63638; border-radius: 5px;">
            <h2 style="color: #d63638; margin-top: 0;">
                <span style="font-size: 24px;">⚠️</span> Missing Required Plugin
            </h2>
            
            <p><strong>' . esc_html($plugin_name) . '</strong> cannot be activated because it requires another plugin to be installed first.</p>
            
            <div style="background: #f0f6fc; padding: 20px; border-radius: 4px; margin: 20px 0;">
                <h3 style="margin-top: 0;">Required Plugin: GitHub Plugin Manager</h3>
                <p>This plugin manages GitHub repositories directly from your WordPress dashboard.</p>
            </div>
            
            <h3>Installation Steps:</h3>
            <ol>
                <li><strong>Download:</strong> Get the plugin from <a href="https://github.com/vadikonline1/github-plugin-manager" target="_blank">GitHub</a></li>
                <li><strong>Upload:</strong> Go to <a href="' . admin_url('plugin-install.php?tab=upload') . '">Plugins → Add New → Upload Plugin</a></li>
                <li><strong>Activate:</strong> Activate the GitHub Plugin Manager</li>
                <li><strong>Return:</strong> Come back and activate ' . esc_html($plugin_name) . '</li>
            </ol>
            
            <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #ddd;">
                <a href="https://github.com/vadikonline1/github-plugin-manager/archive/refs/heads/main.zip" 
                   class="button button-primary button-large"
                   style="margin-right: 10px;">
                    Download ZIP File
                </a>
                <a href="' . admin_url('plugins.php') . '" class="button button-large">
                    Return to Plugins
                </a>
            </div>
            
            <p style="margin-top: 20px; color: #666; font-size: 13px;">
                <strong>Note:</strong> All plugins that require GitHub Plugin Manager will be deactivated until it is installed.
            </p>
        </div>';
        
        // Stop activation with the error message
        wp_die($error_message, 'Missing Required Plugin', 200);
    }
});

// Define plugin constants
define('TPB_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('TPB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TPB_VERSION', '1.0.0');

// Include required files
require_once TPB_PLUGIN_PATH . 'includes/class-admin-settings.php';
require_once TPB_PLUGIN_PATH . 'includes/class-popup-handler.php';
require_once TPB_PLUGIN_PATH . 'includes/class-banner-handler.php';

// Initialize the plugin
class TimedPopupBanner {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init();
    }
    
    private function init() {
        // Initialize admin settings
        new TPB_Admin_Settings();
        
        // Initialize frontend features
        new TPB_Popup_Handler();
        new TPB_Banner_Handler();
        
        // Load text domain
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // Register activation hook
        register_activation_hook(__FILE__, array($this, 'activate'));
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('timed-popup-banner', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }
    
    public function activate() {
        // Set default options on activation
        $default_options = array(
            'popup_enabled' => '1',
            'popup_delay' => '5',
            'popup_image_id' => '',
            'popup_redirect_type' => 'none',
            'popup_redirect_url' => '',
            'popup_redirect_page' => '',
            'banner_enabled' => '1',
            'banner_text' => 'Textul personalizat',
            'banner_button_text' => 'URL + Textul',
            'banner_button_url' => '#'
        );
        
        foreach ($default_options as $key => $value) {
            if (get_option('tpb_' . $key) === false) {
                update_option('tpb_' . $key, $value);
            }
        }
    }
}

// Start the plugin
TimedPopupBanner::get_instance();
