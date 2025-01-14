<?php
function yooker_save_subscription(WP_REST_Request $request) {
    global $wpdb;
    $tablename = $wpdb->prefix . 'ai_subscriptions_table';

    // Sanitize input data
    $id = sanitize_text_field($request->get_param('id'));
    $name = sanitize_text_field($request->get_param('name'));
    $short_description = sanitize_textarea_field($request->get_param('short_description'));
    $price = sanitize_text_field($request->get_param('price'));
    $version = sanitize_text_field($request->get_param('version'));
    $author = sanitize_text_field($request->get_param('author'));
    $long_description = sanitize_textarea_field($request->get_param('long_description'));
    $options = wp_json_encode(array_map('sanitize_text_field', $request->get_param('options'))); 
    $model = wp_json_encode(array_map('sanitize_text_field', $request->get_param('model')));   

    // Validation
    if (empty($name)) {
        return rest_response_fail(400, 'The subscription name is required.');
    }
    if (!is_numeric($price)) {
        return rest_response_fail(400, 'The price must be a valid number.');
    }

    // Prepare data
    $subscription_data = array(
        'name' => $name,
        'short_description' => $short_description,
        'price' => $price,
        'version' => $version,
        'author' => $author,
        'long_description' => $long_description,
        'options' => $options,
        'model' => $model
    );

    // Update or Insert
    if ($id) {
        $subscription_exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $tablename WHERE id = %d", $id));

        if (!$subscription_exists) {
            return rest_response_fail(404, 'Subscription not found.');
        }

        $updated = $wpdb->update($tablename, $subscription_data, array('id' => $id));

        if ($updated !== false) {
            return rest_response_success(200, 'Subscription updated successfully.');
        } else {
            error_log("Failed to update subscription with ID {$id}: " . $wpdb->last_error);
            return rest_response_fail(500, 'Failed to update subscription.');
        }
    } else {
        $wpdb->query('START TRANSACTION');
        $inserted = $wpdb->insert($tablename, $subscription_data);

        if ($inserted) {
            $wpdb->query('COMMIT');
            $new_subscription = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $tablename WHERE id = %d", $wpdb->insert_id
            ), ARRAY_A);

            return rest_response_success(201, 'Subscription created successfully.', $new_subscription);
        } else {
            $wpdb->query('ROLLBACK');
            error_log("Failed to create subscription: " . $wpdb->last_error);
            return rest_response_fail(500, 'Failed to create subscription.');
        }
    }
}

function register_post_subscription_route() {
    register_rest_route('yooker-ai-admin/v1', '/subscriptions/', array(
        'methods' => 'POST',
        'callback' => 'yooker_save_subscription',
        'permission_callback' => 'yooker_check_rest_permissions',  
    ));
}

add_action('rest_api_init', 'register_post_subscription_route');
?>