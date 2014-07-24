<?php
namespace Svea\WebService;

require_once 'WebServiceResponse.php';

/**
 * @author anne-hal
 */
class DeliverOrderResult extends WebServiceResponse{

    public $amount;
    public $orderType;

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

            $this->orderType = $response->DeliverOrderEuResult->DeliverOrderResult->OrderType;
        }
    }
}
