<?php
function yooker_check_rest_permissions( $request ) {
    $xwpnonce = $request->get_header( 'X-WP-Nonce' );
    
    if ( !empty( $xwpnonce ) ) {
        if ( wp_verify_nonce( $xwpnonce, 'wp_rest' ) ) {
            return true;
        }
    }
    return false;
}
function check_basic_auth_permission($request) {
    $authorization = $request->get_header('authorization');
    
    if (!empty($authorization)) {
        $authorizationarray = explode('Basic ', $authorization);

        if (count($authorizationarray) === 2) {
            $credentialsarray = explode(':', base64_decode($authorizationarray[1]));

            if (count($credentialsarray) === 2) {
                $user = wp_authenticate_application_password(null, $credentialsarray[0], $credentialsarray[1]);

                if (isset($user->ID)) {
                    // Store the authenticated user in the request
                    $request->set_param('authenticated_user', $user);
                    return true;
                } else {
                    return new WP_Error('invalid_credentials', 'Invalid application credentials', array('status' => 403));
                }
            }
        }
    }
    
    return new WP_Error('missing_authorization_header', 'Authorization header is missing or invalid', array('status' => 403));
}


function check_basic_or_nonce_permission(WP_REST_Request $request) {
    // Check if the nonce is provided (admin requests)
    $xwpnonce = $request->get_header('X-WP-Nonce');
    if (!empty($xwpnonce)) {
        if (wp_verify_nonce($xwpnonce, 'wp_rest')) {
            return true; // Valid nonce for admin users
        } else {
            return new WP_Error('rest_forbidden', 'Invalid nonce', array('status' => 403));
        }
    }

    // Check for Authorization header (client plugin requests)
    $authorization = $request->get_header('authorization');
    if (!empty($authorization)) {
        $authorizationarray = explode('Basic ', $authorization);

        if (count($authorizationarray) === 2) {
            $credentialsarray = explode(':', base64_decode($authorizationarray[1]));

            if (count($credentialsarray) === 2) {
                $user = wp_authenticate_application_password(null, $credentialsarray[0], $credentialsarray[1]);

                if (isset($user->ID)) {
                    // Store the authenticated user in the request
                    $request->set_param('authenticated_user', $user);
                    return true; // Valid credentials for client users
                } else {
                    return new WP_Error('invalid_credentials', 'Invalid application credentials', array('status' => 403));
                }
            }
        }
    }

    return new WP_Error('missing_authorization', 'Authorization or nonce is missing or invalid', array('status' => 403));
}
?>