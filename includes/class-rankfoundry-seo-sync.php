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
        $categories_raw = get_categories([
            'orderby' => 'name',
            'hide_empty' => 0,
            'parent' => 0
        ]);

        $categories = array_map(function($category) {
            return [
                'id' => $category->cat_ID,
                'name' => $category->name,
                'slug' => $category->slug
            ];
        }, $categories_raw);

        $users_raw = get_users();

        $users = array_map(function($user) {
            return [
                'id' => $user->ID,
                'user_email' => $user->user_email,
                'display_name' => $user->display_name,
                'role' => $user->roles[0]
            ];
        }, $users_raw);

        // Gather WordPress updates data
        $plugin_updates = get_plugin_updates();
        $theme_updates  = get_theme_updates();
        $core_updates   = get_core_updates();

        $updates = [
            'plugins' => [],
            'themes'  => [],
            'core'    => null
        ];

        foreach ($plugin_updates as $plugin) {
            $updates['plugins'][] = $plugin->Name;
        }

        foreach ($theme_updates as $theme) {
            $updates['themes'][] = $theme->Name;
        }

        if (!empty($core_updates) && $core_updates[0]->response == 'upgrade') {
            $updates['core'] = $core_updates[0]->current;
        }

        // Construct payload
        $payload = [
            'url' => site_url(),
            'secret_key' => $secret_key,
            'categories' => $categories,
            'users' => $users,
            'updates' => $updates,
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