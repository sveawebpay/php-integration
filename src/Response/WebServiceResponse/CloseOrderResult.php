<?php
namespace Svea;

require_once 'WebServiceResponse.php';

/**
 * @author anne-hal
 */
class CloseOrderResult extends WebServiceResponse{

    function __construct($message) {
        parent::__construct($message);
         if (isset($message->CloseOrderEuResult->ErrorMessage))
        $this->errormessage = $message->CloseOrderEuResult->ErrorMessage;
    }

    protected function formatObject($message) {
        $this->accepted = $message->CloseOrderEuResult->Accepted;
        $this->resultcode = $message->CloseOrderEuResult->ResultCode;
    }
}
