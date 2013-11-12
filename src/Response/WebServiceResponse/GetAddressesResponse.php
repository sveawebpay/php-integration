<?php
namespace Svea;

require_once 'WebServiceResponse.php';

/**
 * @author anne-hal
 */
class GetAddressesResponse extends WebServiceResponse{

    function __construct($message) {
        if (isset($message->GetAddressesResult->ErrorMessage)) {
            $this->errormessage = $message->GetAddressesResult->ErrorMessage;
        }
        parent::__construct($message);
    }

    protected function formatObject($message) {
        //Required
        $this->accepted = $message->GetAddressesResult->Accepted;
        $this->resultcode = $message->GetAddressesResult->RejectionCode; //Whet update comes

        if (property_exists($message->GetAddressesResult, "Addresses") && $this->accepted == 1) {
            $this->formatCustomerIdentity($message->GetAddressesResult->Addresses);
        }
    }

    public function formatCustomerIdentity($customers) {
        $this->customerIdentity = array();
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
