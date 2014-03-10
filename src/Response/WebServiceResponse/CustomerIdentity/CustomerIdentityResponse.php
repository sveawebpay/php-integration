<?php
namespace Svea;

/**
 *  CustomerIdentityResponse structure
 * 
 *  @attrib $customerType;       // not guaranteed to be defined
 *  @attrib $nationalIdNumber;   // not guaranteed to be defined
 *  @attrib $phoneNumber;        // not guaranteed to be defined
 *  @attrib $firstName;          // not guaranteed to be defined
 *  @attrib $lastName;           // not guaranteed to be defined
 *  @attrib $fullName;           // not guaranteed to be defined
 *  @attrib $street;             // not guaranteed to be defined
 *  @attrib $coAddress;          // not guaranteed to be defined
 *  @attrib $zipCode;            // not guaranteed to be defined
 *  @attrib $locality;           // not guaranteed to be defined
 *
 *  @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea Webpay
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
