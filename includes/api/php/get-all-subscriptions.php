<?php
    function yooker_get_list_subscriptions(WP_REST_Request $request) {
        global $wpdb;
        $tablename = $wpdb->prefix . 'ai_subscriptions_table';

        $data = $wpdb->get_results("SELECT id, name, short_description, price FROM $tablename", ARRAY_A);

        return rest_response_success(200, 'Subscriptions found', $data);
    }  


    function register_get_list_subscriptions_route() {
        register_rest_route('yooker-ai-admin/v1', '/subscriptions/', array(
            'methods' => 'GET',
            'callback' => 'yooker_get_list_subscriptions',
            'permission_callback' => '__return_true',
        ));
    }
    add_action('rest_api_init', 'register_get_list_subscriptions_route');
?>