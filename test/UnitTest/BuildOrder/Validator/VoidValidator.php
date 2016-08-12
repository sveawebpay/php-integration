<?php

namespace Svea\WebPay\Test\UnitTest\BuildOrder\Validator;

use Svea\WebPay\BuildOrder\Validator\OrderValidator;

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class VoidValidator extends OrderValidator
{

    public $nrOfCalls = 0;

    public function validate($order)
    {
        $this->nrOfCalls++;
    }
}
