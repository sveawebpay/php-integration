<?php
namespace Svea\WebService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/** 
 * Handles the various Svea WebPay WebserviceEU responses
 * 
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Masen for Svea Webpay
 */
abstract class WebServiceResponse {

    /** @var bool $accepted  true iff the request succeeded */
    public $accepted;
    
    /** @var string $errormessage  set iff the request returned an unsuccessful response, see also the returncode attribute */
    public $errormessage;   

    function __construct($message) {
        $this->formatObject($message);  
    }
    
    /**
     * Implemented by subclasses. Parses response and sets attributes.
     */
    abstract protected function formatObject($message);
}
