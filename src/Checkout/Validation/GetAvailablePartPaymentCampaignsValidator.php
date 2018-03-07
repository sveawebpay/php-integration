<?php

namespace Svea\WebPay\Checkout\Validation;

use Svea\WebPay\BuildOrder\Validator\OrderValidator;

/**
 * Class GetAvailablePartPaymentCampaignsValidator
 * @package Svea\Svea\WebPay\WebPay\Checkout\Validation
 */
class GetAvailablePartPaymentCampaignsValidator extends OrderValidator
{
    public $errors = array();

    public function validate($request)
    {
        $errors = $this->errors;
        $errors = $this->validatePresetIsCompanyIsSet($request, $errors);
        $errors = $this->validatePresetIsCompanyIsBoolean($request, $errors);

        return $errors;
    }
}
