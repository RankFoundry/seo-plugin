<?php

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

/**
 * RankFoundry SEO Main Class
 */

if (!defined('WPINC')) {
    die;
}

class RankFoundry_SEO {

    /**
     * The unique identifier of this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     */
    public function __construct() {
        if (defined('RANKFOUNDRY_SEO_VERSION')) {
            $this->version = RANKFOUNDRY_SEO_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'rankfoundry-seo';

        $this->load_dependencies();
        $this->set_locale();
        $this->initialize_update_checker();
        $this->define_public_hooks();

        if (is_admin()) {
            $this->define_admin_hooks();
        }
    }

    /**
     * Load the required dependencies for this plugin.
     */
    private function load_dependencies() {

        require_once RANKFOUNDRY_SEO_PLUGIN_DIR . 'includes/class-rankfoundry-seo-api.php';
        require_once RANKFOUNDRY_SEO_PLUGIN_DIR . 'includes/class-rankfoundry-seo-sync.php';
        require_once RANKFOUNDRY_SEO_PLUGIN_DIR . 'includes/class-rankfoundry-seo-cron.php';

        // Add action for our cron hook
        add_action('rankfoundry_seo_sync', array('RankFoundry_SEO_Sync', 'sync'));
        
        // Add custom cron schedules
        add_filter('cron_schedules', array('RankFoundry_SEO_Cron', 'custom_cron_schedules'));

        require_once RANKFOUNDRY_SEO_PLUGIN_DIR . 'includes/class-rankfoundry-seo-api.php';
        $api = new RankFoundry_SEO_API();

    }

    /**
     * Define the locale for this plugin for internationalization.
     */
    private function set_locale() {
        // This is where you'd set up any translation/internationalization functionality.
    }

    /**
     * Load the update checker
     */
    private function initialize_update_checker() {
        $myUpdateChecker = PucFactory::buildUpdateChecker(
            'https://github.com/rankfoundry/seo-plugin/',
            RANKFOUNDRY_SEO_FILE,
            'rankfoundry-seo',
            48
        );

        //Set the branch that contains the stable release.
        $myUpdateChecker->setBranch('master');

        //Optional: If you're using a private repository, specify the access token like this:
        //$myUpdateChecker->setAuthentication('your-token-here');
    }

    /**
     * Register all of the hooks related to the admin area functionality.
     */
    private function define_admin_hooks() {

        require_once RANKFOUNDRY_SEO_PLUGIN_DIR . 'admin/class-rankfoundry-seo-admin.php';
        $this->admin = new RankFoundry_SEO_Admin($this->plugin_name, $this->version);

        add_action('in_admin_header', array($this->admin, 'rankfoundry_seo_remove_other_notices'), 1000); // The high priority ensures it runs after other plugins.
        add_action('admin_enqueue_scripts', array($this->admin, 'enqueue_styles'));
        add_action('admin_menu', array($this->admin, 'add_menu'));
        add_action('admin_init', array($this->admin, 'register_settings'));

        // Add AJAX handler for the sync
        add_action('wp_ajax_manual_sync', array($this->admin, 'manual_sync'));
        add_action('wp_ajax_activate_sync', array($this->admin, 'activate_sync'));
        add_action('wp_ajax_deactivate_sync', array($this->admin, 'deactivate_sync'));
        add_action('wp_ajax_get_last_sync', array($this->admin, 'get_last_sync'));
    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     */
    private function define_public_hooks() {
        // This will involve enqueueing scripts/styles for the frontend, and other related functions.
    }

    /**
     * Run the plugin.
     */
    public function run() {
        // Any code that needs to execute upon plugin initialization would go here.
    }

}

