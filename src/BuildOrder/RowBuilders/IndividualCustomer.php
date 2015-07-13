<?php
namespace Svea;

/**
 * Class IndividualCustomer, a customer information container for private individuals.
 * @author anne-hal, Kristian Grossman-Madsen
 */
class IndividualCustomer {

    /** @var string $ssn */
    public $ssn;
    /** @var string $initials */
    public $initials;
    /** @var string $birthDate  numeric string on the format yyyymmdd */
    public $birthDate;
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
    /** $var string $publicKey */
    public $publicKey;

    // set in GetOrdersResponse
    public $fullName;               // compounded fullName, may be set by CreateOrder for i.e. orders where identify customer via ssn
    public $streetAddress;          // compounds street + housenumber,fullName, may be set by CreateOrder for i.e. orders where identify customer via ssn

    /**
     * Required for private customers in SE, NO, DK, FI
     * @param string for SE, DK:  $yyyymmddxxxx, for FI:  $ddmmyyxxxx, NO:  $ddmmyyxxxxx
     * @return $this
     */
    public function setNationalIdNumber($nationalIdNumber) {
        $this->ssn = $nationalIdNumber;
        return $this;
    }

    /**
     * Required for private customers in NL
     * @param string $initialsAsString
     * @return $this
     */
    public function setInitials($initialsAsString) {
        $this->initials = $initialsAsString;
        return $this;
    }

    /**
     * Required for private customers in NL and DE
     * @param string $yyyy or $yyyymmdd
     * @param string $mm
     * @param string $dd
     * @return $this
     * @throws InvalidArgumentException in case of bad birthdate string format
     */
    public function setBirthDate($yyyy, $mm = null, $dd = null) {
        if( $mm == null && $dd == null ) { // poor man's overloading
            $yyyymmdd = $yyyy;
            if( strlen($yyyymmdd) != 8 ) {
                throw new \InvalidArgumentException( 'setBirthDate expects arguments on format $yyyy, $mm, $dd or $yyyymmdd' );
            }
            else {
                $yyyy = substr($yyyymmdd,0,4);
                $mm = substr($yyyymmdd,4,2);
                $dd = substr($yyyymmdd,6,2);
            }
        }
        if ($mm < 10) {$mm = "0".intval($mm); }
        if ($dd < 10) {$dd = "0".intval($dd); }

        $this->birthDate = $yyyy . $mm . $dd;
        return $this;
    }

    /**
     * Optional but desirable
     * @param string $emailAsString
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
     * Optional but desirable
     * @param string $ipAddressAsString
     * @return $this
     */
    public function setIpAddress($ipAddressAsString) {
        $this->ipAddress = $ipAddressAsString;
        return $this;
    }

    /**
     * Required to set firstName and lastName for private Customers in NL and DE
     * @param string $firstnameAsString, or $fullNameAsString iff sole argument
     * @param string $lastnameAsString, or omitted if setting fullName
     * @return $this
     */
    public function setName($firstnameAsString, $lastnameAsString = null) { // = null is poor man's overloading
        // only one name given, assume fullName;
        if( $lastnameAsString == null) {
            $fullNameAsString = $firstnameAsString;
            $this->name = $fullNameAsString;
        }
        // two names given, assume firstName and lastName
        else {
            $this->firstname = $firstnameAsString;
            $this->lastname = $lastnameAsString;
        }
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
     * @param string $coAddressAsString
     * @return $this
     */
    public function setCoAddress($coAddressAsString) {
        $this->coAddress = $coAddressAsString;
        return $this;
    }

    /**
     * Requuired in NL and DE
     * @param string $zipCodeAsString
     * @return $this
     */
    public function setZipCode($zipCodeAsString) {
        $this->zipCode = $zipCodeAsString;
        return $this;
    }

    /**
     * Required in NL and DE
     * @param string $cityAsString
     * @return $this
     */
    public function setLocality($cityAsString) {
        $this->locality = $cityAsString;
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
