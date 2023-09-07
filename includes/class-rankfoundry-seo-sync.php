<?php

/**
 * RankFoundry SEO Sync Class
 */

 if (!defined('WPINC')) {
    die;
}

class RankFoundry_SEO_Sync {

    // The unique identifier of this plugin.
    private $plugin_name;

    // The current version of the plugin.
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public static function generate_secret_key(){
        if (!get_option('rankfoundry_seo_secret_key')) {
            $secret_key = wp_generate_password(32, false, false);
            update_option('rankfoundry_seo_secret_key', $secret_key);
        }
    }

    public static function sync() {
        // Retrieve stored options
        $api_key = get_option('rankfoundry_seo_api_key');
        $secret_key = get_option('rankfoundry_seo_secret_key');

        $apiUrl = 'https://seoplan.app/api/heartbeat/';

        if (!$api_key || !$secret_key) {
            return; // Missing necessary keys
        }

        // Gather WordPress data
        $categories = get_categories([
            'orderby' => 'name',
            'hide_empty' => 0,
            'parent' => 0
        ]);

        $users = get_users();

        // Construct payload
        $payload = [
            'url' => site_url(),
            'secret_key' => $secret_key,
            'categories' => $categories,
            'users' => $users,
        ];

        $data = [
            'body' => $payload,
            'headers' => [
                'x-api-key' => $api_key
            ],
        ];

        $response = wp_remote_post($apiUrl, $data);
    
        // Handle Response
        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            // Log error message
        } else {
            // Process response if needed
            $response_body = wp_remote_retrieve_body( $response );
             // Save timestamp of last sync
            update_option('rankfoundry_seo_last_sync', current_time('mysql'));

        }
    }

}