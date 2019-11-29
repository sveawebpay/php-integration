<?php

namespace Svea\WebPay\BuildOrder\Validator;

use Svea\WebPay\Helper\Helper;
/**
 * @author Anneli Halld'n, Daniel Brolund, Fredrik Sundell for Svea Webpay
 */
class HostedOrderValidator extends OrderValidator
{
    public $errors = array();

    protected $isCompany = false;

    /**
     * @param type $order
     * @return array $errors
     */
    public function validate($order)
    {
        if (isset($order->order->orgNumber) || isset($order->order->companyVatNumber) || isset($order->order->companyName)) {
            $this->isCompany = TRUE;
        }

        $this->errors = $this->validateClientOrderNumber($order->order, $this->errors);
        $this->errors = $this->validateCurrency($order, $this->errors);
        $this->errors = $this->validateCountryCode($order, $this->errors); //should be optional for hosted payment because not used
        $this->errors = $this->validateRequiredFieldsForOrder($order->order, $this->errors);
        $this->errors = $this->validateOrderRows($order, $this->errors);
        $this->errors = $this->validatePayerAlias($order, $this->errors); // validate for swish

        return $this->errors;
    }

    /**
     * @param type $order
     * @param array $errors
     * @return array
     */
    private function validatePayerAlias($order, $errors)
    {
        if (isset($order->order->payerAlias) && $order->paymentMethod == "SWISH")
        {
            if(ctype_digit($order->order->payerAlias) == false)
            {
                $errors['incorrect type'] = 'payerAlias must be numeric and can not contain any non-numeric characters';
            }
            if(strlen($order->order->payerAlias) != 11)
            {
                $errors['incorrect length'] = 'payerAlias must be 11 digits';
            }
            if($order->order->countryCode != "SE")
            {
                $errors['incorrect value'] = 'countryCode must be set to "SE" if payment method is SWISH';
            }
        }
        elseif(isset($order->order->payerAlias) == false && isset($order->paymentMethod) && $order->paymentMethod == "SWISH")
        {
            $errors['missing value'] = 'payerAlias must be set if using payment method SWISH. Use function setPayerAlias()';
        }
        return $errors;
    }

    /**
     * @param type $order
     * @param array $errors
     * @return array
     */
    private function validateClientOrderNumber($order, $errors)
    {
        if (isset($order->clientOrderNumber) == false || "" == $order->clientOrderNumber) {
            $errors['missing value'] = "ClientOrderNumber is required. Use function setClientOrderNumber().";
        }
        /*if(isset($order->clientOrderNumber) && $order->paymentMethod == "SWISH")
        {
            $errors['incorrect value'] = "ClientOrderNumber cannot be longer than 35 characters for Swish payments";
        }*/
        return $errors;
    }

    /**
     * @param type $order
     * @param type $errors
     * @return type
     */
    private function validateCurrency($order, $errors)
    {
        if (isset($order->order->currency) == false) {
            $errors['missing value'] = "Currency is required. Use function setCurrency().";
        }
        if (isset($order->order->currency) && isset($order->paymentMethod))
        {
            if($order->paymentMethod == "SVEACARDPAY" || $order->paymentMethod == "SVEACARDPAY_PF")
            {
                if(Helper::isCardPayCurrency($order->order->currency) == false)
                {
                    $errors['unsupported currency'] = "Currency is not supported with this payment method.";
                }
            }
        }
        return $errors;
    }

    /**
     * @param type $order
     * @param type $errors
     * @return type
     */
    private function validateCountryCode($order, $errors)
    {
        if (isset($order->order->countryCode) == false && isset($order->paymentMethod) && $order->paymentMethod == "SVEACARDPAY_PF") {
            $errors['missing value'] = "CountryCode is required for SVEACARDPAY_PF. Use function setCountryCode().";
        }

        return $errors;
    }

    public function validateEuroCustomer($order, $errors)
    {
        if (isset($order->customerIdentity->initials) == false && $this->isCompany == FALSE && $order->countryCode == "NL") {
            $errors['missing value'] = "Initials is required for INVOICE and PAYMENTPLAN payments for individual customers when countrycode is NL. Use function setInitials().";
        }
        if (isset($order->customerIdentity->birthDate) == false && $this->isCompany == FALSE) {
            $errors['missing value'] = "BirthDate is required for INVOICE and PAYMENTPLAN payments for individual customers when countrycode is NL. Use function setBirthDate().";
        }
        if (isset($order->customerIdentity->firstname) == false || isset($order->customerIdentity->lastname) == false && $this->isCompany == FALSE) {
            $errors['missing value'] = "Name is required for INVOICE and PAYMENTPLAN payments for individual customers when countrycode is NL. Use function setName().";
        }
        if (isset($order->customerIdentity->companyVatNumber) == false && $this->isCompany == true) {
            $errors['missing value'] = "VatNumber is required for INVOICE and PAYMENTPLAN payments for company customers when countrycode is NL. Use function setVatNumber().";
        }
        if (isset($order->customerIdentity->companyName) == false && $this->isCompany == true) {
            $errors['missing value'] = "CompanyName is required for INVOICE and PAYMENTPLAN payments for individual customers when countrycode is NL. Use function setCompanyName().";
        }
        if (isset($order->customerIdentity->street) == false || isset($order->customerIdentity->housenumber) == false) {
            $errors['missing value'] = "StreetAddress is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setStreetAddress().";
        }
        if (isset($order->customerIdentity->locality) == false) {
            $errors['missing value'] = "Locality is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setLocality().";
        }
        if (isset($order->customerIdentity->zipCode) == false) {
            $errors['missing value'] = "ZipCode is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setZipCode().";
        }

        return $errors;
    }
}
