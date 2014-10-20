<?php
namespace Svea\WebService;

require_once 'WebServiceResponse.php';

/**
 * @author anne-hal, Kristian Grossman-Madsen
 */
class DeliverOrderResult extends WebServiceResponse{

    /** @var float $amount  The sum of all the provided order rows. */
    public $amount;

    /** @var string $orderType  Indicates the payment method selected. */
    public $orderType;
    
    /** @var long $invoiceId  May be present. Svea invoice id received when delivering an invoice order. */    
    public $invoiceId;
    /** @var string $dueDate  May be present.  Due date for the invoice when SveaWebPay wants the invoice to be payed. */    
    public $dueDate;
    /** @var string $invoiceDate  May be present. Date when the invoice is created in Svea’s system. Due date is InvoiceDate + Credit days. */    
    public $invoiceDate;
    /** @var string $invoiceDistributionType  May be present. See DistributionType class for possible values.*/    
    public $invoiceDistributionType;
    /** @var string $ocr  May be present. */    
    public $ocr;
    /** @var long $lowestAmountToPay  May be present. */    
    public $lowestAmountToPay;
    
    /** @var long $contractNumber   May be present. Svea contract number received when delivering a payment plan order.*/    
    public $contractNumber;
    
    public function __construct($response) {
              
        $this->accepted = $response->DeliverOrderEuResult->Accepted;
        $this->resultcode = $response->DeliverOrderEuResult->ResultCode;
        if (isset($response->DeliverOrderEuResult->ErrorMessage)) { $this->errormessage = $response->DeliverOrderEuResult->ErrorMessage; }

        if ($this->accepted == 1) {
            $this->amount = $response->DeliverOrderEuResult->DeliverOrderResult->Amount;
            $this->orderType = $response->DeliverOrderEuResult->DeliverOrderResult->OrderType;
            if (property_exists($response->DeliverOrderEuResult->DeliverOrderResult, "InvoiceResultDetails")) {
                $this->invoiceId = $response->DeliverOrderEuResult->DeliverOrderResult->InvoiceResultDetails->InvoiceId;
                $this->dueDate = $response->DeliverOrderEuResult->DeliverOrderResult->InvoiceResultDetails->DueDate;
                $this->invoiceDate = $response->DeliverOrderEuResult->DeliverOrderResult->InvoiceResultDetails->InvoiceDate;
                $this->invoiceDistributionType = $response->DeliverOrderEuResult->DeliverOrderResult->InvoiceResultDetails->InvoiceDistributionType;
                $this->ocr = $response->DeliverOrderEuResult->DeliverOrderResult->InvoiceResultDetails->Ocr;
                $this->lowestAmountToPay = $response->DeliverOrderEuResult->DeliverOrderResult->InvoiceResultDetails->LowestAmountToPay;
            } elseif (property_exists($response->DeliverOrderEuResult->DeliverOrderResult, "PaymentPlanResultDetails")) {
                $this->contractNumber = $response->DeliverOrderEuResult->DeliverOrderResult->PaymentPlanResultDetails->ContractNumber;
            }
        }
    }
}
