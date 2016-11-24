<?php

namespace Svea\WebPay\Checkout\Validation;

use Svea\WebPay\BuildOrder\Validator\OrderValidator;

/**
 * Class GetCheckoutValidator
 * @package Svea\Svea\WebPay\WebPay\Checkout\Validation
 */
class GetOrderValidator extends OrderValidator
{
    public $errors = array();

    public function validate($order)
    {
        $errors = $this->errors;

        $errors = $this->validateOrderId($order, $errors);

        return $errors;
    }
}
