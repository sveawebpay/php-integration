<?php
namespace Svea;

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CustomerIdentityResponse {

    public $customerType;       // not guaranteed to be defined
    public $nationalIdNumber;   // not guaranteed to be defined
    public $phoneNumber;        // not guaranteed to be defined
    public $firstName;          // not guaranteed to be defined
    public $lastName;           // not guaranteed to be defined
    public $fullName;           // not guaranteed to be defined
    public $street;             // not guaranteed to be defined
    public $coAddress;          // not guaranteed to be defined
    public $zipCode;            // not guaranteed to be defined
    public $locality;           // not guaranteed to be defined
}
