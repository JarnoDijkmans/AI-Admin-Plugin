<?php
require_once dirname(__DIR__, 2) . '/gripp/php/gripp-contract-line.php';

function new_subscription_for_user(WP_REST_Request $request) {
    global $wpdb;
    $tablename = $wpdb->prefix . 'ai_user_subscriptions_table';
    $user_meta_table = $wpdb->prefix . 'usermeta';

    // Validate authenticated user
    $user = $request->get_param('authenticated_user');
    if (empty($user->ID)) {
        return rest_response_fail(401, 'User not authenticated');
    }

    $user_id = $user->ID;

    // Fetch user meta data
    $user_meta = fetch_user_meta($wpdb, $user_meta_table, $user_id);
    if (!$user_meta) {
        return rest_response_fail(404, 'User meta data not found');
    }

    $activated = $user_meta->activated;
    $clientnumber_gripp = $user_meta->clientnumber_gripp;
    $clientNumber = is_numeric($clientnumber_gripp) ? (int)$clientnumber_gripp : 0;
    
    if ($activated !== "1") {
        return rest_response_fail(403, 'Account not activated');
    }
    
    // Check if $clientNumber is invalid or zero
    if (empty($clientNumber) || $clientNumber === 0) {
        return rest_response_fail(400, 'Invalid Gripp client number');
    }

    $subscription_id = $request->get_param('subscription_id');

    // Check existing subscription
    $existing_subscription = get_existing_subscription($wpdb, $tablename, $user_id, $subscription_id);

    if ($existing_subscription) {
        if ($existing_subscription->status == 1) {
            return rest_response_fail(409, 'User already subscribed to this subscription');
        }

        if ($existing_subscription->status == 2) {
            update_subscription($wpdb, $tablename, $user_id, $subscription_id);
            return rest_response_success(200, 'Subscription updated successfully');
        }
    }

    // Handle Gripp contract validation
    $gripp_result = handle_gripp_contract($clientNumber, $subscription_id);
    if ($gripp_result instanceof WP_REST_Response) {
        return $gripp_result;
    }

    // Create new subscription
    return create_subscription($wpdb, $tablename, $user_id, $subscription_id);
}

/*---------------------------------------------------------------------------------*/


function fetch_user_meta($wpdb, $user_meta_table, $user_id) {
    return $wpdb->get_row(
        $wpdb->prepare(
            "SELECT 
                MAX(CASE WHEN meta_key = 'activated' THEN meta_value END) AS activated, 
                MAX(CASE WHEN meta_key = 'clientnumber_gripp' THEN meta_value END) AS clientnumber_gripp
             FROM $user_meta_table 
             WHERE user_id = %d",
            $user_id
        )
    );
}

/*--------------------------------------------------------------------------------*/

function get_existing_subscription($wpdb, $tablename, $user_id, $subscription_id) {
    return $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $tablename WHERE user_id = %d AND subscription_id = %d AND status IN (1, 2)",
        $user_id, $subscription_id
    ));
}

/*--------------------------------------------------------------------------------*/

function handle_gripp_contract($clientNumber, $subscription_id) {
    $gripp_product_id = connect_productIds($subscription_id);
    if (!$gripp_product_id) {
        return rest_response_fail(400, 'Invalid subscription ID or Gripp product not found');
    }

    $gripp_contract = gripp_contract_line($clientNumber, $gripp_product_id);

    if ($gripp_contract instanceof WP_REST_Response) {
        return $gripp_contract; 
    }

    return true; 
}

/*---------------------------------------------------------------------------------*/

function update_subscription($wpdb, $tablename, $user_id, $subscription_id) {
    $wpdb->update(
        $tablename,
        ['end_date' => null, 'status' => 1],
        ['subscription_id' => $subscription_id, 'user_id' => $user_id],
        ['%s', '%d'],
        ['%d', '%d']
    );
}

/*---------------------------------------------------------------------------------*/

function create_subscription($wpdb, $tablename, $user_id, $subscription_id) {
    $start_date = current_time('mysql', 1);
    $inserted = $wpdb->insert(
        $tablename,
        [
            'user_id' => $user_id,
            'subscription_id' => $subscription_id,
            'start_date' => $start_date,
            'end_date' => null,
            'status' => 1,
        ],
        ['%d', '%d', '%s', '%s', '%d']
    );

    if ($inserted === false) {
        return rest_response_fail(500, 'Failed to add new subscription.');
    }

    return rest_response_success(201, 'Successfully subscribed to Yooker AI extension', [
        'subscription_id' => $subscription_id,
        'start_date' => $start_date,
    ]);
}

/*---------------------------------------------------------------------------------*/

function register_new_subscription_route() {
    register_rest_route('yooker-ai-admin/v1', '/new-subscription/', [
        'methods' => 'POST',
        'callback' => 'new_subscription_for_user',
        'permission_callback' => 'check_basic_auth_permission',
    ]);
}

add_action('rest_api_init', 'register_new_subscription_route');
?>