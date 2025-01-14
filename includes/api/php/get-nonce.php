<?php
// Function to generate nonce
function get_nonce_for_testing() {
    $nonce = wp_create_nonce('wp_rest'); 
    return rest_ensure_response(['nonce' => $nonce]);
}

function yooker_validate_nonce_permission_callback() {
    $test_access = isset($_SERVER['HTTP_X_TEST_ACCESS']) ? sanitize_text_field($_SERVER['HTTP_X_TEST_ACCESS']) : '';

    if ($test_access !== 'onlyForTestPurpose') {
        error_log('Permission Denied: Invalid Test Access Header.');
        return false; 
    }

    // Get allowed IPs from environment variable
    $allowed_ips = getenv('ALLOWED_IPS') ? explode(',', getenv('ALLOWED_IPS')) : [];

    // Default to localhost (for testing locally)
    $user_ip = $_SERVER['REMOTE_ADDR'];

    if ($user_ip === '127.0.0.1' || $user_ip === '::1') {
        return true;
    }

    // If the IP is not in the allowed list, deny access
    if (!in_array($user_ip, $allowed_ips)) {
        error_log('Permission Denied: IP address is not allowed.');
        return false; 
    }

    return true;
}


function register_get_nonce_route(){
    register_rest_route('yooker-ai-admin/v1', '/get-nonce', [
        'methods' => 'GET',
        'callback' => 'get_nonce_for_testing',
        'permission_callback' => 'yooker_validate_nonce_permission_callback',
    ]);
}

add_action('rest_api_init', 'register_get_nonce_route');
?>