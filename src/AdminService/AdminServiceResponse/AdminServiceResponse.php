<?php
namespace Svea\AdminService;

/**
 * Handles common Svea Admin Web Service request response attributes.
 * 
 * @author Kristian Grossman-Madsen
 */
class AdminServiceResponse {

    /** @var int $accepted  true iff request was accepted by the service */
    public $accepted;    
    /** @var int $resultcode  response specific result code */
    public $resultcode;    
    /** @var string errormessage  may be set iff accepted above is false */
    public $errormessage;      

    /**
     * Parses response and sets basic attributes.
     */    
    protected function formatObject($message) {
        // was request accepted?
        $this->accepted = $message->ResultCode == 0 ? 1 : 0; // ResultCode of 0 means all went well.
        $this->errormessage = isset($message->ErrorMessage) ? $message->ErrorMessage : "";
        $this->resultcode = $message->ResultCode;

    }
}
