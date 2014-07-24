<?php
namespace Svea\WebService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/** 
 * Handles the various Svea WebPay WebServiceEU request responses. The respective
 * child response classes parses the service response and sets public attributes 
 * that may be inspected.
 * 
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea WebPay
 */
abstract class WebServiceResponse {

    /** @var bool $accepted  true if the request succeeded, else false */
    public $accepted;
    
    /** @var string $errormessage  set iff the request returned an unsuccessful response, see also the returncode attribute */
    public $errormessage;   

    /** @var int $resultcode  response specific result code */
    public $resultcode;    
}