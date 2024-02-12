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
    public const page_slug = "rankfoundry-seo";
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

        add_menu_page(
            $this::page_title,
            $this::menu_title,
            'manage_options',
            $this::page_slug,
            array($this, 'display_dashboard_page'),
            'data:image/svg+xml;base64,' . base64_encode( $menu_icon ),
            4
        );

        add_submenu_page($this::page_slug, 'Dashboard', 'Dashboard', 'manage_options', $this::page_slug, array($this, 'display_dashboard_page'));
        add_submenu_page($this::page_slug, 'Integration', 'Integration', 'manage_options', $this::page_slug . '-integration', array($this, 'display_integration_page'));
        add_submenu_page($this::page_slug, 'Scheduler', 'Scheduler', 'manage_options', $this::page_slug . '-scheduler', array($this, 'display_scheduler_page'));
    }

    // Display the general page
    public function display_dashboard_page() {
        include_once 'partials/admin-dashboard.php';
    }
    
    // Display the integration page
    public function display_integration_page() {
        include_once 'partials/admin-integration.php';
    }

    // Display the scheduler page
    public function display_scheduler_page() {
        include_once 'partials/admin-scheduler.php';
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
        RankFoundry_SEO_Sync::sync();
        echo json_encode(['success' => true, 'message' => 'Sync activated successfully']);
        exit;
    }
    
    public function deactivate_sync() {
        RankFoundry_SEO_Cron::unschedule_cron_jobs();
        update_option('rankfoundry_seo_sync_activation', '0');
        echo json_encode(['success' => true, 'message' => 'Sync deactivated successfully']);
        exit;
    }

    public function get_last_sync() {
        $last_sync = get_option('rankfoundry_seo_last_sync', 'Never');
        echo $last_sync;
        wp_die();  // this is required to terminate immediately and return a proper response
    }

    public function enqueue_styles() {
        // Only enqueue on your plugin's pages
        if ($this->is_rankfoundry_seo_page()) {
            wp_enqueue_style('rankfoundry-seo-tailwind', plugin_dir_url(__DIR__) . 'assets/css/tailwind.css', array(), RANKFOUNDRY_SEO_VERSION);
            wp_enqueue_script('alpine', 'https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js', array(), null, true);
        }
    }

    /**
     * Check if the page is one of ours.
     *
     * @since 1.1.10
     *
     * @return bool
     */
    public function is_rankfoundry_seo_page() {
        if ( ! is_admin() && ( ! isset($_REQUEST['page']) || ! isset($_REQUEST['post_type']))) {
            return false;
        }

        if (isset($_REQUEST['page'])) {
            return 0 === strpos($_REQUEST['page'], 'rankfoundry-seo');
        } elseif (isset($_REQUEST['post_type'])) {
            if (is_array($_REQUEST['post_type']) && !empty($_REQUEST['post_type'])) {
                return 0 === strpos($_REQUEST['post_type'][0], 'rankfoundry-seo');
            } else {
                return 0 === strpos($_REQUEST['post_type'], 'rankfoundry-seo');
            }
        }
    }

    /**
     * Only add our notices on our pages.
     *
     * @since 1.1.10
     *
     * @return bool
     */
    public function rankfoundry_seo_remove_other_notices() {
        if ($this->is_rankfoundry_seo_page()) {
            remove_all_actions('network_admin_notices');
            remove_all_actions('admin_notices');
            remove_all_actions('user_admin_notices');
            remove_all_actions('all_admin_notices');

            // If in the future you have specific notices for your plugin, you can add them here.
            // e.g., add_action('admin_notices', 'rankfoundry_seo_admin_notices');
        }
    }
}
