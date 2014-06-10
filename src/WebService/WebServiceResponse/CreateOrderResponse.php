<?php
namespace Svea\WebService;

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
 * @attrib type sveaOrderId -- Unique Id for the created order. Used for any further webservice requests.
 * @attrib type sveaWillBuyOrder
 * @attrib type amount
 * @attrib type expirationDate -- Order expiration date. If the order isn’t delivered before this date the order is automatically closed.
 *
 * The following properties are only present if sent with the order request
 * @property String  clientOrderNumber -- Your reference to the current order.
 * @property String  orderType -- One of {Invoice, Paymentplan}
 * @property CreateOrderIdentity customerIdentity -- invoice address
 * 
 * @author anne-hal, Kristian Grossman-Madsen
 */
class CreateOrderResponse extends WebServiceResponse {

    // always present
    public $sveaOrderId;
    public $sveaWillBuyOrder;
    public $amount;
    public $expirationDate;

    // may be present -- injected iff set in response from Svea
    //public $clientOrderNumber;
    //public $orderType;
    //public $customerIdentity;
    
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
            if (isset($message->CreateOrderEuResult->CreateOrderResult->ClientOrderNumber)) {
                $this->clientOrderNumber = $message->CreateOrderEuResult->CreateOrderResult->ClientOrderNumber;
            }
            if (isset($message->CreateOrderEuResult->CreateOrderResult->OrderType)) {
                $this->orderType = $message->CreateOrderEuResult->CreateOrderResult->OrderType;
            }
            if (isset($message->CreateOrderEuResult->CreateOrderResult->CustomerIdentity)) {
                $this->customerIdentity = new CreateOrderIdentity($message->CreateOrderEuResult->CreateOrderResult->CustomerIdentity);                             
            }
        }
    }
}
