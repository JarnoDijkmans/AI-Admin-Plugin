<?php
function get_gripp_company_id($companyName) {
    require_once('api.class.php');

    $token = get_option('grippAPIKEY');

    if (!$token) {
        return rest_response_fail(500, 'Gripp API Key is missing or not configured.');
    }

    $API = new gripp_API($token, 'https://api.gripp.com/public/api3.php');

    // Check if company exists
    $companyResponse = $API->company_getone([
        ["field" => "company.searchname", "operator" => "like", "value" => $companyName]
    ]);

    if (!$companyResponse || !isset($companyResponse[0]['result']['rows'])) {
        return rest_response_fail(400, "Could not find a company named something like: {$companyName}");
    }

    // Filter the response to get only the ID
    $rows = $companyResponse[0]['result']['rows'];
    $companyId = null;

    foreach ($rows as $row) {
        if (isset($row['id'])) {
            $companyId = $row['id'];
            break;
        }
    }

    if (!$companyId) {
        return rest_response_fail(404, "No company ID found for: {$companyName}");
    }

    return $companyId; // Return the ID if successful
}
