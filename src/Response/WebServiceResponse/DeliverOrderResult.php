<?php
require_once 'WebServiceResponse.php';
/**
 * Description of DeliverOrderResult
 *
 * @author anne-hal
 */
class DeliverOrderResult extends WebServiceResponse{
   
    public $amount;
    public $orderType;


    function __construct($message) {
        parent::__construct($message);
        if(isset($message->DeliverOrderEuResult->ErrorMessage))
        $this->errormessage = $message->DeliverOrderEuResult->ErrorMessage;
    }
    
     protected function formatObject($message){
        $this->accepted = $message->DeliverOrderEuResult->Accepted;
        $this->resultcode = $message->DeliverOrderEuResult->ResultCode;
        
        $this->amount = $message->DeliverOrderEuResult->DeliverOrderResult->Amount;
        $this->orderType = $message->DeliverOrderEuResult->DeliverOrderResult->OrderType;
        if(property_exists($message->DeliverOrderEuResult->DeliverOrderResult, "InvoiceResultDetails")){
            $this->invoiceId = $message->DeliverOrderEuResult->DeliverOrderResult->InvoiceResultDetails->InvoiceId;
            $this->dueDate = $message->DeliverOrderEuResult->DeliverOrderResult->InvoiceResultDetails->DueDate;
            $this->invoiceDate = $message->DeliverOrderEuResult->DeliverOrderResult->InvoiceResultDetails->InvoiceDate;
            $this->invoiceDistributionType = $message->DeliverOrderEuResult->DeliverOrderResult->InvoiceResultDetails->InvoiceDistributionType;
        }elseif (property_exists($message->DeliverOrderEuResult->DeliverOrderResult, "PaymentPlanResultDetails")) {
            $this->contractNumber = $message->DeliverOrderEuResult->DeliverOrderResult->PaymentPlanResultDetails->ContractNumber;
        }
                
        $this->orderType = $message->DeliverOrderEuResult->DeliverOrderResult->OrderType;

     }
}

?>
