<?php

class RankFoundry_SEO_API {

    private $secret_key_option_name = 'rankfoundry_seo_secret_key';

    public function __construct() {
        add_action('rest_api_init', [$this, 'register_rest_routes']);
    }

    public function register_rest_routes() {
        register_rest_route('rankfoundry-seo/v1', '/create-post', [
            'methods' => 'POST',
            'callback' => [$this, 'create_post_callback'],
            'permission_callback' => [$this, 'permissions_check'],
        ]);
    }

    public function create_post_callback($request) {
        $params = $request->get_json_params();
    
        $post_data = [
            'post_title'    => sanitize_text_field($params['title']),
            'post_content'  => sanitize_textarea_field($params['content']),
            'post_status'   => sanitize_text_field($params['status']),
            'post_author'   => intval($params['author']),
            'post_category' => [intval($params['category'])], // Assuming category is an ID
        ];
    
        $post_id = wp_insert_post($post_data);
    
        if (is_wp_error($post_id)) {
            return new WP_Error('post_creation_failed', 'Failed to create post.', ['status' => 500]);
        }
    
        // Add SEO meta values if provided
        if (isset($params['meta_title'])) {
            update_post_meta($post_id, '_seopress_titles_title', sanitize_text_field($params['meta_title']));
        }
        if (isset($params['meta_description'])) {
            update_post_meta($post_id, '_seopress_titles_desc', sanitize_text_field($params['meta_description']));
        }
        if (isset($params['keyword'])) {
            update_post_meta($post_id, '_seopress_analysis_target_kw', sanitize_text_field($params['keyword']));
        }
    
        return new WP_REST_Response(['message' => 'Post created successfully!', 'post_id' => $post_id], 200);
    }
    

    public function permissions_check($request) {
        $api_key = $request->get_header('x-api-key');
        $stored_key = get_option($this->secret_key_option_name);

        if (!$api_key || $api_key !== $stored_key) {
            return new WP_Error('rest_forbidden', 'Invalid API key', ['status' => 401]);
        }

        return true;
    }
}
