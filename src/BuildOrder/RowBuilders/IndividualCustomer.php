<?php
namespace Svea;

/**
 * @author anne-hal
 */
class IndividualCustomer {
    
    /**
     * Required for private customers in SE, NO, DK, FI
     * @param for SE, DK:  $yyyymmddxxxx
     * @param for FI:  $ddmmyyxxxx
     * @param for NO:  $ddmmyyxxxxx
     * @return \IndividualCustomer
     */
    public function setNationalIdNumber($nationalIdNumber) {
        $this->ssn = $nationalIdNumber;
        return $this;
    }

    /**
     * Required for private customers in NL
     * @param type $initialsAsString
     * @return \IndividualCustomer
     */
    public function setInitials($initialsAsString) {
        $this->initials = $initialsAsString;
        return $this;
    }

    /**
     * Required for private customers in NL and DE
     * @param type $yyyy
     * @param type $mm
     * @param type $dd
     * @return \IndividualCustomer
     */
    public function setBirthDate($yyyy, $mm, $dd) {
        if ($mm < 10) {$mm = "0".$mm; }
        if ($dd < 10) {$dd = "0".$dd; }

        $this->birthDate = $yyyy . $mm . $dd;
        return $this;
    }

   /**
     * Optional but desirable
     * @param type $emailAsString
     * @return \IndividualCustomer
     */
    public function setEmail($emailAsString) {
        $this->email = $emailAsString;
        return $this;
    }
    
     /**
     * Optional
     * @param type $phoneNumberAsInt
     * @return \IndividualCustomer
     */
    public function setPhoneNumber($phoneNumberAsInt) {
        $this->phonenumber = $phoneNumberAsInt;
        return $this;
    }
    
    /**
     * Optinal but desirable
     * @param type $ipAddressAsString
     * @return \IndividualCustomer
     */
    public function setIpAddress($ipAddressAsString) {
        $this->ipAddress = $ipAddressAsString;
        return $this;
    }
    
    /**
     * Required for private Customers in NL and DE
     * @param type $firstnameAsString
     * @param type $lastnameAsString
     * @return \IndividualCustomer
     */
    public function setName($firstnameAsString, $lastnameAsString) {
        $this->firstname = $firstnameAsString;
        $this->lastname = $lastnameAsString;
        return $this;
    }
    
    /**
     * Required in NL and DE
     * For other countries, you may ommit this, or let either of street and/or housenumber be empty
     * 
     * @param type $streetAsString
     * @param type $houseNumberAsInt
     * @return \IndividualCustomer
     */
    public function setStreetAddress($streetAsString, $houseNumberAsInt) {
        $this->street = $streetAsString;
        $this->housenumber = $houseNumberAsInt;
        return $this;
    }
    
    /**
     * Optional in NL and DE
     * @param type $coAddressAsString
     * @return \IndividualCustomer
     */
    public function setCoAddress($coAddressAsString) {
        $this->coAddress = $coAddressAsString;
        return $this;
    }
    
    /**
     * Requuired in NL and DE
     * @param type $zipCodeAsString
     * @return \IndividualCustomer
     */
    public function setZipCode($zipCodeAsString) {
        $this->zipCode = $zipCodeAsString;
        return $this;
    }
    
    /**
     * Required in NL and DE
     * @param type $cityAsString
     * @return \IndividualCustomer
     */
    public function setLocality($cityAsString) {
        $this->locality = $cityAsString;
        return $this;
    }
}
