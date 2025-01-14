<?php
function ai_subscriber_details(WP_REST_Request $request) {
    global $wpdb;

    $user_subscriptions_table = $wpdb->prefix . 'ai_user_subscriptions_table'; 
    $subscriptions_table = $wpdb->prefix . 'ai_subscriptions_table'; 
    $user_table = $wpdb->prefix . 'users';

    $id = $request->get_param('id');

    if (!$id) {
        return rest_response_fail(400, 'User ID is required');
    }

    // Fetch the email from wp_users table
    $email = $wpdb->get_var($wpdb->prepare("SELECT user_email FROM $user_table WHERE ID = %d", $id));
    if (!$email) {
        return rest_response_fail(404, 'User not found');
    }

    // Fetch the meta fields from wp_usermeta
    $meta_keys = [
        'first_name', 'last_name', 'surnameprefix', 'company',
        'phonenumber', 'address', 'zipcode', 'town',
        'country', 'clientnumber_gripp', 'activated'
    ];

    $meta_data = [];
    foreach ($meta_keys as $key) {
        $meta_data[$key] = get_user_meta($id, $key, true);
    }

    $meta_data['email'] = $email;

    // Retrieve all active subscriptions for the user, including subscription names
    $user_subscriptions = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT us.subscription_id AS id, us.status, us.end_date, s.name
             FROM $user_subscriptions_table AS us
             INNER JOIN $subscriptions_table AS s ON us.subscription_id = s.id
             WHERE us.user_id = %d AND (us.end_date IS NULL OR us.end_date > NOW())",
            $id
        ),
        ARRAY_A
    );

    // Add subscription details to the meta data
    $meta_data['subscriptions'] = $user_subscriptions ? $user_subscriptions : [];

    // Return user details with subscription info
    return rest_response_success(200, 'Successfully retrieved user details', $meta_data);
}

function register_get_ai_subscriber_details_route() {
    register_rest_route('yooker-ai-admin/v1', '/ai-subscriber-details/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'ai_subscriber_details',
        'permission_callback' => 'yooker_check_rest_permissions',
    ));
}
add_action('rest_api_init', 'register_get_ai_subscriber_details_route');
?>