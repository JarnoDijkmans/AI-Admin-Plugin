<?php
require_once(ABSPATH . 'wp-admin/includes/user.php');

function delete_subscriber(WP_REST_Request $request) {
    $user_id = $request->get_param('id');

    if ($user_id && get_userdata($user_id)) {
            // Attempt to delete the user
        error_log('Deleting user with ID: ' . $user_id);
        if (wp_delete_user($user_id)) {
            return rest_response_success(200, 'User deleted successfully');
        } else {
            return rest_response_fail(500, 'Error deleting the user');
        }
    } else {
        return rest_response_fail(404, 'User not found');
    }
}

function register_delete_subscriber_route() {
    register_rest_route('yooker-ai-admin/v1', '/delete-subscriber/(?P<id>\d+)', array(
        'methods' => 'POST',
        'callback' => 'delete_subscriber',
        'permission_callback' => 'yooker_check_rest_permissions',
    ));
}
add_action('rest_api_init', 'register_delete_subscriber_route');
?>