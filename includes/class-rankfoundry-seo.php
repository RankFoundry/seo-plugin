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
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     */
    private function load_dependencies() {
        // Include other required files here
        // For example, the API and sync classes, once they're set up.
        require_once RANKFOUNDRY_SEO_PLUGIN_DIR . 'includes/class-rankfoundry-seo-api.php';
        require_once RANKFOUNDRY_SEO_PLUGIN_DIR . 'includes/class-rankfoundry-seo-sync.php';
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
            'rankfoundry-seo'
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
        // This will involve enqueueing scripts/styles for the admin, and other related functions.
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

