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
            'post_content'  => wp_kses_post($params['content']),
            'post_status'   => sanitize_text_field($params['status']),
            'post_author'   => intval($params['author']),
            'post_category' => [intval($params['category'])],
        ];

        // Check if slug is set in the API call and add it to the post data
        if (isset($params['slug'])) {
            $post_data['post_name'] = sanitize_title($params['slug']);
        }

        // Check if excerpt is set in the API call and add it to the post data
        if (isset($params['excerpt'])) {
            $post_data['post_excerpt'] = sanitize_textarea_field($params['excerpt']);
        }
    
        $post_id = wp_insert_post($post_data);
    
        if (is_wp_error($post_id)) {
            return new WP_Error('post_creation_failed', 'Failed to create post. '.json_encode($post_id), ['status' => 500]);
        }

        if (isset($params['image_base64'])){
            $image_id = $this->save_image($params['image_base64'], $params['image_slug'], $params['image_title'], $params['image_alt'], $params['author']);
            set_post_thumbnail( $post_id, $image_id );
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
    
    public function save_image($image_base64, $image_slug, $image_title, $image_alt, $author_id) {

        // Upload dir.
        $upload_dir  = wp_upload_dir();
        $upload_path = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;
    
        $img             = str_replace( 'data:image/png;base64,', '', $image_base64 );
        $img             = str_replace( ' ', '+', $img );
        $decoded         = base64_decode( $img );
        $filename        = $image_slug . '.png';
        $file_type       = 'image/png';
        $hashed_filename = md5( $filename . microtime() ) . '_' . $filename;
    
        // Save the image in the uploads directory.
        $upload_file = file_put_contents( $upload_path . $filename, $decoded );
    
        $attachment = array(
            'post_mime_type' => $file_type,
            'post_title'     => $image_title,
            'post_content'   => '',
            'post_status'    => 'inherit',
            'post_author'    => $author_id
        );
    
        $attach_id = wp_insert_attachment($attachment, $upload_dir['path'] . '/' . $filename);

        require_once(ABSPATH . 'wp-admin/includes/image.php');
		$attach_data = wp_generate_attachment_metadata($attach_id, $upload_dir['path'] . '/' . $filename);
		wp_update_attachment_metadata($attach_id, $attach_data);
        update_post_meta($attach_id, '_wp_attachment_image_alt', $image_alt);

        return $attach_id;
    }

    public function permissions_check($request) {
        $api_key = $request->get_header('x-api-key');
        $stored_key = get_option($this->secret_key_option_name);

        if (!$api_key || $api_key !== $stored_key) {
            return new WP_Error('rest_forbidden', 'Invalid API key', ['status' => 401]);
        }

        return true;
    }

    function custom_debug_log($message) {
        $logFile = WP_CONTENT_DIR . '/debug_log.txt';
        file_put_contents($logFile, $message . "\n", FILE_APPEND);
    }
}
