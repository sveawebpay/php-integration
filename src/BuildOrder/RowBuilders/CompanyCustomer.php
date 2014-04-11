<?php
namespace Svea;

/**
 * @author anne-hal
 */
class CompanyCustomer {

    /**
     * Example: 4608142222
     * Required for company customers in SE, NO, DK, FI
     * For SE: Organisationsnummer
     * For NO: Organisasjonsnummer
     * For DK: CVR
     * For FI: Yritystunnus
     * @param string $companyIdNumberAsString
     * @return $this
     */
    public function setNationalIdNumber($companyIdNumberAsString) {
        $this->orgNumber = $companyIdNumberAsString;
        return $this;
    }
    /** @var string $orgNumber */
    public $orgNumber;
    
    /**
     * Example: NL123456789A12
     * @param string $vatNumber
     * Required for NL and DE
     * @return $this
     */
    public function setVatNumber($vatNumberAsString) {
        $this->companyVatNumber = $vatNumberAsString;
        return $this;
    }
    /** @var string $companyVatNumber */
    public $companyVatNumber;    

    /**
     * Optional but desirable
     * @param type $emailAsString
     * @return $this
     */
    public function setEmail($emailAsString) {
        $this->email = $emailAsString;
        return $this;
    }
    /** @var string $email */
    public $email;
    
     /**
     * Optional
     * @param int $phoneNumberAsInt  @todo check if int or string is correct?
     * @return $this
     */
    public function setPhoneNumber($phoneNumberAsInt) {
        $this->phonenumber = $phoneNumberAsInt;
        return $this;
    }
    /** @var int $phonenumber */
    public $phonenumber;
    
    /**
     * Optinal but desirable
     * @param type $ipAddressAsString
     * @return $this
     */
    public function setIpAddress($ipAddressAsString) {
        $this->ipAddress = $ipAddressAsString;
        return $this;
    }
    /** @var string $ipAddress */
    public $ipAddress;    
    
    /**
     * Required in NL and DE
     * @param type $streetAsString
     * @param type $houseNumberAsInt
     * @return $this
     */
    public function setStreetAddress($streetAsString, $houseNumberAsInt) {
        $this->street = $streetAsString;
        $this->housenumber = $houseNumberAsInt;
        return $this;
    }
    /** @var string $street */
    public $street;    
    /** @var int $housenumber */
    public $housenumber;        
    
    /**
     * Optional in NL and DE
     * @param type $coAddressAsString
     * @return $this
     */
    public function setCoAddress($coAddressAsString) {
        $this->coAddress = $coAddressAsString;
        return $this;
    }
    /** @var string $coAddress */
    public $coAddress;      
    
    /**
     * Requuired in NL and DE
     * @param type $zipCodeAsString
     * @return $this
     */
    public function setZipCode($zipCodeAsString) {
        $this->zipCode = $zipCodeAsString;
        return $this;
    }
    /** @var string $zipCode */
    public $zipCode;      
    
    /**
     * Required in NL and DE
     * @param type $cityAsString
     * @return $this
     */
    public function setLocality($cityAsString) {
        $this->locality = $cityAsString;
        return $this;
    }
    /** @var string $locality */
    public $locality;   
    
    /**
     * Required for Eu countries like NL and DE
     * @param string $nameAsString
     * @return $this
     */
    public function setCompanyName($nameAsString) {
        $this->companyName = $nameAsString;
        return $this;
    }
    /** @var string $companyName */
    public $companyName;    
    
    /**
    * Optional. If not set, the invoice/partpayment orders will use the first registered address as invoice address.
    * Recieve string param from getAddresses
     * @param type $addressSelectorAsString
     * @return $this
     */
    public function setAddressSelector($addressSelectorAsString) {
        $this->addressSelector = $addressSelectorAsString;
        return $this;
    }
    /** @var string $addressSelector */
    public $addressSelector;
}
