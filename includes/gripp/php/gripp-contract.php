<?php
function create_gripp_contract($clientNumber) {
    require_once('api.class.php');

    $token = get_option('grippAPIKEY');

    if (!$token) {
        return rest_response_fail(500, 'Gripp API Key is missing or not configured.');
    }

    $API = new gripp_API($token, 'https://api.gripp.com/public/api3.php');

    // Check if company exists
    $companyResponse = $API->company_getone([
        ["field" => "company.id", "operator" => "equals", "value" => $clientNumber]
    ]);
    
    if (empty($companyResponse[0]['result']['rows'])) { 
        return rest_response_fail(401, 'No company found');
    }

    $companyName = $companyResponse[0]['result']['rows'][0]['companyname'];

    $filters = array(
        array(
            "field" => "contract.name",
            "operator" => "like",
            "value" => 'Contract ' . $companyName . ' AI%'
        )
    );

    $options = array(
        "paging" => array(
            "firstresult" => 0,
            "maxresults" => 250
        ),
    );

    $allContractsResponse = $API->contract_get($filters, $options);

    $sequenceNumber = 1;
    $allFinished = true;
   
    if (!empty($allContractsResponse[0]['result']['rows'])) {
        foreach ($allContractsResponse[0]['result']['rows'] as $contract) {
            $contractStatus = $contract['status']['id'] ?? null;
            
            if ($contractStatus !== 3) { 
                $allFinished = false;
                break;
            }

            if (preg_match('/Contract ' . preg_quote($companyName) . ' AI(?: (\d+))?/', $contract['name'], $matches)) {
                $currentSequence = isset($matches[1]) ? (int)$matches[1] : 1;
                $sequenceNumber = max($sequenceNumber, $currentSequence + 1);
            }
        }
    }

    if (!$allFinished) {
        return true;
    }

    $newContractName = 'Contract ' . $companyName . ' AI' . ($sequenceNumber > 1 ? " $sequenceNumber" : '');
   
    $newContract = [
        "templateset" => 2,
        "name" => $newContractName,
        "company" => $clientNumber,
        "date" => date("Y-m-d"),
        "frequency" => "EVERYYEAR",
        "paymentmethod" => "MANUALTRANSFER",
        "description" => "Contract voor de Yooker AI abonnementen",
        "customfields" => $sequenceNumber,
    ];
        
    $contractCreateResponse = $API->contract_create($newContract);
    $contractId = $contractCreateResponse[0]['result']['recordid'] ?? null;

    return $contractId ? true : false;
}
?>