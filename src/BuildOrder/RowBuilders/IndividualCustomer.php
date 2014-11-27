<?php
namespace Svea;

/**
 * Class IndividualCustomer, a customer information container for private individuals.
 *
 * The IndividualCustomer attributes are used by the invoice and payment plan payment methods
 * to identify the customer. Which attributes are required varies according to country.
 *
 * (For card and direct bank orders, adding customer information to the order is optional.)
 *
 *     $order->addCustomerDetails(
 *         WebPayItem::individualCustomer()
 *             ->setNationalIdNumber(194605092222) // required for individual customers in SE, NO, DK, FI
 *             ->setInitials("SB")                 // required for individual customers in NL
 *             ->setBirthDate(1923, 12, 20)        // required for individual customers in NL and DE
 *             ->setName("Tess", "Testson")        // required for individual customers in NL and DE
 *             ->setStreetAddress("Gatan", 23)     // required in NL and DE
 *             ->setZipCode(9999)                  // required in NL and DE
 *             ->setLocality("Stan")               // required in NL and DE
 *             ->setEmail("test@svea.com")         // optional but desirable
 *             ->setIpAddress("123.123.123")       // optional but desirable
 *             ->setCoAddress("c/o Eriksson")      // optional
 *             ->setPhoneNumber(999999)            // optional
 *     )
 * ;
 *
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
     */
    public function setBirthDate($yyyy, $mm = null, $dd = null) {
        if( $mm == null && $dd == null ) { // poor man's overloading
            $yyyymmdd = $yyyy;
            if( strlen($yyyymmdd) != 8 ) {
                throw new \InvalidArgumentException;
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
     * Required for private Customers in NL and DE
     * @param string $firstnameAsString
     * @param string $lastnameAsString
     * @return $this
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
}
