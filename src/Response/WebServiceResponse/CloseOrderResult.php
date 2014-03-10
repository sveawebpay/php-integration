<?php
namespace Svea;

require_once 'WebServiceResponse.php';

/**
 * Handles the Svea Webservice CloseOrder request response.
 * 
 * @attrib $resultcode -- response specific result code
 * 
 * @author anne-hal, Kristian Grossman-Madsen
 */
class CloseOrderResult extends WebServiceResponse {

    public $resultcode;

    protected function formatObject($message) {
        // was request accepted?
        $this->accepted = $message->CloseOrderEuResult->Accepted;
        $this->errormessage = isset($message->CloseOrderEuResult->ErrorMessage) ? $message->CloseOrderEuResult->ErrorMessage : "";        

        // set response resultcode
        $this->resultcode = $message->CloseOrderEuResult->ResultCode;
    }
}
