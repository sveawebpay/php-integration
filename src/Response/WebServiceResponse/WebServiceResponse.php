<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/** 
 * Handles the various Svea WebPay WebserviceEU responses
 * 
 * @attrib accepted                 // true iff request was accepted by the service 
 * @attrib errormessage             // may be set iff accepted above is false
 * 
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Masen for Svea Webpay
 */
abstract class WebServiceResponse {

    public $accepted;
    public $errormessage;   

    function __construct($message) {
        $this->formatObject($message);  
    }
    
    /**
     * Implemented by subclasses. Parses response and sets attributes.
     */
    abstract protected function formatObject($message);
}
