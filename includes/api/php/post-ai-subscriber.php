<?php
function yooker_save_user(WP_REST_Request $request) {
    global $wpdb;

    $user_table = $wpdb->prefix . 'users';
    $user_meta_table = $wpdb->prefix . 'usermeta';

    // Sanitize input data
    $id = $request->get_param('id');  
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
    $clientnumber_gripp = sanitize_text_field($request->get_param('clientnumber_gripp'));

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
        'clientnumber_gripp' => $clientnumber_gripp,
    ];

    if (empty($email)) {
        return rest_response_fail(400, 'Email is required.');
    }

    if (empty($company)) {
        return rest_response_fail(400, 'Company is required.');
    }

    $user = get_user_by( 'ID', $id );

    if ($user) {
        // Get the current email and company of the user being updated
        $current_email = $wpdb->get_var($wpdb->prepare("SELECT user_email FROM $user_table WHERE ID = %d", $id));
        $current_company = $wpdb->get_var($wpdb->prepare(
            "SELECT meta_value FROM $user_meta_table WHERE user_id = %d AND meta_key = %s",
            $id, 'company'
        ));

        if ($email !== $current_email && email_exists_for_other_user($email, $id)) {
            return rest_response_fail(409, 'Email already exists for another user.');
        }

        if ($company !== $current_company && company_exists_for_other_user($company, $id)) {
            return rest_response_fail(409, 'Company name already exists for another user.');
        }

        $updated = update_user_fields($id, $email, $user_meta);

        if ($updated) {
            return rest_response_success(200, 'User updated successfully'); 
        } else {
            return rest_response_fail(500, 'Failed to update user');
        }
    } else { 
        // Create a new user
        $subscriptions = json_encode([]);
        $user_meta['subscriptions'] = $subscriptions;

        if (email_exists($email)) {
            return rest_response_fail(409, 'Email already exists for another user');
        }
        if (company_exists($company)) {
            return rest_response_fail(409, 'Company name already exists for another user.');
        }

        $pass = wp_generate_password(12, true, true);
        $user_id = wp_create_user($company, $pass, $email);
        (new WP_User($user_id))->set_role('ai_subscriber');

        $updated = update_user_fields($user_id, $email, $user_meta);

        if ($updated) {
            $app_exists = WP_Application_Passwords::application_name_exists_for_user($user_id, 'ai-subscriber-import');
            if (!$app_exists) {
                
                $to = 'jarno.dijkmans@yooker.nl';
                $subject = "Yooker AI - Account activeren: {$company}.";
                $message = "Een nieuw account aangemaakt voor {$company}.\n\n";
                $message .= "Username: {$company}\n";
                $message .= "Application Password: {$app_pass[0]}\n\n";
                $message .= "Bewaar dit wachtwoord op een veilige plek, want het wordt niet opnieuw weergegeven.";
        
                if (!send_mail($to, $company, $subject, $message)) {
                    throw new Exception('Failed to send activation email.');
                }
                $app_pass = WP_Application_Passwords::create_new_application_password($user_id, array('name' => 'ai-subscriber-import'));
                if (!is_wp_error($app_pass)) {
                    rest_response_success(201, 'User and Application password created successfully.', $app_pass);
                } else {
                    rest_response_fail(500, 'Error creating application password: ' . $app_pass->get_error_message());
                }
            } else {
                rest_response_fail(409, 'Application password already exists for user.');
            }
        }
    }

    return rest_ensure_response($response);
}

function register_post_ai_subscriber_route() {
    register_rest_route('yooker-ai-admin/v1', '/post-ai-subscriber/', array(
        'methods' => 'POST',
        'callback' => 'yooker_save_user',
        'permission_callback' => 'yooker_check_rest_permissions',  
    ));
}

add_action('rest_api_init', 'register_post_ai_subscriber_route');
?>