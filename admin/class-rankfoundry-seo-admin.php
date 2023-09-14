<?php

class RankFoundry_SEO_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    public const option_group = "rankfoundry_group";
    public const option_key = "rankfoundry_options";
    public const page_slug = "rankfoundry-settings";
    public const page_title = "Rank Foundry Settings";
    public const menu_title = "Rank Foundry";
    private $api_key_option_name = 'rankfoundry_seo_api_key';  // Name of the option where API key is stored

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    // Register settings page under WP Settings
    public function add_menu() {
        $menu_icon = file_get_contents(RANKFOUNDRY_SEO_PLUGIN_DIR . 'assets/images/rankfoundry-seo-icon.svg');

        /*
        add_menu_page(
            'RankFoundry SEO Settings',   // Page title
            'RankFoundry',            // Menu title
            'manage_options',             // Capability
            $this->plugin_name,           // Menu slug
            array($this, 'display_settings_page'),  // Callback function
            'data:image/svg+xml;base64,' . base64_encode( $menu_icon ),       // Icon URL (optional, you can use a dashicon)
            5                             // Position (optional)
        );
        */
        add_menu_page(
            $this::page_title,
            $this::menu_title,
            'manage_options',
            $this::page_slug,
            array($this, 'display_settings_page'),
            'data:image/svg+xml;base64,' . base64_encode( $menu_icon ),
            4
        );

        add_submenu_page($this::page_slug, 'General', 'General', 'manage_options', $this::page_slug, array($this, 'display_general_page'));
        add_submenu_page($this::page_slug, 'Sync Settings', 'Sync', 'manage_options', $this::page_slug, array($this, 'display_sync_page'));
    }

    // Display the general page
    public function display_general_page() {
        include_once 'partials/admin-general.php';
    }
    
    // Display the settings page
    public function display_sync_page() {
        include_once 'partials/admin-sync.php';
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
        update_option('rankfoundry_seo_sync_activation', '1');
        RankFoundry_SEO_Cron::schedule_cron_jobs();
        echo json_encode(['success' => true, 'message' => 'Sync activated successfully']);
        exit;
    }
    
    public function deactivate_sync() {
        RankFoundry_SEO_Cron::unschedule_cron_jobs();
        update_option('rankfoundry_seo_sync_activation', '0');
        echo json_encode(['success' => true, 'message' => 'Sync deactivated successfully']);
        exit;
    }
}
