<?php
namespace Svea\WebService;

require_once 'WebServiceResponse.php';

/**
 * Handles the Svea WebService GetAddresses request response. 
 * (Note that this maps to the legacy Web Service, not the Europe Web Service GetAddresses request.)
 *
 * For attribute descriptions, see the formatObject() method documentation
 * 
 *     $response->accepted                 // boolean, true iff Svea accepted request
 *     $response->resultcode               // may contain an error code
 *     $response->customerIdentity         // if accepted, may define a GetAddressIdentity object:
 *         ->customerType;       // not guaranteed to be defined
 *         ->nationalIdNumber;   // not guaranteed to be defined
 *         ->phoneNumber;        // not guaranteed to be defined
 *        ->firstName;          // not guaranteed to be defined
 *         ->lastName;           // not guaranteed to be defined
 *         ->fullName;           // not guaranteed to be defined
 *         ->street;             // not guaranteed to be defined
 *        ->coAddress;          // not guaranteed to be defined
 *         ->zipCode;            // not guaranteed to be defined
 *         ->locality;           // not guaranteed to be defined
 * 
 * @author anne-hal, Kristian Grossman-Madsen
 */
class GetAddressesResponse extends WebServiceResponse{
    
    /** @var GetAddressIdentity  array of GetAddressIdentity */
    public $customerIdentity = array();
    
    public function __construct($response) {
        
        // was request accepted?
        if( $response->GetAddressesResult->RejectionCode == "Error" ) {
            $this->accepted = 0;
        }
        else {
            $this->accepted = $response->GetAddressesResult->Accepted;
        }
        $this->resultcode = $response->GetAddressesResult->RejectionCode;
        $this->errormessage = isset($response->GetAddressesResult->ErrorMessage) ? $response->GetAddressesResult->ErrorMessage : "";        

        // set response attributes
        if (property_exists($response->GetAddressesResult, "Addresses") && $this->accepted == 1) {
            $this->formatCustomerIdentity($response->GetAddressesResult->Addresses);
        }
    }

    private function formatCustomerIdentity($customers) {

        is_array($customers->CustomerAddress) ? $loopValue = $customers->CustomerAddress : $loopValue = $customers;
        
        foreach ($loopValue as $customer) {
            $temp = new GetAddressIdentity( $customer );
            
            array_push($this->customerIdentity, $temp);
        }
    }
}

//    $response->accepted                 // boolean, true iff Svea accepted request
//    $response->resultcode               // may contain an error code
//    $response->customerIdentity         // if accepted, may define a GetAddressIdentity object:
//        ->customerType;       // not guaranteed to be defined
//        ->nationalIdNumber;   // not guaranteed to be defined
//        ->phoneNumber;        // not guaranteed to be defined
//        ->firstName;          // not guaranteed to be defined
//        ->lastName;           // not guaranteed to be defined
//        ->fullName;           // not guaranteed to be defined
//        ->street;             // not guaranteed to be defined
//        ->coAddress;          // not guaranteed to be defined
//        ->zipCode;            // not guaranteed to be defined
//        ->locality;           // not guaranteed to be defined
