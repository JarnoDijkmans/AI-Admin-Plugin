<?php
function gripp_contract_line($clientNumber, $productId){
    require_once('api.class.php');

    if (!is_int($clientNumber)) {
        return rest_response_fail(400, 'clientNumber must be an integer.');
    }

    if (!is_int($productId)) {
        return rest_response_fail(400, 'productId must be an integer.');
    }

    if (!$clientNumber) { 
        return rest_response_fail(428, 'No clientNumber found'); 
    } 

    $token = get_option('grippAPIKEY');

    if (!$token) {
        return rest_response_fail(500, 'Gripp API Key is missing or not configured.');
    }

    try {
        $API = new gripp_API($token, 'https://api.gripp.com/public/api3.php');
    } catch (Exception $e) {
        print_r("API initialization error: " . $e->getMessage());
        exit;
    }

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

    if (!$contractId) { 
        return rest_response_fail(404, 'ContractID not found');
    }

    $productResponse = $API->product_getone([
        ["field" => "product.id", "operator" => "equals", "value" => $productId]
    ]);

    if (empty($productResponse[0]['result']['rows'])) { 
        return rest_response_fail(404, 'Product not found');
    }

    $contractLineId = check_contractline($API, $contractId, $productId);

    if (!$contractLineId) {
        $newContractline = [
            "contract" => $contractId,
            "product" => $productId,
            "startdate" => date('Y-m-d'),
            "amount" => 1,
            "buyingprice" => 0.0, 
            "sellingprice" => 2,
            "discount" => 0,
        ];

        error_log(print_r("ContractLine is created!", true));
        $contractLineCreateResponse = $API->contractline_create($newContractline);
        return true; 
    } else {
        $updateContract = [
            "enddate" => null,
        ];

        $contractLineUpdateResponse = $API->contractline_update($contractLineId, $updateContract);
        error_log(print_r("ContractLine is updated!", true));
        return true;
    }
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
?>