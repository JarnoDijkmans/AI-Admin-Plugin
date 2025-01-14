<?php

function yooker_get_subscription(WP_REST_Request $request) {
    global $wpdb;
    $tablename = $wpdb->prefix . 'ai_subscriptions_table';

    $response = new \stdClass();
    $response->data = new \stdClass();

    $response->data->status = 200;

    $id = $request->get_param('id');

    if ($id) {
        $data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tablename WHERE id = %d", $id), ARRAY_A);
        return rest_response_success(200, 'Successfully retrieved subscriptions', $data);
    } else {
        return rest_response_fail(400, 'Missing id');
    }
}


function register_get_subscription_route() {
    register_rest_route('yooker-ai-admin/v1', '/subscriptions/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'yooker_get_subscription',
        'permission_callback' => 'yooker_check_rest_permissions',
    ));
}


add_action('rest_api_init', 'register_get_subscription_route');
?>