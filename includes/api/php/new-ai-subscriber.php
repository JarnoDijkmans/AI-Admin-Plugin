<?php
function yooker_create_user(WP_REST_Request $request) {
    global $wpdb;

    $email = sanitize_email($request->get_param('email'));
    $first_name = sanitize_text_field($request->get_param('first_name'));
    $last_name = sanitize_text_field($request->get_param('last_name'));
    $surnameprefix = sanitize_text_field($request->get_param('surnameprefix'));
    $company = sanitize_text_field($request->get_param('company'));
    $phonenumber = sanitize_text_field($request->get_param('phonenumber'));
    $address = sanitize_text_field($request->get_param('address'));
    $zipcode = sanitize_text_field($request->get_param('zipcode'));
    $town = sanitize_text_field($request->get_param('town'));
    $country = sanitize_text_field($request->get_param('country'));
    $subscriptions = json_encode([]);

    if (empty($email)) {
        return rest_response_fail(404, 'Email is required.');
    }

    if (empty($company)) {
        return rest_response_fail(404, 'Company field is required.');
    }

    if (email_exists($email)) {
        return rest_response_fail(409, 'Email already exists for another user.');
    }

    if (company_exists($company)) {
        return rest_response_fail(409, 'Company name already exists for another user.');
    }

    $pass = wp_generate_password(12, true, true);
    $user_id = wp_create_user($company, $pass, $email);

    if (is_wp_error($user_id)) {
        return rest_response_fail(500, 'Failed to create user account: ' . $user_id->get_error_message());
    }

    try {
        // Set the role
        (new WP_User($user_id))->set_role('ai_subscriber');

        // Update user meta
        $user_meta = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'surnameprefix' => $surnameprefix,
            'company' => $company,
            'phonenumber' => $phonenumber,
            'address' => $address,
            'zipcode' => $zipcode,
            'town' => $town,
            'country' => $country,
            'subscriptions' => $subscriptions,
            'activated' => 0,
        ];
        $updated = update_user_fields($user_id, $email, $user_meta);

        if (!$updated) {
            throw new Exception('Failed to update user fields.');
        }

        // Create application password
        $app_pass = WP_Application_Passwords::create_new_application_password($user_id, ['name' => 'ai-subscriber-import']);
        if (is_wp_error($app_pass)) {
            throw new Exception('Error creating application password: ' . $app_pass->get_error_message());
        }

        // Send activation email
        $to = 'jarno.dijkmans@yooker.nl';
        $subject = "Yooker AI - Account activeren: {$company}.";
        $title = "Yooker AI - Nieuw account aangemaakt: {$company}.";
        $intro = "{$company} heeft een nieuw account aangemaakt. Graag account zo snel mogelijk activeren.";
        $username = "Username: {$company}";
        $apiKey = "API Key: {$app_pass[0]}";
        $outro = "Stuur een notificatie naar {$company} zodra het account is geactiveerd.";

        if (!send_mail($to, $subject, $title, $intro, $username, $apiKey, $outro)) {
            throw new Exception('Failed to send activation email.');
        }

        $to = $email;
        $subject = "Yooker AI - Account gegevens: {$company}.";
        $title = "Account aangemaakt!";
        $intro = "Welkom bij Yooker AI! Momenteel zijn we bezig met het activeren van uw account. Hieronder vindt u uw login gegevens:";
        $username = "Username: {$company}";
        $apiKey = "API key: {$app_pass[0]}";
        $outro = "Bewaar dit wachtwoord op een veilige plek, want het wordt niet opnieuw weergegeven.";

        if (!send_mail($to, $subject, $title, $intro, $username, $apiKey, $outro)) {
            throw new Exception('Failed to send activation email.');
        }

        $details = [
            'apppass' => $app_pass[0],
            'username' => $company,
        ];

        return rest_response_success(201, 'User and application password created successfully.', $details);

    } catch (Exception $e) {
        wp_delete_user($user_id);
        return rest_response_fail(500, $e->getMessage());
    }
}

function register_new_post_ai_subscriber_route() {
    register_rest_route('yooker-ai-admin/v1', '/new-subscriber/', array(
        'methods' => 'POST',
        'callback' => 'yooker_create_user',
        'permission_callback' => '__return_true',  
    ));
}

add_action('rest_api_init', 'register_new_post_ai_subscriber_route');
?>