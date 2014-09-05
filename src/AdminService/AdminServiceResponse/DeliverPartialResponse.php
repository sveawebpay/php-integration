<?php
namespace Svea\AdminService;

require_once 'AdminServiceResponse.php';

/**
 * Handles the Svea Admin Web Service DeliverPartial request response.
 * 
 * @author Kristian Grossman-Madsen
 */
class DeliverPartialResponse extends AdminServiceResponse {
  
    /** @var float $amount  (set iff accepted) the amount delivered with this request */
    public $amount;

    /** @var string $orderType  (set iff accepted)  one of [Invoice|PaymentPlan] */
    public $orderType;
    
    /** @var numeric $invoiceId  (set iff accepted, orderType Invoice)  the invoice id for the delivered order */
    public $invoiceId;
   
    function __construct($message) {
        $this->formatObject($message);  
    }    
        
    /**
     * Parses response and sets attributes.
     */    
    protected function formatObject($message) {
        parent::formatObject($message);
        
        if ($this->accepted == 1) {

            $this->rawDeliverPartialResponse = $message;

            $this->amount = $message->OrdersDelivered->DeliverOrderResult->DeliveredAmount;
            $this->orderType = $message->OrdersDelivered->DeliverOrderResult->OrderType;
            if( $this->orderType == "Invoice" ) {
                $this->invoiceId = $message->OrdersDelivered->DeliverOrderResult->DeliveryReferenceNumber;
            } 
        }            
    }
}  

// Example DeliverPartialResponse:
//Svea\AdminService\DeliverPartialResponse Object
//(
//    [amount] => 250.00
//    [orderType] => Invoice
//    [invoiceId] => 1033413
//    [accepted] => 1
//    [resultcode] => 0
//    [errormessage] => 
//    [rawDeliverPartialResponse] => stdClass Object
//        (
//            [ErrorMessage] => 
//            [ResultCode] => 0
//            [OrdersDelivered] => stdClass Object
//                (
//                    [DeliverOrderResult] => stdClass Object
//                        (
//                            [ClientId] => 79021
//                            [DeliveredAmount] => 250.00
//                            [DeliveryReferenceNumber] => 1033413
//                            [OrderType] => Invoice
//                            [SveaOrderId] => 410449
//                        )
//
//                )
//
//        )
//
//)