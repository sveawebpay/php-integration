<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class VoidValidator extends OrderValidator {

    public $nrOfCalls = 0;

    public function validate($order) {
        $this->nrOfCalls++;
    }
}
