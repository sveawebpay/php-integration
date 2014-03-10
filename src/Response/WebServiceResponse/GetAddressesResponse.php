<?php
namespace Svea;

require_once 'WebServiceResponse.php';

/**
 * Handles the Svea Webservice GetAddresses request response.
 * 
 * For attribute descriptions, see formatObject() method documentation
 * Possible resultcodes are {Error, Accepted, NoSuchEntity}
 * 
 * @attrib $resultcode -- response specific result code
 * @attrib $customerIdentity -- array of GetAddressIdentity
 * 
 * @author anne-hal, Kristian Grossman-Madsen
 */
class GetAddressesResponse extends WebServiceResponse{

    public $resultcode;
    public $customerIdentity = array();  // array of GetAddressIdentity
    
    /**
     *  formatObject sets the following attributes:
     * 
     *  $response->accepted                 // true iff request was accepted by the service 
     *  $response->errormessage             // may be set iff accepted above is false
     *
     *  $response->resultcode               // one of {Error, Accepted, NoSuchEntity}
     * 
     *   $response->$customerIdentity[0..n] // array of GetAddressIdentity
     *      ->customerType 
     *      ->nationalIdNumber
     *      ->phoneNumber 
     *      ->firstName
     *      ->lastName
     *      ->fullName
     *      ->street
     *      ->coAddress
     *      ->zipCode 
     *      ->locality
     *      ->addressSelector 
     */
    
    protected function formatObject($message) {
        
        // was request accepted?
        $this->accepted = $message->GetAddressesResult->Accepted;
        $this->errormessage = isset($message->GetAddressesResult->ErrorMessage) ? $message->GetAddressesResult->ErrorMessage : "";        

        // set response resultcode
        $this->resultcode = $message->GetAddressesResult->RejectionCode;

        // set response attributes
        if (property_exists($message->GetAddressesResult, "Addresses") && $this->accepted == 1) {
            $this->formatCustomerIdentity($message->GetAddressesResult->Addresses);
        }
    }

    public function formatCustomerIdentity($customers) {

        is_array($customers->CustomerAddress) ? $loopValue = $customers->CustomerAddress : $loopValue = $customers;

        foreach ($loopValue as $customer) {
            $temp = new GetAddressIdentity();
            
            $temp->customerType = $customer->BusinessType;
            $temp->nationalIdNumber = isset($customer->SecurityNumber) ? $customer->SecurityNumber : "";
            $temp->phoneNumber = isset($customer->PhoneNumber) ? $customer->PhoneNumber : "";
            $temp->firstName = isset($customer->FirstName) ? $customer->FirstName : "";
            $temp->lastName = isset($customer->LastName) ? $customer->LastName : "";
            $temp->fullName = isset($customer->LegalName) ? $customer->LegalName : "";
            $temp->street = isset($customer->AddressLine2) ? $customer->AddressLine2 : "";
            $temp->coAddress = isset($customer->AddressLine1) ? $customer->AddressLine1 : "";
            $temp->zipCode = isset($customer->Postcode) ? $customer->Postcode : "";
            $temp->locality = isset($customer->Postarea) ? $customer->Postarea : "";
            $temp->addressSelector = isset($customer->AddressSelector) ? $customer->AddressSelector : "";

            array_push($this->customerIdentity, $temp);
        }
    }
}
