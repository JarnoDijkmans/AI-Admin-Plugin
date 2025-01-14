<?php
function yooker_get_active_subscriptions(WP_REST_Request $request) {
    global $wpdb;
    $user_subscriptions_table = $wpdb->prefix . 'ai_user_subscriptions_table';

    $user = $request->get_param('authenticated_user');
    $user_id = $user->ID;
    
    if (!$user_id) {
        return rest_response_fail(403, 'User is not logged in');
    }

    $query = $wpdb->prepare(
        "SELECT subscription_id FROM $user_subscriptions_table WHERE user_id = %d AND (end_date IS NULL OR end_date > NOW())",
        $user_id
    );

    $active_subscriptions = $wpdb->get_col($query); 

    if ($active_subscriptions) {
        return rest_response_success(200, 'User has active subscriptions', [
            'subscriptions' => $active_subscriptions
        ]);
    } else {
        return rest_response_fail(404, 'User does not have any active subscriptions');
    }
}

function register_get_active_subscriptions_route() {
    register_rest_route('yooker-ai-admin/v1', '/active-subscriptions/', array(
        'methods' => 'GET',
        'callback' => 'yooker_get_active_subscriptions',
        'permission_callback' => 'check_basic_auth_permission',
    ));
}
add_action('rest_api_init', 'register_get_active_subscriptions_route');
?>