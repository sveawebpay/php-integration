<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';
require_once 'OrderValidator.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class HostedOrderValidator extends OrderValidator {

    public $errors = array();
    protected $isCompany = false;

    /**
     * @param type $order
     * @return array $errors
     */
    public function validate($order) {
        if (isset($order->orgNumber) || isset($order->companyVatNumber) || isset($order->companyName)) {
            $this->isCompany = TRUE;
        }

        $this->errors = $this->validateClientOrderNumber($order,$this->errors);
        $this->errors = $this->validateCurrency($order,$this->errors);
//        $this->errors = $this->validateCountryCode($order, $this->errors); //should be optional for hosted payment because not used
        $this->errors = $this->validateRequiredFieldsForOrder($order,$this->errors);
        $this->errors = $this->validateOrderRows($order,$this->errors);

        return $this->errors;
    }

    /**
     * @param type $order
     * @param type $errors
     */
    private function validateClientOrderNumber($order,$errors) {
        if (isset($order->clientOrderNumber) == false || "" == $order->clientOrderNumber) {
            $errors['missing value'] = "ClientOrderNumber is required. Use function setClientOrderNumber().";
        }
        return $errors;
    }

    /**
     * @param type $order
     * @param type $errors
     */
    private function validateCurrency($order,$errors) {
         if (isset($order->currency) == false) {
            $errors['missing value'] = "Currency is required. Use function setCurrency().";
        }
        return $errors;
    }

    /**
     * @param type $order
     * @param type $errors
     */
    private function validateCountryCode($order,$errors) {
         if (isset($order->countryCode) == false) {
            $errors['missing value'] = "CountryCode is required. Use function setCountryCode().";
        }
        return $errors;
    }

    public function validateEuroCustomer($order, $errors) {
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
