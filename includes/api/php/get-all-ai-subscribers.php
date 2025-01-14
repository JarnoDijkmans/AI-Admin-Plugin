<?php
function get_all_ai_subscribers(WP_REST_Request $request) {
    global $wpdb;
    $user_meta_table = $wpdb->prefix . 'usermeta';
    $user_table = $wpdb->prefix . 'users';

    $query = "
        SELECT 
            users.ID as user_id, 
            MAX(CASE WHEN meta_key = 'company' THEN meta_value END) as company, 
            MAX(CASE WHEN meta_key = 'address' THEN meta_value END) as address,
            MAX(CASE WHEN meta_key = 'activated' THEN meta_value END) as activated
        FROM $user_table as users
        JOIN $user_meta_table as usermeta
            ON users.ID = usermeta.user_id
        WHERE meta_key IN ('company', 'address', 'activated')
        GROUP BY users.ID
    ";

    $results = $wpdb->get_results($query, ARRAY_A);

    if ($results === false) {
        return rest_response_fail(500, 'Failed to retrieve AI subscribers.');
    }

    return rest_response_success(200, 'AI subscribers retrieved successfully.', $results);
}

function register_get_list_ai_subscribers_route() {
    register_rest_route('yooker-ai-admin/v1', '/list-of-ai-subscribers/', array(
        'methods' => 'GET',
        'callback' => 'get_all_ai_subscribers',
        'permission_callback' => 'yooker_check_rest_permissions',
    ));
}

add_action('rest_api_init', 'register_get_list_ai_subscribers_route');
?>