<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class WebServiceResponse {

    public $accepted;
    public $resultcode;
    public $paymentmethod;
    //public $merchantId; should not be here. Not included in the webservice response. Removed 2013-11-26 by Anneli

    function __construct($message) {
        $this->formatObject($message);
    }
}
