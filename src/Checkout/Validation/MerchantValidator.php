<?php

namespace Svea\WebPay\Checkout\Validation;

use Svea\WebPay\Checkout\Model\MerchantSettings;

/**
 * Class MerchantValidator
 * @package Svea\Svea\WebPay\WebPay\Checkout\Validation
 */
class MerchantValidator
{
    public function validate(MerchantSettings $merchant, $errors)
    {
        $termsUri = $merchant->getTermsUri();
        $checkoutUri = $merchant->getCheckoutUri();
        $confirmationUri = $merchant->getConfirmationUri();
        $pushUri = $merchant->getPushUri();

        if (!isset($termsUri) || trim($termsUri) === '') {
            $errors['incorrectMerchantTermsUri'] = "termsUri must be defined";
        }

        if (!isset($checkoutUri) || trim($checkoutUri) === '') {
            $errors['incorrectMerchantCheckoutUri'] = "checkoutUri must be defined";
        }

        if (!isset($confirmationUri) || trim($confirmationUri) === '') {
            $errors['incorrectMerchantConfirmationUri'] = "confirmationUri must be defined";
        }

        if (!isset($pushUri) || trim($pushUri) === '') {
            $errors['incorrectMerchantPushUri'] = "pushUri must be defined";
        }

        return $errors;
    }
}
