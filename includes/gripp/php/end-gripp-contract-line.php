<?php
function end_gripp_contract_line($clientNumber, $gripp_product_id, $endDate) {
    require_once('api.class.php');

    if (!$clientNumber) { 
        return rest_response_fail(428, 'No clientNumber found'); 
    } 

    $token = get_option('grippAPIKEY');

    if (!$token) {
        return rest_response_fail(500, 'Gripp API Key is missing or not configured.');
    }
    $API = new gripp_API($token, 'https://api.gripp.com/public/api3.php');

    $companyResponse = $API->company_getone([
        ["field" => "company.id", "operator" => "equals", "value" => $clientNumber]
    ]);
    
    if (empty($companyResponse[0]['result']['rows'])) { 
        return rest_response_fail(404, 'Company not found'); 
    }

    $companyName = $companyResponse[0]['result']['rows'][0]['companyname'];

    $contractResponse = $API->contract_getone([
        [
            "field" => "contract.name",
            "operator" => "like",
            "value" => 'Contract ' . $companyName . ' AI%'
        ],
        [
            "field" => "contract.status",
            "operator" => "equals",
            "value" => 1 
        ]
    ]);

    if (empty($contractResponse[0]['result']['rows'])) { 
        return rest_response_fail(404, 'Contract not found');
    }

    $contractId = $contractResponse[0]['result']['rows'][0]['id'] ?? null;

    $contractLineId = check_contractline($API, $contractId, $gripp_product_id);
    if ($contractLineId) {
        $updatedFields = [
            "enddate" => $endDate,
        ];

        $contractLineCreateResponse = $API->contractline_update($contractLineId, $updatedFields);
        if ($contractLineCreateResponse) {
            return true; 
        }
        else {
            return rest_response_fail(500, 'Failed to update contract line');
        }
    } else {
        return rest_response_fail(404, 'Contract line not found');
    }


   function check_contractline($API, $contractId, $productId) {
        $filters = [
            ["field" => "contractline.contract", "operator" => "equals", "value" => $contractId],
            ["field" => "contractline.product", "operator" => "equals", "value" => $productId]
        ];
        $response = $API->contractline_getone($filters);
    
        // Return contract line ID if found, or null
        return $response[0]['result']['rows'][0]['id'] ?? null;
    }
}
?>