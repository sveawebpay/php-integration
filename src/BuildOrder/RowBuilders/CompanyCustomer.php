<?php
namespace Svea;

/**
 * Class CompanyCustomer, a customer information container for legal entities.
 * 
 * Note that "required" below as a requirement only when the IndividualCustomer is used 
 * to identify the customer when using the invoice or payment plan payment methods.
 *  
 * (For card and direct bank orders, adding customer information to the order is optional.)
 * 
 *     $order->addCustomerDetails(
 *         WebPayItem::companyCustomer()
 *             ->setNationalIdNumber(2345234)      // required in SE, NO, DK, FI
 *             ->setVatNumber("NL2345234")         // required in NL and DE
 *             ->setCompanyName("TestCompagniet")  // required in NL and DE
 *             ->setStreetAddress("Gatan", 23)     // required in NL and DE
 *             ->setZipCode(9999)                  // required in NL and DE
 *             ->setLocality("Stan")               // required in NL and DE
 *             ->setEmail("test@svea.com")         // optional but desirable
 *             ->setIpAddress("123.123.123")       // optional but desirable
 *             ->setCoAddress("c/o Eriksson")      // optional
 *             ->setPhoneNumber(999999)            // optional
 *             ->setAddressSelector("7fd7768")     // optional, string recieved from WebPay::getAddress() request
 *     )
 * ;
 * 
 * @author anne-hal, Kristian Grossman-Madsen
 */
class CompanyCustomer {
    
    /** @var string $orgNumber */
    public $orgNumber;
    /** @var string $companyVatNumber */
    public $companyVatNumber; 
    /** @var string $email */
    public $email;
    /** @var int $phonenumber */
    public $phonenumber;
    /** @var string $ipAddress */
    public $ipAddress;  
    /** @var string $street */
    public $street;    
    /** @var int $housenumber */
    public $housenumber;
    /** @var string $coAddress */
    public $coAddress; 
    /** @var string $zipCode */
    public $zipCode; 
    /** @var string $locality */
    public $locality;
    /** @var string $companyName */
    public $companyName; 
    /** @var string $addressSelector */
    public $addressSelector;
    
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

    /**
     * Optional but desirable
     * @param type $emailAsString
     * @return $this
     */
    public function setEmail($emailAsString) {
        $this->email = $emailAsString;
        return $this;
    }
    
     /**
     * Optional
     * @param int $phoneNumberAsInt
     * @return $this
     */
    public function setPhoneNumber($phoneNumberAsInt) {
        $this->phonenumber = $phoneNumberAsInt;
        return $this;
    }
    
    /**
     * Optinal but desirable
     * @param type $ipAddressAsString
     * @return $this
     */
    public function setIpAddress($ipAddressAsString) {
        $this->ipAddress = $ipAddressAsString;
        return $this;
    }  
         
    /**
     * Required in NL and DE
     * For other countries, you may ommit this, or let either of street and/or housenumber be empty
     * 
     * @param string $streetAsString
     * @param int $houseNumberAsInt  -- optional
     * @return $this
     */
    public function setStreetAddress($streetAsString, $houseNumberAsInt = null) { // = null is poor man's overloading
        $this->street = $streetAsString;
        $this->housenumber = $houseNumberAsInt;
        return $this;
    }     
    /**
     * Optional in NL and DE
     * @param type $coAddressAsString
     * @return $this
     */
    public function setCoAddress($coAddressAsString) {
        $this->coAddress = $coAddressAsString;
        return $this;
    }     
    
    /**
     * Requuired in NL and DE
     * @param type $zipCodeAsString
     * @return $this
     */
    public function setZipCode($zipCodeAsString) {
        $this->zipCode = $zipCodeAsString;
        return $this;
    }     
    
    /**
     * Required in NL and DE
     * @param type $cityAsString
     * @return $this
     */
    public function setLocality($cityAsString) {
        $this->locality = $cityAsString;
        return $this;
    }   
    
    /**
     * Required for Eu countries like NL and DE
     * @param string $nameAsString
     * @return $this
     */
    public function setCompanyName($nameAsString) {
        $this->companyName = $nameAsString;
        return $this;
    }   
    
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
}
