<?php
function get_totalprice(WP_REST_Request $request) {
    global $wpdb;

    $tablename = $wpdb->prefix . 'ai_payment_table';

    $user = $request->get_param('authenticated_user');
    $user_id = $user->ID;

    if (empty($user_id)) {
        return rest_response_fail(401, 'User not authenticated');
    }

    $total_cost = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT SUM(value) FROM $tablename WHERE user_id = %d",
            $user_id
        )
    );

    if ($total_cost === null) {
        return rest_response_success(200, 'No payment history found for this user', 0);
    }

    return rest_response_success(200, 'Total cost retrieved successfully', $total_cost);
}

function register_get_totalprice_route() {
    register_rest_route('yooker-ai-admin/v1', '/get-total-price-spend/', array(
        'methods' => 'GET',
        'callback' => 'get_totalprice',
        'permission_callback' => 'check_basic_auth_permission',
    ));
}
add_action('rest_api_init', 'register_get_totalprice_route');
?>