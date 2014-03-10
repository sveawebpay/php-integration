<?php
namespace Svea;

require_once 'WebServiceResponse.php';

/**
 * Handles Svea WebService (Invoice, Payment Plan) CreateOrder request response.
 * 
 * For attribute descriptions, see formatObject() method documentation
 * Possible resultcodes are i.e. 20xxx, 24xxx, 27xxx, 3xxxx, 4xxxx, 5xxxx
 * 
 * CreateOrderResponse structure contains all attributes returned from the Svea
 * webservice.
 *
 * $resultcode -- response specific result code
 *
 * @property integer    sveaOrderId -- Unique Id for the created order. Used for any further webservice requests.
 * @property boolean    sveaWillBuyOrder
 * @property decimal    amount
 * @property datetime   expirationDate -- Order expiration date. If the order isn’t delivered before this date the order is automatically closed.
 *
 * The following properties are only present if sent with the order request
 * @property String     orderType -- One of {Invoice, Paymentplan}
 * @property String     clientOrderNumber -- Your reference to the current order.
 * @property CreateOrderIdentity    customerIdentity -- invoice address
 * 
 * @property customerIdentity->nationalIdNumber
 * @property customerIdentity->email 
 * @property customerIdentity->ipAddress 
 * @property customerIdentity->phoneNumber
 * @property customerIdentity->fullName 
 * @property customerIdentity->street 
 * @property customerIdentity->coAddress 
 * @property customerIdentity->zipCode 
 * @property customerIdentity->houseNumber 
 * @property customerIdentity->locality 
 * @property customerIdentity->countryCode 
 * @property customerIdentity->customerType 
 * 
 * @author anne-hal, Kristian Grossman-Madsen
 */
class CreateOrderResponse extends WebServiceResponse {

    public $sveaOrderId;
    public $sveaWillBuyOrder;
    public $amount;
    public $expirationDate;
    public $customerIdentity;

    protected function formatObject($message) {

        // was request accepted?
        $this->accepted = $message->CreateOrderEuResult->Accepted;
        $this->errormessage = isset($message->CreateOrderEuResult->ErrorMessage) ? $message->CreateOrderEuResult->ErrorMessage : "";        
    
        // set response resultcode
        $this->resultcode = $message->CreateOrderEuResult->ResultCode;

        // set response attributes        
        if ($this->accepted == 1) {

            // always present 
            $this->sveaOrderId = $message->CreateOrderEuResult->CreateOrderResult->SveaOrderId;
            $this->sveaWillBuyOrder = $message->CreateOrderEuResult->CreateOrderResult->SveaWillBuyOrder;
            $this->amount = $message->CreateOrderEuResult->CreateOrderResult->Amount;
            $this->expirationDate = $message->CreateOrderEuResult->CreateOrderResult->ExpirationDate;
            
            // presence not guaranteed
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
        $this->customerIdentity = new CreateOrderIdentity();

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
