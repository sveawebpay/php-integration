<?php
namespace Svea\WebService;

require_once 'WebServiceResponse.php';

/**
 * Handles Svea WebService (Invoice, Payment Plan) CreateOrder request response.
 * 
 * CreateOrderResponse structure contains all attributes returned from the Svea
 * webservice.
 * 
 * Possible resultcodes are i.e. 20xxx, 24xxx, 27xxx, 3xxxx, 4xxxx, 5xxxx, see svea webpay_eu_webservice documentation
 * 
 * @author anne-hal, Kristian Grossman-Madsen
 */
class CreateOrderResponse extends WebServiceResponse {

    /** @var string $sveaOrderId  Always present. Unique Id for the created order. Used for any further webservice requests. */
    public $sveaOrderId;
    /** @var string $orderType Always present. One of {Invoice|PaymentPlan} */
    public $orderType;  // TODO java: enum
    /** @var string $sveaWillBuyOrder  Always present. */
    public $sveaWillBuyOrder;   // TODO java: boolean
    /** @var string $amount  Always present. The total amount including VAT, presented as a decimal number. */
    public $amount;
    /** @var string $expirationDate  Always present. Order expiration date. If the order isn’t delivered before this date the order is automatically closed. */
    /** @var CreateOrderIdentity $customerIdentity  May be present. Contains invoice address. */
    public $customerIdentity;
    public $expirationDate;
    /** @var string $clientOrderNumber  May be present. If passed in with request, a reference to the current order. */
    public $clientOrderNumber;
    
    public function __construct($response) {
        
        // was request accepted?
        $this->accepted = $response->CreateOrderEuResult->Accepted;
        $this->errormessage = isset($response->CreateOrderEuResult->ErrorMessage) ? $response->CreateOrderEuResult->ErrorMessage : "";        
    
        // set response resultcode
        $this->resultcode = $response->CreateOrderEuResult->ResultCode;

        // set response attributes        
        if ($this->accepted == 1) {

            // always present 
            $this->sveaOrderId = $response->CreateOrderEuResult->CreateOrderResult->SveaOrderId;
            $this->sveaWillBuyOrder = $response->CreateOrderEuResult->CreateOrderResult->SveaWillBuyOrder;
            $this->amount = $response->CreateOrderEuResult->CreateOrderResult->Amount;
            $this->expirationDate = $response->CreateOrderEuResult->CreateOrderResult->ExpirationDate;
            
            // presence not guaranteed
            if (isset($response->CreateOrderEuResult->CreateOrderResult->ClientOrderNumber)) {
                $this->clientOrderNumber = $response->CreateOrderEuResult->CreateOrderResult->ClientOrderNumber;
            }
            if (isset($response->CreateOrderEuResult->CreateOrderResult->OrderType)) {
                $this->orderType = $response->CreateOrderEuResult->CreateOrderResult->OrderType;
            }
            if (isset($response->CreateOrderEuResult->CreateOrderResult->CustomerIdentity)) {
                $this->customerIdentity = new CreateOrderIdentity($response->CreateOrderEuResult->CreateOrderResult->CustomerIdentity);                             
            }
        }
    }
}
