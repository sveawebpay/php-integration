<?php
namespace Svea;

require_once 'WebServiceResponse.php';

/**
 * 
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CreateOrderResponse extends WebServiceResponse {

    public $sveaOrderId;
    public $sveaWillBuyOrder;
    public $amount;
    public $expirationDate;
    public $customerIdentity;

    function __construct($message) {
        if (isset($message->CreateOrderEuResult->ErrorMessage)) {
            $this->errormessage = $message->CreateOrderEuResult->ErrorMessage;
        }
        parent::__construct($message);
    }

    protected function formatObject($message) {
        //Required
        $this->accepted = $message->CreateOrderEuResult->Accepted;
        $this->resultcode = $message->CreateOrderEuResult->ResultCode;
        if ($this->accepted == 1) {
            $this->sveaOrderId = $message->CreateOrderEuResult->CreateOrderResult->SveaOrderId;
            $this->sveaWillBuyOrder = $message->CreateOrderEuResult->CreateOrderResult->SveaWillBuyOrder;
            $this->amount = $message->CreateOrderEuResult->CreateOrderResult->Amount;
            $this->expirationDate = $message->CreateOrderEuResult->CreateOrderResult->ExpirationDate;
            //Optional
            if (isset($message->CreateOrderEuResult->CreateOrderResult->OrderType)) {
                $this->orderType = $message->CreateOrderEuResult->CreateOrderResult->OrderType;
            }
            if (isset($message->CreateOrderEuResult->CreateOrderResult->ClientOrderNumber)) {
                $this->clientOrderNumber = $message->CreateOrderEuResult->CreateOrderResult->ClientOrderNumber;
            }
            if (isset($message->CreateOrderEuResult->CreateOrderResult->CustomerIdentity)) {
                $this->formatCustomerIdentity($message->CreateOrderEuResult->CreateOrderResult->CustomerIdentity);
            }
        }
    }

    public function formatCustomerIdentity($customer) {
        $this->customerIdentity = new CreateOrderIdentity();//new CustomerIdentityPaymentResponse($message->CreateOrderEuResult->CreateOrderResult->CustomerIdentity);
              //required
        $this->customerIdentity->customerType = $customer->CustomerType;
        //optional
        if (property_exists($customer, "NationalIdNumber") && $customer->NationalIdNumber != "") {
            $this->customerIdentity->nationalIdNumber = $customer->NationalIdNumber;
        }
        $this->customerIdentity->email = isset($customer->Email) ? $customer->Email : "";
        $this->customerIdentity->ipAddress = isset($customer->IpAddress) ? $customer->IpAddress : "";
        $this->customerIdentity->phoneNumber = isset($customer->PhoneNumber) ? $customer->PhoneNumber : "";
        $this->customerIdentity->fullName = isset($customer->FullName) ? $customer->FullName : "";
        $this->customerIdentity->street = isset($customer->Street) ? $customer->Street : "";
        $this->customerIdentity->coAddress = isset($customer->CoAddress) ? $customer->CoAddress : "";
        $this->customerIdentity->zipCode = isset($customer->ZipCode) ? $customer->ZipCode : "";
        $this->customerIdentity->houseNumber = isset($customer->HouseNumber) ? $customer->HouseNumber : "";
        $this->customerIdentity->locality = isset($customer->Locality) ? $customer->Locality : "";
        $this->customerIdentity->countryCode = isset($customer->CountryCode) ? $customer->CountryCode : "";
        $this->customerIdentity->customerType = isset($customer->CustomerType) ? $customer->CustomerType : "";
    }
}
