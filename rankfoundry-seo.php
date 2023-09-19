<?php
/**
 * RankFoundry SEO Plugin
 *
 * @package   RankFoundry_SEO
 * @link      https://rankfoundry.com
 * @copyright Copyright (C) 2021-2023, Rank Foundry LLC - support@rankfoundry.com
 * @since     1.0.0
 * @license   GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: RankFoundry SEO
 * Plugin URI:  https://rankfoundry.com/plugins/seo
 * Description: An integration bridge between WordPress and RankFoundry SEO, enabling real-time data synchronization and updates via API.
 * Version:     1.1.8
 * Author:      Rank Foundry
 * Author URI:  https://rankfoundry.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: rankfoundry-seo
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin version
if (!defined('RANKFOUNDRY_SEO_VERSION')) {
    define('RANKFOUNDRY_SEO_VERSION', '1.1.8');
}

// Define plugin directory path
if (!defined('RANKFOUNDRY_SEO_PLUGIN_DIR')) {
    define('RANKFOUNDRY_SEO_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

// Define plugin file
if ( ! defined( 'RANKFOUNDRY_SEO_FILE' ) ) {
    define( 'RANKFOUNDRY_SEO_FILE', __FILE__ );
}

// Load the Composer autoloader.
require_once RANKFOUNDRY_SEO_PLUGIN_DIR . 'vendor/autoload.php';

// Include the main class file
require_once RANKFOUNDRY_SEO_PLUGIN_DIR . 'includes/class-rankfoundry-seo.php';

// Generate secret key on plugin activation
register_activation_hook(RANKFOUNDRY_SEO_FILE, array('RankFoundry_SEO_Sync', 'generate_secret_key'));

// Remove our scheduled cron jobs on plugin deactivation
register_deactivation_hook(RANKFOUNDRY_SEO_FILE, array('RankFoundry_SEO_Cron', 'unschedule_cron_jobs'));


// Control where the admin notices appear
global $captured_admin_notices;

function capture_admin_notices() {
    global $captured_admin_notices;
    
    ob_start();
    do_action('admin_notices');
    $captured_admin_notices = ob_get_clean();

    // Remove all other actions attached to admin_notices to prevent duplicate printing
    remove_all_actions('admin_notices');
}
add_action('admin_head', 'capture_admin_notices');


// Begin execution of the plugin.
function run_rankfoundry_seo() {
    $plugin = new RankFoundry_SEO();
    $plugin->run();
}

run_rankfoundry_seo();      
