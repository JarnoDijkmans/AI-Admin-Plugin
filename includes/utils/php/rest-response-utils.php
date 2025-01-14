<?php
/**
 * reusable function for failure rest_ensure_response.
 *
 * @param int $status The status of the rest response
 * @param string $message The message of the rest response
 * @return object returns rest_ensure_response
 */

    function rest_response_fail($status, $message) {
        $response = new \stdClass();
        
        $response->message = $message;
        $response->success = false;
        $response->status = $status;

        $rest_response = rest_ensure_response($response);
        $rest_response->set_status($status);

        return $rest_response;
    }

/**
 * reusable function for succesfull rest_ensure_response.
 *
 * @param int $status The status of the rest response
 * @param string $message The message of the rest response
 * @param mixed|null $data Optional additional data for the response
 * @return object returns rest_ensure_response
 */

    function rest_response_success($status, $message, $data = null) {
        $response = new \stdClass();
        
        $response->message = $message;
        $response->success = true;
        $response->status = $status;
        $response->data = $data;
        
        $rest_response = rest_ensure_response($response);
        $rest_response->set_status($status);

        return $rest_response;
    }
?>