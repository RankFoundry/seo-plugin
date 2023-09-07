<?php

class RankFoundry_SEO_Admin {

    private $plugin_name;
    private $version;
    private $api_key_option_name = 'rankfoundry_seo_api_key';  // Name of the option where API key is stored

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    // Register settings page under WP Settings
    public function add_settings_page() {
        add_menu_page(
            'RankFoundry SEO Settings',   // Page title
            'RankFoundry SEO',            // Menu title
            'manage_options',             // Capability
            $this->plugin_name,           // Menu slug
            array($this, 'display_settings_page'),  // Callback function
            plugins_url('rankfoundry-seo/assets/images/rankfoundry-seo-icon.svg'),       // Icon URL (optional, you can use a dashicon)
            100                             // Position (optional)
        );
    }

    // Display the settings page
    public function display_settings_page() {
        include_once 'partials/admin-display.php';
    }

    // Register settings
    public function register_settings() {
        register_setting($this->plugin_name, $this->api_key_option_name);
    }

    public function manual_sync() {
       
        RankFoundry_SEO_Sync::sync();
        
        // Return a response (this can be more detailed based on sync success/failure)
        echo json_encode(['success' => true, 'message' => 'Synced successfully']);
        exit;
    }
}
