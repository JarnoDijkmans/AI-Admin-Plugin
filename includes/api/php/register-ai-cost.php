<?php
function yooker_register_cost(WP_REST_Request $request) {
    global $wpdb;
    $tablename = $wpdb->prefix . 'ai_payment_table';

    $user = $request->get_param('authenticated_user');
    $user_id = $user->ID;

    if (empty($user_id)) {
        return rest_response_fail(401, 'User not authenticated');
    }

    // Sanitize and get parameters
    $post_title = sanitize_text_field($request->get_param('post_title'));
    $post_type = sanitize_text_field($request->get_param('post_type'));
    $sub_type = sanitize_textarea_field($request->get_param('sub_type'));
    $value = sanitize_text_field($request->get_param('value'));

    error_log('post_title: ' . print_r( $post_title, true));
    error_log('post_type: ' . print_r( $post_type, true));
    error_log('sub_type: ' . print_r( $sub_type, true));
    error_log('value: ' . print_r( $value, true));

    // Validate required fields
    if (empty($post_title) || empty($value)) {
        return rest_response_fail(400, 'postTitle and value are required.');
    }
    
    $current_date = current_time('Y-m-d');
    
    $current_value = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT value FROM $tablename WHERE post_name = %s AND timestamp = %s",
            $post_title,
            $current_date
        )
    );

    $new_value = floatval($value);

    if ($current_value === null) {
        // No entry for today, insert a new record
        $inserted = $wpdb->insert(
            $tablename,
            array(
                'user_id' => $user_id,
                'post_name' => $post_title,
                'post_type' => $post_type,
                'sub_type' => $sub_type,
                'value' => $new_value,
                'timestamp' => $current_date
            )
        );

        if ($inserted) {
            return rest_response_success(201, 'First payment recorded successfully');
        } else {
            return rest_response_fail(500, 'Failed to create payment');
        }
    } else {
        // Entry exists for today, update the existing record
        $current_value = floatval($current_value);
        
        // Calculate the updated total value
        $updated_value = $current_value + $new_value;

        // Update the existing record
        $updated = $wpdb->update(
            $tablename,
            array(
                'user_id' => $user_id,
                'post_name' => $post_title,
                'post_type' => $post_type,
                'sub_type' => $sub_type,
                'value' => $updated_value,
                'timestamp' => $current_date
            ),
            array(
                'post_name' => $post_title,
                'user_id' => $user_id,
                'timestamp' => $current_date
            )
        );

        if ($updated) {
            return rest_response_success(200, 'Total cost updated successfully');
        } else {
            return rest_response_fail(500, 'Failed to update payment');
        }
    }
}

function register_cost_route() {
    register_rest_route('yooker-ai-admin/v1', '/register-cost/', array(
        'methods' => 'POST',
        'callback' => 'yooker_register_cost',
        'permission_callback' => 'check_basic_auth_permission',
    ));
}

add_action('rest_api_init', 'register_cost_route');
?>