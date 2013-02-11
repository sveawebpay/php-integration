<?php

/**
 * Description of IdentityValidator
 */
class IdentityValidator {
    
    private $isCompany;


    function __construct($isCompany = false) {
        $this->isCompany = $isCompany;
    }
/**
 * Not in use
 * @param type $order
 * @param string $errors
 * @return string
 */
    public function validateThatCustomerIdentityExists($order,$errors) {
        if (    isset($order->ssn) == false 
                && isset($order->orgNumber) == false
                && isset($order->companyVatNumber) == false
                && isset($order->initials) == false
                && isset($order->email) == false
                && isset($order->phonenumber) == false
                && isset($order->ipAddress) == false
                && isset($order->firstname) == false
                && isset($order->lastname) == false
                && isset($order->street) == false
                && isset($order->housenumber) == false
                && isset($order->zipcode) == false
                && isset($order->coAddress) == false
                && isset($order->locality) == false
                && isset($order->companyName) == false) {
            $errors['missing values'] = "Customer values are required for Invoice and PaymentPlan orders.";
        }
        return $errors;
    }

   
    /**
     *  ssn or companyVanNumber required for SE, NO, DK, FI
     * @param type $order
     * @param type $errors
     * @return string
     */
    public function validateNordicIdentity($order, $errors) {
        if (isset($order->ssn) == false && $this->isCompany == FALSE){
            $errors['missing value'] = "CustomerSsn is required for individual customers when countrycode is SE, NO, DK or FI. Use function setCustomerSsn().";

        }  elseif ( isset($order->orgNumber) == false && $this->isCompany){ 
            $errors['missing value'] =  "OrgNumber is required for company customers when countrycode is SE, NO, DK or FI. Use function setCustomerCompanyIdNumber().";

        }
        return $errors;
    }

    /**
     * CustomerIdentity values Required for NL
     * @param type $order
     * @param type $errors
     */
    public function validateNLidentity($order, $errors) {
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

    /**
     * CustomerIdentity values Required for DE
     * @param type $order
     * @param type $errors
     */
    public function validateDEidentity($order, $errors) {
        
        if(isset($order->birthDate) == false && $this->isCompany == FALSE){
            $errors['missing value'] = "CustomerBirthDate is required for individual customers when countrycode is DE. Use function setCustomerBirthDate().";
        }
        if(isset($order->firstname) == false || isset($order->lastname) == false && $this->isCompany == FALSE){
            $errors['missing value'] = "CustomerName is required for individual customers when countrycode is DE. Use function setCustomerName().";
        }
        if(isset($order->companyVatNumber) == false && $this->isCompany == true){
            $errors['missing value'] = "CustomerCompanyVatNumber is required for company customers when countrycode is DE. Use function setCustomerCompanyVatNumber().";
        }
        if(isset($order->companyName) == false && $this->isCompany == true){
            $errors['missing value'] = "CustomerCompanyName is required for individual customers when countrycode is DE. Use function setCustomerCompanyName().";
        }
        if(isset($order->street) == false || isset($order->housenumber) == false){
            $errors['missing value'] = "CustomerStreetAddress is required for all customers when countrycode is DE. Use function setCustomerStreetAddress().";
        }
        if(isset($order->locality) == false){
            $errors['missing value'] = "CustomerLocality is required for all customers when countrycode is DE. Use function setCustomerLocality().";
        }
        if(isset($order->zipCode) == false){
            $errors['missing value'] = "CustomerZipCode is required for all customers when countrycode is DE. Use function setCustomerZipCode().";
        }
      
        return $errors;
    }


    
    /**
     * @param type $order
     * @param type $errors
     * @return type $errors
     */
    public function validateDoubleIdentity($order,$errors) {
        if((isset($order->orgNumber) || isset($order->companyVatNumber)) && isset($order->ssn)){
            $errors['duplicated value'] = "Customer is either an individual or a company. You can not use function setCustomerSsn() in combination with setCustomerCompanyIdNumber() or setCustomerCompanyVatNumber().";
        }
        return $errors;        
    }
}

?>
