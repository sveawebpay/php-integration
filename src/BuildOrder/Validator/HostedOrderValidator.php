<?php

require_once SVEA_REQUEST_DIR . '/Includes.php';
require_once 'OrderValidator.php';

/**
 * Description of HostedOrderValidator
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package BuildOrder
 */
class HostedOrderValidator extends OrderValidator {

     public $errors = array();
     protected $isCompany = false;

     /**
     * @param type $order
     * @return type $errors
     */
    public function validate($order) {
         if(isset($order->orgNumber) || isset($order->companyVatNumber) || isset($order->companyName)){
            $this->isCompany = TRUE;           
        }
        $this->errors = $this->validateClientOrderNumber($order,$this->errors);
        $this->errors = $this->validateCurrency($order,$this->errors);
        $this->errors = $this->validateCountryCode($order, $this->errors);
        $this->errors = $this->validateRequiredFieldsForOrder($order,$this->errors);
        $this->errors = $this->validateOrderRows($order,$this->errors);
        if (isset($order->countryCode) && $order->countryCode == "NL") {
            $this->errors = $this->validateNlCustomer($order,  $this->errors);
        }
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
    private function validateCurrency($order,$errors){
         if(isset($order->currency) == false){
            $errors['missing value'] = "Currency is required. Use function setCurrency().";
        }
        return $errors;
    }
     /**
     * @param type $order
     * @param type $errors
     */
    private function validateCountryCode($order,$errors){
         if(isset($order->countryCode) == false){
            $errors['missing value'] = "CountryCode is required. Use function setCountryCode().";
        }
        return $errors;
    }

    public function validateNlCustomer($order, $errors) {
        if(isset($order->initials) == false && $this->isCompany == FALSE){
            $errors['missing value'] = "CustomerInitials is required for individual customers when countrycode is NL. Use function setCustomerInitials().";
        }
        if(isset($order->birthDate) == false && $this->isCompany == FALSE){
            $errors['missing value'] = "CustomerBirthDate is required for individual customers when countrycode is NL. Use function setCustomerBirthDate().";
        }
        if(isset($order->firstname) == false || isset($order->lastname) == false && $this->isCompany == FALSE){
            $errors['missing value'] = "CustomerName is required for individual customers when countrycode is NL. Use function setCustomerName().";
        }
        if(isset($order->companyVatNumber) == false && $this->isCompany == true){
            $errors['missing value'] = "CustomerCompanyVatNumber is required for company customers when countrycode is NL. Use function setCustomerCompanyVatNumber().";
        }
        if(isset($order->companyName) == false && $this->isCompany == true){
            $errors['missing value'] = "CustomerCompanyName is required for individual customers when countrycode is NL. Use function setCustomerCompanyName().";
        }
        if(isset($order->street) == false || isset($order->housenumber) == false){
            $errors['missing value'] = "CustomerStreetAddress is required for all customers when countrycode is NL. Use function setCustomerStreetAddress().";
        }
        if(isset($order->locality) == false){
            $errors['missing value'] = "CustomerLocality is required for all customers when countrycode is NL. Use function setCustomerLocality().";
        }
        if(isset($order->zipCode) == false){
            $errors['missing value'] = "CustomerZipCode is required for all customers when countrycode is NL. Use function setCustomerZipCode().";
        }
       
        return $errors;
    }
  
}

?>
