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
        $menu_icon = file_get_contents(RANKFOUNDRY_SEO_PLUGIN_DIR . 'assets/images/rankfoundry-seo-icon.svg');

        add_menu_page(
            'RankFoundry SEO Settings',   // Page title
            'RankFoundry SEO',            // Menu title
            'manage_options',             // Capability
            $this->plugin_name,           // Menu slug
            array($this, 'display_settings_page'),  // Callback function
            'data:image/svg+xml;base64,' . base64_encode( $menu_icon ),       // Icon URL (optional, you can use a dashicon)
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
        register_setting($this->plugin_name, 'rankfoundry_seo_sync_activation');
    }

    public function manual_sync() {
        RankFoundry_SEO_Sync::sync();
        echo json_encode(['success' => true, 'message' => 'Synced successfully']);
        exit;
    }

    public function activate_sync() {
        RankFoundry_SEO_Cron::schedule_cron_jobs();
        echo json_encode(['success' => true, 'message' => 'Sync activated successfully']);
        exit;
    }
    
    public function deactivate_sync() {
        RankFoundry_SEO_Cron::unschedule_cron_jobs();
        echo json_encode(['success' => true, 'message' => 'Sync deactivated successfully']);
        exit;
    }
}
