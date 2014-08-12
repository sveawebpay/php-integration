<?php
namespace Svea\WebService;

require_once 'WebServiceResponse.php';

/**
 * Handles the Svea Webservice CloseOrder request response.
 * 
 * @author anne-hal, Kristian Grossman-Madsen
 */
class CloseOrderResult extends WebServiceResponse {

    public function __construct($response) {
        // was request accepted?
        $this->accepted = $response->CloseOrderEuResult->Accepted; // false or 1
        $this->errormessage = isset($response->CloseOrderEuResult->ErrorMessage) ? $response->CloseOrderEuResult->ErrorMessage : "";        

        // set response resultcode
        $this->resultcode = $response->CloseOrderEuResult->ResultCode;
    }
}
