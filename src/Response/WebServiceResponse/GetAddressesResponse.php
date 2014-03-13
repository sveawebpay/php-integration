<?php
namespace Svea;

require_once 'WebServiceResponse.php';

/**
 * Handles the Svea Webservice GetAddresses request response.
 *
 * For attribute descriptions, see formatObject() method documentation
 * Possible resultcodes are {Error, Accepted, NoSuchEntity}
 * 
 * @attrib string $resultcode -- response specific result code
 * @attrib array of GetAddressIdentity $customerIdentity
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
     *  $response->errormessage             // may be set if accepted above is false
     *
     *  $response->resultcode               // one of {Error, Accepted, NoSuchEntity}
     * 
     *  $response->$customerIdentity[0..n] // array of GetAddressIdentity
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
            $temp = new GetAddressIdentity( $customer );
            
            array_push($this->customerIdentity, $temp);
        }
    }
}
