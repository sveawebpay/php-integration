<?php

namespace Svea\WebPay\Checkout\Validation;

use Svea\WebPay\BuildOrder\Validator\OrderValidator;
use Svea\WebPay\Checkout\Helper\CheckoutOrderBuilder;

/**
 * Class CreateCheckoutValidator
 * @package Svea\Svea\WebPay\WebPay\Checkout\Validation
 */
class CreateOrderValidator extends OrderValidator
{
    public $errors = array();

    /**
     * @param CheckoutOrderBuilder $order
     * @return array|mixed
     */
    public function validate($order)
    {
        $errors = $this->errors;

        $errors = $this->restrictExVatValue($order, $errors);

        $errors = $this->validateRequiredOrderFields($order, $errors);

        $errors = $this->validateRequiredFieldsForOrder($order, $errors);

        $errors = $this->validateOrderRows($order, $errors);

        $errors = $this->validateMerchantSettings($order, $errors);

        return $errors;
    }

    /**
     * @param CheckoutOrderBuilder $order
     * @param array $errors
     * @return array
     */
    private function validateRequiredOrderFields($order, $errors)
    {
        // force correct order type of present attributes, see class OrderRow
        if ($order->countryCode === null ||
            !ctype_alpha($order->countryCode)
        ) {
            $errors['incorrectCountryCode'] = "countryCode must be defined, and string of alphabetic characters";
        }

        if ($order->currency === null ||
            !ctype_alpha($order->currency) ||
            count($order->currency) === 0
        ) {
            $errors['incorrectCurrency'] = "currency must be defined, and must be string of alphabetic characters";
        }

        if ($order->getLocale() === null || count($order->getLocale()) === 0) {
            $errors['incorrectLocale'] = "locale must be defined";
        }
        
        return $errors;
    }

    /**
     * @param CheckoutOrderBuilder $order
     * @param array $errors
     * @return mixed
     */
    private function validateMerchantSettings(CheckoutOrderBuilder $order, $errors)
    {
        $merchantValidator = new MerchantValidator();
        $errors = $merchantValidator->validate($order->getMerchantSettings(), $errors);

        return $errors;
    }

    /**
     * @param CheckoutOrderBuilder $order
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
