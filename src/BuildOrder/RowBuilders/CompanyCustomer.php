<?php
namespace Svea;

/**
 * Class CompanyCustomer, a customer information container for legal entities.
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
    /** @var string $firstname */
    public $firstname;
    /** @var string $lastname */
    public $lastname;
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
    /** $var string $publicKey */
    public $publicKey;

    // set in GetOrdersResponse
    public $streetAddress;          // compounds street + housenumber,fullName, may be set by CreateOrder for i.e. orders where identify customer via ssn

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
     * Required to set street and houseNumber in NL and DE
     * @param string $streetAsString, or $streetAddressAsString iff sole argument
     * @param int $houseNumberAsInt, or omitted if setting streetAddress
     * @return $this
     */
    public function setStreetAddress($streetAsString, $houseNumberAsInt = null) { // = null is poor man's overloading
        // only one name given, assume streetName;
        if( $houseNumberAsInt == null) {
            $streetAddressAsString = $streetAsString;
            $this->streetAddress = $streetAddressAsString;
            $this->street = $streetAsString;    // preserve old behaviour if only street given (assume contains compounded street + housenumber)
        }
        else {
            $this->street = $streetAsString;
            $this->housenumber = $houseNumberAsInt;
        }
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
    /**
    * Optional. Identifier for selecting a specific pre-approved address.
     * @param type $publicKeyAsString
     * @return $this
     */
    public function setPublicKey($publicKeyAsString) {
        $this->publicKey = $publicKeyAsString;
        return $this;
    }
}
