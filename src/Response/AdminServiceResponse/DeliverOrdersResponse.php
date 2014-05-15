<?php
namespace Svea;

/**
 * Handles the Svea Admin Web Service CancelOrder request response.
 * 
 * @author Kristian Grossman-Madsen
 */
class DeliverOrdersResponse {

    /** @var int $accepted  true iff request was accepted by the service */
    public $accepted;    
    /** @var int $resultcode  response specific result code */
    public $resultcode;

    
    /** @var string errormessage  may be set iff accepted above is false */
    public $errormessage;   
    
    /** @var float $amount  (set iff accepted) the amount delivered with this request */
    public $amount;

    /** @var string $orderType  (set iff accepted)  one of [Invoice|PaymentPlan] */
    public $orderType;
    
    /** @var numeric $invoiceId  (set iff accepted, orderType Invoice)  the invoice id for the delivered order */
    public $invoiceId;

    /** @var numeric $contractNumber  (set iff accepted, orderType PaymentPlan)  the contract number for the delivered order */
    public $contractNumber;

    
    function __construct($message) {
        $this->formatObject($message);  
    }
    
    protected function formatObject($message) {
        // was request accepted?
        $this->accepted = $message->ResultCode == 0 ? 1 : 0; // ResultCode of 0 means all went well.
        $this->errormessage = isset($message->ErrorMessage) ? $message->ErrorMessage : "";
        $this->resultcode = $message->ResultCode;

        // if successful, set deliverOrderResult, using the same attributes as for DeliverOrderEU?
        if ($this->accepted == 1) {

            $this->amount = $message->OrdersDelivered->DeliverOrderResult->DeliveredAmount;
            $this->orderType = $message->OrdersDelivered->DeliverOrderResult->OrderType;
            if( $this->orderType == "Invoice" ) {
                $this->invoiceId = $message->OrdersDelivered->DeliverOrderResult->DeliveryReferenceNumber;
            } 
            else {
                $this->contractNumber = $message->OrdersDelivered->DeliverOrderResult->DeliveryReferenceNumber;
            }
            // we ignore ClientId and SveaOrderId
            //[ClientId] => 79021
            //[SveaOrderId] => 346761        
        }
    }
}

//Admin DeliverOrders Invoice response example:
//
//<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
//   <s:Body>
//      <DeliverOrdersResponse xmlns="http://tempuri.org/">
//         <DeliverOrdersResult xmlns:a="http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
//            <a:ErrorMessage i:nil="true"/>
//            <a:ResultCode>0</a:ResultCode>
//            <a:OrdersDelivered>
//               <a:DeliverOrderResult>
//                  <a:ClientId>79021</a:ClientId>
//                  <a:DeliveredAmount>250.00</a:DeliveredAmount>
//                  <a:DeliveryReferenceNumber>1026571</a:DeliveryReferenceNumber>
//                  <a:OrderType>Invoice</a:OrderType>
//                  <a:SveaOrderId>346812</a:SveaOrderId>
//               </a:DeliverOrderResult>
//            </a:OrdersDelivered>
//         </DeliverOrdersResult>
//      </DeliverOrdersResponse>
//   </s:Body>
//</s:Envelope>

//stdClass Object
//(
//    [ErrorMessage] => 
//    [ResultCode] => 0
//    [OrdersDelivered] => stdClass Object
//        (
//            [DeliverOrderResult] => stdClass Object
//                (
//                    [ClientId] => 79021
//                    [DeliveredAmount] => 250.00
//                    [DeliveryReferenceNumber] => 1026572
//                    [OrderType] => Invoice
//                    [SveaOrderId] => 346761
//                )
//
//        )
//
//)
