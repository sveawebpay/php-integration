<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../src/Includes.php';

/**
 * Description of VoidValidator
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class VoidValidator extends OrderValidator {

    public $nrOfCalls = 0;

    function validate($order) {
        $this->nrOfCalls++;
    }

}

?>
