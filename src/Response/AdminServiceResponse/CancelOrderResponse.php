<?php
namespace Svea;

/**
 * Handles the Svea Admin Web Service CancelOrder request response.
 * 
 * @author Kristian Grossman-Madsen
 */
class CancelOrderResponse {

    /** @var int $accepted  true iff request was accepted by the service */
    public $accepted;
    
    /** @var int $resultcode  response specific result code */
    public $resultcode;

    /** @var string errormessage  may be set iff accepted above is false */
    public $errormessage;   

    
    function __construct($message) {
        $this->formatObject($message);  
    }

    /**
     * Parses response and sets attributes.
     */    
    protected function formatObject($message) {
        // was request accepted?     
        
        // was request accepted?
        $this->accepted = $message->ResultCode == 0 ? 1 : 0; // ResultCode of 0 means all went well.
        $this->errormessage = isset($message->ErrorMessage) ? $message->ErrorMessage : "";
        $this->resultcode = $message->ResultCode;

        // if successful, set deliverOrderResult, using the same attributes as for DeliverOrderEU?
        if ($this->accepted == 1) {
 
        }
    }
}
