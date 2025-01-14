<?php
function admin_end_subscription(WP_REST_Request $request) {
    global $wpdb;

    $user_id = $request->get_param('user_id');
    if (!$user_id) {
        return rest_response_fail(400, 'Missing user ID');
    }

    // Validate the user exists
    $user = get_user_by('ID', $user_id);
    if (!$user) {
        return rest_response_fail(404, 'User not found');
    }

    $clientNumberString = get_user_meta($user_id, 'clientnumber_gripp', true);
    if (!$clientNumberString) {
        return rest_response_fail(404, 'Client number not found for the user. Contact support');
    }

    $clientNumber = (int)$clientNumberString;
    $subscription_id = $request->get_param('subscription_id');
    if (!$subscription_id) {
        return rest_response_fail(400, 'Missing subscription ID');
    }

    // Check for subscription existence
    $table_name = $wpdb->prefix . 'ai_user_subscriptions_table';
    $subscription = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE subscription_id = %d AND user_id = %d",
        $subscription_id,
        $user_id
    ));

    if (!$subscription) {
        return rest_response_fail(404, 'Subscription not found');
    }

    // Calculate the end date
    $start_date = new DateTime($subscription->start_date);
    $end_date = calculate_end_date($start_date);

    // Update subscription in the database
    $updated = update_subscription_end_date($wpdb, $table_name, $subscription_id, $user_id, $end_date);
    if (!$updated) {
        return rest_response_fail(500, 'Failed to update subscription end date');
    }

    // Handle Gripp contract ending
    $gripp_result = handle_gripp_end_contract($clientNumber, $subscription_id, $end_date);
    if ($gripp_result instanceof WP_Error || $gripp_result !== true) {
        return $gripp_result;
    }

    return rest_response_success(200, 'Subscription updated successfully to match the end of the current billing cycle.');
}


/* ------------------------------------------------------------------------------------------------------------*/

function register_admin_end_subscription_route() {
    register_rest_route('yooker-ai-admin/v1', '/admin-end-subscription/', [
        'methods' => 'POST',
        'callback' => 'admin_end_subscription',
        'permission_callback' => 'yooker_check_rest_permissions',
    ]);
}

add_action('rest_api_init', 'register_admin_end_subscription_route');
?>