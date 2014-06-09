<?php
namespace Svea;

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
        if (isset($order->customerIdentity->ssn) == false
            && isset($order->customerIdentity->orgNumber) == false
            && isset($order->customerIdentity->companyVatNumber) == false
            && isset($order->customerIdentity->initials) == false
            && isset($order->customerIdentity->email) == false
            && isset($order->customerIdentity->phonenumber) == false
            && isset($order->customerIdentity->ipAddress) == false
            && isset($order->customerIdentity->firstname) == false
            && isset($order->customerIdentity->lastname) == false
            && isset($order->customerIdentity->street) == false
            && isset($order->customerIdentity->housenumber) == false
            && isset($order->customerIdentity->zipcode) == false
            && isset($order->customerIdentity->coAddress) == false
            && isset($order->customerIdentity->locality) == false
            && isset($order->customerIdentity->companyName) == false) {
    $errors['missing values'] = "Customer values are required for Invoice and PaymentPlan orders.";
}

        return $errors;
    }

    /**
     * Validates that either NationalIdNumber (individual customers) or companyVatNumber (company customers) is set to a non-empty string.
     * (NationalIdNumber or companyVatNumber required for SE, NO, DK, FI orders)
     * @param Svea\CreateOrderBuilder $order
     * @param array of string $errors  -- validator errors array
     * @return array of string $errors -- updated validator errors array
     */
    public function validateNordicIdentity($order, $errors) {
        if($this->isCompany == FALSE )
        {
            if( !isset($order->customerIdentity->ssn) || empty($order->customerIdentity->ssn) )
            {
                $errors['missing value'] = "NationalIdNumber is required for individual customers when countrycode is SE, NO, DK or FI. Use function setNationalIdNumber().";
            } 
        }
        else    // is company customer
        {
            if( !isset($order->customerIdentity->orgNumber) || empty($order->customerIdentity->orgNumber) ) 
            {
                $errors['missing value'] =  "OrgNumber is required for company customers when countrycode is SE, NO, DK or FI. Use function setNationalIdNumber().";
            }
        }
        return $errors;
    }

    /**
     * CustomerIdentity values Required for NL
     * @param type $order
     * @param type $errors
     */
    public function validateNLidentity($order, $errors) {
        if ($this->isCompany == FALSE && isset($order->customerIdentity->initials) == false) {
            $errors['missing value'] = "Initials is required for individual customers when countrycode is NL. Use function setInitials().";
        }

        if ($this->isCompany == FALSE && isset($order->customerIdentity->birthDate) == false) {
            $errors['missing value'] = "BirthDate is required for individual customers when countrycode is NL. Use function setBirthDate().";
        }

        if ($this->isCompany == FALSE && (isset($order->customerIdentity->firstname) == false || isset($order->customerIdentity->lastname) == false)) {
            $errors['missing value'] = "Name is required for individual customers when countrycode is NL. Use function setName().";
        }

        if ($this->isCompany == true && isset($order->customerIdentity->companyVatNumber) == false) {
            $errors['missing value'] = "VatNumber is required for company customers when countrycode is NL. Use function setVatNumber().";
        }
        if($this->isCompany == true && isset($order->customerIdentity->companyName) == false){
            $errors['missing value'] = "CompanyName is required for company customers when countrycode is NL. Use function setCompanyName().";

        }

        if (isset($order->customerIdentity->street) == false || isset($order->customerIdentity->housenumber) == false) {
            $errors['missing value'] = "StreetAddress is required for all customers when countrycode is NL. Use function setStreetAddress().";
        }

        if (isset($order->customerIdentity->locality) == false) {
            $errors['missing value'] = "Locality is required for all customers when countrycode is NL. Use function setLocality().";
        }

        if (isset($order->customerIdentity->zipCode) == false) {
            $errors['missing value'] = "ZipCode is required for all customers when countrycode is NL. Use function setZipCode().";
        }

        return $errors;
    }

    /**
     * CustomerIdentity values Required for DE
     * @param type $order
     * @param type $errors
     */
    public function validateDEidentity($order, $errors) {
        if ($this->isCompany == FALSE && isset($order->customerIdentity->birthDate) == false) {
            $errors['missing value'] = "BirthDate is required for individual customers when countrycode is DE. Use function setBirthDate().";
        }

        if ($this->isCompany == FALSE && (isset($order->customerIdentity->firstname) == false || isset($order->customerIdentity->lastname) == false)) {
            $errors['missing value'] = "Name is required for individual customers when countrycode is DE. Use function setName().";
        }

        if ($this->isCompany == true && isset($order->customerIdentity->companyVatNumber) == false) {
            $errors['missing value'] = "VatNumber is required for company customers when countrycode is DE. Use function setVatNumber().";
        }

        if ($this->isCompany == true && isset($order->customerIdentity->companyName) == false) {
            $errors['missing value'] = "CompanyName is required for individual customers when countrycode is DE. Use function setCompanyName().";
        }

        if (isset($order->customerIdentity->street) == false || isset($order->customerIdentity->housenumber) == false) {
            $errors['missing value'] = "StreetAddress is required for all customers when countrycode is DE. Use function setStreetAddress().";
        }

        if (isset($order->customerIdentity->locality) == false) {
            $errors['missing value'] = "Locality is required for all customers when countrycode is DE. Use function setLocality().";
        }

        if (isset($order->customerIdentity->zipCode) == false) {
            $errors['missing value'] = "ZipCode is required for all customers when countrycode is DE. Use function setZipCode().";
        }

        return $errors;
    }

    /**
     * @param type $order
     * @param type $errors
     * @return type $errors
     */
    public function validateDoubleIdentity($order,$errors) {
        if ((isset($order->customerIdentity->orgNumber) || isset($order->customerIdentity->companyVatNumber)) && isset($order->customerIdentity->ssn)) {
            $errors['duplicated value'] = "Customer is either an individual or a company. You can not use function setNationalIdNumber() in combination with setNationalIdNumber() or setCompanyVatNumber().";
        }

        return $errors;
    }
}
