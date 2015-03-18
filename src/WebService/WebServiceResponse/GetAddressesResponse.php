<?php
namespace Svea\WebService;

require_once 'WebServiceResponse.php';

/**
 * The Webpay::getAddresses request returns an instance of GetAddressesResponse, containing the actual customer addresses in an array of
 * GetAddressIdentity:
 *      
 *      $response = WebPay::getAddresses($myConfig);
 * 
 *      // GetAddressResponse attributes:
 *      $response->accepted;                        // Boolean  // true iff request was accepted
 *      $response->resultcode;                      // String   // set iff accepted false
 *      $response->errormessage;                    // String   // set iff accepted false
 *      $response->customerIdentity;                // Array of GetAddressIdentity
 * 
 *      $firstCustomerAddress = $myGetAddressesResponse->customerIdentity[0];
 * 
 *      // GetAddressIdentity attributes:
 *      $firstCustomerAddress->customerType;        // String   // "Person" or "Business" for individual and company customers, respectively
 *      $firstCustomerAddress->nationalIdNumber;    // Numeric  // national id number of individual or company 
 *      $firstCustomerAddress->fullName;            // String   // amalgated firstname and surname for indivdual, or company name for company customers
 *      $firstCustomerAddress->coAddress;           // String   // optional
 *      $firstCustomerAddress->street;              // String   // required, streetname including housenumber
 *      $firstCustomerAddress->zipCode;             // String   // required
 *      $firstCustomerAddress->locality;            // String   // required, city name
 *      $firstCustomerAddress->phoneNumber;         // String   // optional
 *      $firstCustomerAddress->firstName;           // String   // optional, present in GetAddressResponse, not returned in CreateOrderResponse
 *      $firstCustomerAddress->lastName;            // String   // optional, present in GetAddressResponse, not returned in CreateOrderResponse
 *      $firstCustomerAddress->addressSelector      // String   // optional, uniquely disambiguates company addresses      
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
