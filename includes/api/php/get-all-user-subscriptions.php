<?php
function yooker_get_user_subscriptions(WP_REST_Request $request) {
    global $wpdb;
    $subscriptions_table = $wpdb->prefix . 'ai_subscriptions_table'; 
    $user_subscriptions_table = $wpdb->prefix . 'ai_user_subscriptions_table'; 

    $user = $request->get_param('authenticated_user');

    if (!$user || !isset($user->ID)) {
        return rest_response_fail(403, 'User not authenticated');
    }

    $user_id = $user->ID;

    //You can find this function in includes/utils/user-utils.php
    check_user_subscription_status($user_id);

    // Retrieve all active subscriptions for the user
    $user_subscriptions = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT subscription_id, status, end_date
             FROM $user_subscriptions_table 
             WHERE user_id = %d AND (end_date IS NULL OR end_date > NOW())",
            $user_id
        ),
        ARRAY_A 
    );

    $all_subscriptions = $wpdb->get_results("SELECT id, name, short_description, price FROM $subscriptions_table", ARRAY_A);

    $subscriptions = array_map(function ($subscription) use ($user_subscriptions) {
        // Find the matching user subscription
        $matching_subscription = array_filter($user_subscriptions, function ($user_subscription) use ($subscription) {
            return $user_subscription['subscription_id'] == $subscription['id'];
        });
    
        // If a match exists, extract the status and end_date; otherwise, set defaults
        $matching_subscription = !empty($matching_subscription) ? current($matching_subscription) : null;
    
        return array_merge($subscription, [
            'status' => $matching_subscription ? $matching_subscription['status'] : 3, 
            'end_date' => $matching_subscription ? $matching_subscription['end_date'] : null, 
        ]);
    }, $all_subscriptions);
    
    // Return subscriptions even if the user doesn't have any
    return rest_response_success(200, 'Successfully gathered subscriptions', $subscriptions);
}

function register_get_all_users_subscriptions_route() {
    register_rest_route('yooker-ai-admin/v1', '/user-subscriptions/', array(
        'methods' => 'GET',
        'callback' => 'yooker_get_user_subscriptions',
        'permission_callback' => 'check_basic_auth_permission',
    ));
}
add_action('rest_api_init', 'register_get_all_users_subscriptions_route');
?>