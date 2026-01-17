<?php
/**
 * Plugin Name: Timed Popup & Top Banner
 * Plugin URI: https://github.com/vadikonline1/wp-timed-popup-banner/
 * Description: A plugin that displays a popup after X seconds and a top banner
 * Version: 1.0.1
 * Author: Steel..xD
 * License: GPL v2 or later
 * Text Domain: timed-popup-banner
 * Requires Plugins: github-plugin-manager-main
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TPB_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('TPB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TPB_VERSION', '1.0.0');

// Include required files
require_once TPB_PLUGIN_PATH . 'includes/class-admin-settings.php';
require_once TPB_PLUGIN_PATH . 'includes/class-popup-handler.php';
require_once TPB_PLUGIN_PATH . 'includes/class-banner-handler.php';

add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($actions) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=popup-banner-settings') . '">⚙️ Settings</a>';
    array_unshift($actions, $settings_link);
    
    // Numele plugin-ului necesar
    $required_plugin = 'github-plugin-manager-main/github-plugin-manager.php';
    
    // Asigură-te că funcția is_plugin_active() este disponibilă
    if (!function_exists('is_plugin_active')) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    
    if (!is_plugin_active($required_plugin)) {
        $plugin_path = WP_PLUGIN_DIR . '/' . $required_plugin;
        
        if (!file_exists($plugin_path)) {
            $download_link = '<a href="https://github.com/vadikonline1/github-plugin-manager/archive/refs/heads/main.zip" style="color: red;">
                              ⬇️ Requires Download
                            </a>';
            array_unshift($actions, $download_link);
        } else {
            $activate_link = '<span style="color: #f0ad4e;">⚠️ Plugin installed but not activated</span>';
            array_unshift($actions, $activate_link);
        }
    }    
    return $actions;
});
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
