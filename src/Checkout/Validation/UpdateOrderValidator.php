<?php

namespace Svea\WebPay\Checkout\Validation;

use Svea\WebPay\BuildOrder\Validator\OrderValidator;

/**
 * Class UpdateCheckoutValidator
 * @package Checkout\Validation
 */
class UpdateOrderValidator extends OrderValidator
{
    public $errors = array();

    public function validate($order)
    {
        $errors = $this->errors;
        $errors = $this->restrictExVatValue($order, $errors);
        $errors = $this->validateOrderId($order, $errors);
        $errors = $this->validateOrderRows($order, $errors);

        return $errors;
    }

    /**
     * @param object $order
     * @param array $errors
     * @return array
     */
    protected function validateOrderRows($order, $errors)
    {
        $errors = parent::validateOrderRows($order, $errors);
        $errors = $this->validateCheckoutOrderRows($order, $errors);

        return $errors;
    }
}
