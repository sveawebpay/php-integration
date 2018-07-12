<?php
/**
 * Class for OrderDeliveryAddress which is used to set a delivery address that differs from the address that's registered on the individual or company
 *
 * Can only be used if approved by Svea, otherwise it an error will be returned. Contact Svea if you want to use this function
 *
 * @author Fredrik Sundell
 *
 */

namespace Svea\WebPay\BuildOrder\RowBuilders;


class OrderDeliveryAddress
{
    public $fullName;
    public $firstName;
    public $lastName;
    public $streetAddress;
    public $coAddress;
    public $zipCode;
    public $houseNumber;
    public $locality;
    public $countryCode;
    /**
     * @return mixed
     */
    /**
     * @param mixed $fullName
     * @return $this
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @param mixed $firstName
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @param mixed $lastName
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @param mixed $streetAddress
     * @return $this
     */
    public function setStreetAddress($streetAddress)
    {
        $this->streetAddress = $streetAddress;

        return $this;
    }

    /**
     * @param mixed $coAddress
     * @return $this
     */
    public function setCoAddress($coAddress)
    {
        $this->coAddress = $coAddress;

        return $this;
    }

    /**
     * @param mixed $zipCode
     * @return mixed
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * @param mixed $houseNumber
     * @return $this
     */
    public function setHouseNumber($houseNumber)
    {
        $this->houseNumber = $houseNumber;

        return $this;
    }

    /**
     * @param mixed $locality
     * @return $this
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * @param mixed $countryCode
     * @return $this
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }
}