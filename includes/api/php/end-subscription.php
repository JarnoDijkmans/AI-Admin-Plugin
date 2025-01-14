<?php
require_once dirname(__DIR__, 2) . '/gripp/php/end-gripp-contract-line.php';

function end_subscription(WP_REST_Request $request) {
    global $wpdb;

    $user = $request->get_param('authenticated_user');
    if (!$user || !isset($user->ID)) {
        return rest_response_fail(403, 'User not authenticated');
    }

    $user_id = $user->ID;

    $activated = get_user_meta($user_id, 'activated', true);
    if ($activated !== '1') {
        return rest_response_fail(403, 'User not activated');
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

function handle_gripp_end_contract($clientNumber, $subscription_id, $end_date) {
    $gripp_product_id = connect_productIds($subscription_id);
    if (!$gripp_product_id) {
        return rest_response_fail(400, 'Failed to find associated Gripp product ID.');
    }

    $gripp_response = end_gripp_contract_line($clientNumber, $gripp_product_id, $end_date->format('Y-m-d'));

    if ($gripp_response instanceof WP_Error) {
        return $gripp_response;
    }

    if ($gripp_response !== true) {
        return rest_response_fail(500, 'Failed to update subscription in Gripp.');
    }

    return true; 
}

/* ------------------------------------------------------------------------------------------------------------*/

function calculate_end_date($start_date) {
    $current_date = new DateTime(current_time('mysql', 1));
    $end_date = clone $start_date;

    while ($end_date <= $current_date) {
        $end_date->modify('+1 month');
    }

    return $end_date;
}

/* ------------------------------------------------------------------------------------------------------------*/

function update_subscription_end_date($wpdb, $table_name, $subscription_id, $user_id, $end_date) {
    return $wpdb->update(
        $table_name,
        ['end_date' => $end_date->format('Y-m-d H:i:s'), 'status' => 2],
        ['subscription_id' => $subscription_id, 'user_id' => $user_id],
        ['%s', '%d'],
        ['%d', '%d']
    );
}

/* ------------------------------------------------------------------------------------------------------------*/

function register_end_subscription_route() {
    register_rest_route('yooker-ai-admin/v1', '/end-subscription/', [
        'methods' => 'POST',
        'callback' => 'end_subscription',
        'permission_callback' => 'check_basic_auth_permission',
    ]);
}

add_action('rest_api_init', 'register_end_subscription_route');
?>
