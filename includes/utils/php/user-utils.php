<?php

/**
 * Check if an email exists for a user other than the given user ID.
 *
 * @param string $email The email to check.
 * @param int|null $user_id The ID of the user being updated (optional).
 * @return bool True if email exists for another user, false otherwise.
 */
function email_exists_for_other_user($email, $user_id) {
    $existing_user_id = email_exists($email);
    return $existing_user_id && $existing_user_id != $user_id;
}


/**
 * Check if a company name exists for a user other than the given user ID.
 *
 * @param string $company The company name to check.
 * @return bool True if company exists for another user, false otherwise.
 */
function company_exists($company) {
    global $wpdb;
    $user_meta_table = $wpdb->prefix . 'usermeta';

    $existing_company_user_id = $wpdb->get_var($wpdb->prepare(
        "SELECT user_id FROM $user_meta_table WHERE meta_key = 'company' AND meta_value = %s",
        $company
    ));

    // Return true if a user with this company name exists, false otherwise
    return $existing_company_user_id ? true : false;
}


/**
 * Check if a company name exists for a user other than the given user ID.
 *
 * @param string $company The company name to check.
 * @param int|null $user_id The ID of the user being updated (optional).
 * @return bool True if company exists for another user, false otherwise.
 */
function company_exists_for_other_user($company, $user_id) {
    global $wpdb;
    $user_meta_table = $wpdb->prefix . 'usermeta';

    $existing_company_id = $wpdb->get_var($wpdb->prepare(
        "SELECT user_id FROM $user_meta_table WHERE meta_key = 'company' AND meta_value = %s",
        $company
    ));

    return $existing_company_id && $existing_company_id != $user_id;
}

/**
 * Update user meta fields and email in bulk.
 *
 * @param int $user_id The ID of the user.
 * @param string $email The email of the user.
 * @param array $meta The associative array of meta keys and values.
 * @return bool|WP_Error True on success, WP_Error on failure.
 */
function update_user_fields($user_id, $email, $meta) {
    $user_data = array(
        'ID' => $user_id,
        'user_email' => $email,
    );

    wp_update_user($user_data);

    foreach ($meta as $key => $value) {
        $current_value = get_user_meta($user_id, $key, true);
        if ($current_value != $value) {  // Only attempt update if the value is different
            $meta_update_result = update_user_meta($user_id, $key, $value);
        } 
    }

    return true; 
}


/**
 * Check and update user subscription status for expired subscriptions.
 *
 * @param int $user_id The ID of the user.
 * @return void
 */
function check_user_subscription_status($user_id) {
    global $wpdb;
    $user_subscriptions_table = $wpdb->prefix . 'ai_user_subscriptions_table';

    // Update all expired subscriptions for the user in a single query
    $wpdb->query(
        $wpdb->prepare(
            "UPDATE $user_subscriptions_table
             SET status = 3
             WHERE user_id = %d AND end_date < NOW()",
            $user_id
        )
    );
}


/**
 * Success response utility function.
 *
 * @param string $message The success message.
 * @param int $status The status code (default is 200).
 * @param array $data Additional data to return in the response.
 * @return WP_REST_Response The response object.
 */
function success_response($message, $status = 200, $data = []) {
    return new WP_REST_Response(array_merge(['success' => true, 'message' => $message], $data), $status);
}

/**
 * Error response utility function.
 *
 * @param string $message The error message.
 * @param int $status The status code (default is 400).
 * @return WP_REST_Response The response object.
 */
function error_response($message, $status = 400) {
    return new WP_REST_Response(['success' => false, 'message' => $message], $status);
}

