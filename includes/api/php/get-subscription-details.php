<?php
function get_subscription_details(WP_REST_Request $request) {
    global $wpdb;

    // Get subscription ID from request
    $subscription_id = $request->get_param('subscription_id');

    if ($subscription_id) {
        // Define your table name
        $subscription_table = $wpdb->prefix . 'ai_subscriptions_table';

        // Prepare and execute query to get subscription details
        $subscription_details = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $subscription_table WHERE id = %d", 
                $subscription_id
            )
        );

        // Check if any results were found
        if ($subscription_details) {
            return rest_response_success(200, 'Successfully retrieved subscription details', $subscription_details);
        } else {
            return  rest_response_fail(404, 'No subscription found with this ID');
        }
    } else {
        return rest_response_fail(400, 'No subscription ID provided');
    }
}


function register_get_subscription_details_route() {
    register_rest_route('yooker-ai-admin/v1', '/get-subscription-details/(?P<subscription_id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_subscription_details',
        'permission_callback' => '__return_true',
    ));    
}
add_action('rest_api_init', 'register_get_subscription_details_route');
?>