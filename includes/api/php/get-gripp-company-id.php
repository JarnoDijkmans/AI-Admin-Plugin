<?php
require_once dirname(__DIR__, 2) . '/gripp/php/gripp-get-company-id.php';

function get_gripp_client_by_id(WP_REST_Request $request) {
    $companyName = $request->get_param('company');

    if (empty($companyName)) {
        return rest_response_fail(400, 'Missing required parameter: company');
    }

    $result = get_gripp_company_id($companyName);

    if (is_int($result) && $result > 0) {
        return rest_response_success(200, "Found company ID: {$result}", $result);
    } elseif ($result instanceof WP_REST_Response) {
        return $result;
    } else {
        return rest_response_fail(404, "No company ID found for: {$result}");
    }
}


// Register the REST route
function register_get_gripp_client() {
    register_rest_route('yooker-ai-admin/v1', '/get-gripp-client', [
        'methods' => 'GET',
        'callback' => 'get_gripp_client_by_id',
        'permission_callback' => 'yooker_check_rest_permissions',
    ]);
}

add_action('rest_api_init', 'register_get_gripp_client');
?>