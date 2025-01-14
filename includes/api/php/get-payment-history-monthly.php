<?php
function get_payment_history_monthly(WP_REST_Request $request) {
    global $wpdb;

    $tablename = $wpdb->prefix . 'ai_payment_table';

    $user = $request->get_param('authenticated_user');
    $user_id = $user->ID;

    if (empty($user_id)) {
        return rest_response_fail(401, 'User not authenticated');
    }

    $page = intval($request->get_param('page')) ?: 1; 
    $per_page = intval($request->get_param('per_page')) ?: 12; 

    $offset = ($page - 1) * $per_page;

    // Retrieve the payment history for the authenticated user, grouped by sub_type and month
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT sub_type, SUM(value) as total_value, MONTH(timestamp) as payment_month, YEAR(timestamp) as payment_year
            FROM $tablename
            WHERE user_id = %d
            GROUP BY sub_type, payment_month, payment_year
            ORDER BY timestamp DESC
            LIMIT %d OFFSET %d",
            $user_id,
            $per_page,
            $offset
        )
    );

    // Handle empty results
    if (empty($results)) {
        return rest_response_success(200, 'No payment history found for this user');
    }

    // Prepare the data for response
    $payments = array_map(function($payment) {
        return array(
            'sub_type' => $payment->sub_type,
            'total_value' => $payment->total_value,
            'month' => $payment->payment_month,
            'year' => $payment->payment_year,
        );
    }, $results);

    return rest_response_success(200, 'Payment history retrieved successfully', $payments);
}

function register_get_monthly_payment_history_route() {
    register_rest_route('yooker-ai-admin/v1', '/get-payment-history-monthly/', array(
        'methods' => 'GET',
        'callback' => 'get_payment_history_monthly',
        'permission_callback' => 'check_basic_auth_permission',
    ));
}
add_action('rest_api_init', 'register_get_monthly_payment_history_route');
?>