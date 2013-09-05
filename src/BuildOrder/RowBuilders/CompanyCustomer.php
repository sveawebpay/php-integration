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
     * @return \CompanyCustomer
     */
    public function setNationalIdNumber($companyIdNumberAsString) {
        $this->orgNumber = $companyIdNumberAsString;
        return $this;
    }
    
    /**
     * Example: NL123456789A12
     * @param type $vatNumber
     * Required for NL and DE
     * @return \CompanyCustomer
     */
    public function setVatNumber($vatNumber) {
        $this->companyVatNumber = $vatNumber;
        return $this;
    }
    
    /**
     * Optional but desirable
     * @param type $emailAsString
     * @return \CompanyCustomer
     */
    public function setEmail($emailAsString) {
        $this->email = $emailAsString;
        return $this;
    }
    
     /**
     * Optional
     * @param type $phoneNumberAsInt
     * @return \CompanyCustomer
     */
    public function setPhoneNumber($phoneNumberAsInt) {
        $this->phonenumber = $phoneNumberAsInt;
        return $this;
    }
    
    /**
     * Optinal but desirable
     * @param type $ipAddressAsString
     * @return \CompanyCustomer
     */
    public function setIpAddress($ipAddressAsString) {
        $this->ipAddress = $ipAddressAsString;
        return $this;
    }
    
    /**
     * Required in NL and DE
     * @param type $streetAsString
     * @param type $houseNumberAsInt
     * @return \CompanyCustomer
     */
    public function setStreetAddress($streetAsString, $houseNumberAsInt) {
        $this->street = $streetAsString;
        $this->housenumber = $houseNumberAsInt;
        return $this;
    }
    
    /**
     * Optional in NL and DE
     * @param type $coAddressAsString
     * @return \CompanyCustomer
     */
    public function setCoAddress($coAddressAsString) {
        $this->coAddress = $coAddressAsString;
        return $this;
    }
    
    /**
     * Requuired in NL and DE
     * @param type $zipCodeAsString
     * @return \CompanyCustomer
     */
    public function setZipCode($zipCodeAsString) {
        $this->zipCode = $zipCodeAsString;
        return $this;
    }
    
    /**
     * Required in NL and DE
     * @param type $cityAsString
     * @return \CompanyCustomer
     */
    public function setLocality($cityAsString) {
        $this->locality = $cityAsString;
        return $this;
    }
    
    /**
     * Required for Eu countries like NL and DE
     * @param string $nameAsString
     * @return \CompanyCustomer
     */
    public function setCompanyName($nameAsString) {
        $this->companyName = $nameAsString;
        return $this;
    }

    /**
    * Optional when creating order
    * Recieve string param from getAddresses
     * @param type $addressSelectorAsString
     * @return \CompanyCustomer
     */
    public function setAddressSelector($addressSelectorAsString) {
        $this->addressSelector = $addressSelectorAsString;
        return $this;
    }
}
