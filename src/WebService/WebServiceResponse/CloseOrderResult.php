<?php
namespace Svea\WebService;

require_once 'WebServiceResponse.php';

/**
 * Handles the Svea Webservice CloseOrder request response.
 * 
 * @author anne-hal, Kristian Grossman-Madsen
 */
class CloseOrderResult extends WebServiceResponse {

    /** type $resultcode  response specific result code */
    public $resultcode;

    protected function formatObject($message) {
        // was request accepted?
        $this->accepted = $message->CloseOrderEuResult->Accepted; // false or 1
        $this->errormessage = isset($message->CloseOrderEuResult->ErrorMessage) ? $message->CloseOrderEuResult->ErrorMessage : "";        

        // set response resultcode
        $this->resultcode = $message->CloseOrderEuResult->ResultCode;
    }
}
