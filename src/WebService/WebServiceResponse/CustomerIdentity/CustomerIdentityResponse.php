<?php
namespace Svea\WebService;

/**
 *  CustomerIdentityResponse structure
 * 
 *  @attrib $customerType;       
 *  @attrib $nationalIdNumber;   
 *  @attrib $phoneNumber;        
 *  @attrib $fullName;           
 *  @attrib $street;             
 *  @attrib $coAddress;          
 *  @attrib $zipCode;            
 *  @attrib $locality;           
 *
 *  @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea Webpay
 */  
class CustomerIdentityResponse {

    public $customerType;       
    public $nationalIdNumber;   
    public $phoneNumber;        
    public $fullName;           
    public $street;             
    public $coAddress;          
    public $zipCode;            
    public $locality;      
    
    /**
     * populates the CustomerIdentityResponse object
     * 
     * @param object $customer -- response from either legacy GetAddresses or CreateOrderEU
     */
    function __construct( $customer ) {        
        
        if( isset($customer->BusinessType) ) { // GetAddressesResponse (Legacy webservice)
        
            $this->customerType = $customer->BusinessType;
            $this->nationalIdNumber = isset($customer->SecurityNumber) ? $customer->SecurityNumber : "";
            $this->phoneNumber = isset($customer->PhoneNumber) ? $customer->PhoneNumber : "";
            $this->firstName = isset($customer->FirstName) ? $customer->FirstName : "";
            $this->lastName = isset($customer->LastName) ? $customer->LastName : "";
            $this->fullName = isset($customer->LegalName) ? $customer->LegalName : "";
            $this->street = isset($customer->AddressLine2) ? $customer->AddressLine2 : "";
            $this->coAddress = isset($customer->AddressLine1) ? $customer->AddressLine1 : "";
            $this->zipCode = isset($customer->Postcode) ? $customer->Postcode : "";
            $this->locality = isset($customer->Postarea) ? $customer->Postarea : "";
        }
        else { // CreateOrderResponse (EU webservice)
            $this->customerType = isset($customer->CustomerType) ? $customer->CustomerType : "";
            $this->nationalIdNumber = isset($customer->NationalIdNumber) ? $customer->NationalIdNumber : "";
            $this->phoneNumber = isset($customer->PhoneNumber) ? $customer->PhoneNumber : "";
            $this->fullName = isset($customer->FullName) ? $customer->FullName : "";
            $this->street = isset($customer->Street) ? $customer->Street : "";
            $this->coAddress = isset($customer->CoAddress) ? $customer->CoAddress : "";
            $this->zipCode = isset($customer->ZipCode) ? $customer->ZipCode : "";
            $this->locality = isset($customer->Locality) ? $customer->Locality : "";          
        }
    }
}