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

        $errors = $this->validatePartnerKey($order, $errors);

        $errors = $this->validateIdentityFlags($order, $errors);

        $errors = $this->validateRequireElectronicIdAuthentication($order, $errors);

        return $errors;
    }

    /**
     * @param CheckoutOrderBuilder $order
     * @param array $errors
     * @return array
     */
    private function validateRequireElectronicIdAuthentication($order, $errors)
    {
        if($order->getRequireElectronicIdAuthentication() != null)
        {
            if(!is_bool($order->getRequireElectronicIdAuthentication()))
            {
                $errors['invalid type'] = "requireElectronicIdAuthentication field isn't a boolean type, use setRequireElectronicIdAuthentication(true)";
            }
        }
        return $errors;
    }

    /**
     * @param CheckoutOrderBuilder $order
     * @param array $errors
     * @return array
     */
    private function validatePartnerKey($order, $errors)
    {
        $guid = $order->getPartnerKey();
        if($guid != null)
        {
            if (!preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', $guid)) {
                $errors['invalidFormatPartnerKey'] = "partnerKey is not in guid-format. The partnerKey is provided by Svea. If you're a partner to Svea and wish to use the partnerKey, please contact Svea in order to receive a guid.";
            }
        }
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

        if (isset($order->currency) === false ||
            !ctype_alpha($order->currency)
        ) {
            $errors['incorrectCurrency'] = "currency must be defined, and must be string of alphabetic characters";
        }

        if ($order->getLocale() === null) {
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

    /**
     * @param CheckoutOrderBuilder $order
     * @param array $errors
     * @return array
     */
    private function validateIdentityFlags($order, $errors)
    {
        $identityFlagValidator = new IdentityFlagValidator();
        $errors = $identityFlagValidator->validate($order->getIdentityFlags(), $errors);

        return $errors;
    }
}
