<?php
function get_payment_history(WP_REST_Request $request) {
    global $wpdb;

    $tablename = $wpdb->prefix . 'ai_payment_table';

    $user = $request->get_param('authenticated_user');
    $user_id = $user->ID;

    if (empty($user_id)) {
        return rest_response_fail(401, 'User not authenticated');
    }

    $page = intval($request->get_param('page')) ?: 1; 
    $per_page = intval($request->get_param('per_page')) ?: 10; 

    $offset = ($page - 1) * $per_page;

    // Retrieve the payment history for the authenticated user
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $tablename WHERE user_id = %d ORDER BY timestamp DESC LIMIT %d OFFSET %d",
            $user_id,
            $per_page,
            $offset
        )
    );

    // Handle empty results
    if (empty($results)) {
        return rest_response_fail(200, 'No payment history found for this user');
    }

    $payments_by_date = [];
    foreach ($results as $payment) {
        $date = date('Y-m-d', strtotime($payment->timestamp));
        
        if (!isset($payments_by_date[$date])) {
            $payments_by_date[$date] = [];
        }

        // Check if a payment with the same post_name and post_type already exists for this date
        $found = false;
        foreach ($payments_by_date[$date] as &$existing_payment) {
            if ($existing_payment['post_name'] === $payment->post_name && $existing_payment['post_type'] === $payment->post_type) {
                // Add the values together if the same post_name and post_type are found
                $existing_payment['value'] += $payment->value;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $payments_by_date[$date][] = array(
                'post_id' => $payment->post_id,
                'post_name' => $payment->post_name,
                'post_type' => $payment->post_type,
                'sub_type' => $payment->sub_type,
                'value' => $payment->value,
                'timestamp' => $payment->timestamp,
            );
        }
    }

    return rest_response_success(200, 'Payment History retrieved successfully', $payments_by_date);
}

function register_get_payment_history_route() {
    register_rest_route('yooker-ai-admin/v1', '/get-payment-history/', array(
        'methods' => 'GET',
        'callback' => 'get_payment_history',
        'permission_callback' => 'check_basic_auth_permission',
    ));
}
add_action('rest_api_init', 'register_get_payment_history_route');
?>