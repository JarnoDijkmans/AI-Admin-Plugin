<?php
require_once dirname(__DIR__, 2) . '/gripp/php/gripp-contract.php';
function yooker_ai_activate_account(WP_REST_Request $request) {

    global $wpdb;
    $user_table = $wpdb->prefix . 'users';
    $user_meta_table = $wpdb->prefix . 'usermeta';

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
    $activated = sanitize_text_field($request->get_param('activated'));

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
        'activated' => $activated
    ];

    if (empty($email)) {
        return rest_response_fail(400, 'Email is required.');
    }

    if (empty($company)) {
        return rest_response_fail(400, 'Company is required.');
    }

    if ($activated == 1) {
        if (empty($clientnumber_gripp)) {
            return rest_response_fail(400, 'Client number (Gripp) is required for activation.');
        }

        $clientNumber = (int)$clientnumber_gripp;

        $gripp_contract = create_gripp_contract($clientNumber);

        if ($gripp_contract instanceof WP_REST_RESPONSE) {
            return $gripp_contract;
        }

        if (!$gripp_contract) {
            return rest_response_fail(500, 'Failed to create Gripp contract.');
        }
    }

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

    if (!$updated) {
        return rest_response_fail(500, 'Failed to update user fields.');
    }

    return rest_response_success(201, 'User account updated successfully.', ['user_id' => $id]);
}

function register_ai_activate_route() {
    register_rest_route('yooker-ai-admin/v1', '/yooker-ai-activate-account/', array(
        'methods' => 'POST',
        'callback' => 'yooker_ai_activate_account',
        'permission_callback' => 'yooker_check_rest_permissions',  
    ));
}

add_action('rest_api_init', 'register_ai_activate_route');
?>