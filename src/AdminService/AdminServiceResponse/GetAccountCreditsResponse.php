<?php

namespace Svea\WebPay\AdminService\AdminServiceResponse;

use Svea\WebPay\BuildOrder\RowBuilders\OrderRow;
use Svea\WebPay\BuildOrder\RowBuilders\CompanyCustomer;
use Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow;
use Svea\WebPay\BuildOrder\RowBuilders\IndividualCustomer;

/**
 * Handles the Svea Admin Web Service GetOrders request response.
 *
 * @todo this is for searching AccountCredit orders and for formatting response, it will be implemented in the future
 */
class GetAccountCreditsResponse
{
    /**
     * @var int $accepted true iff request was accepted by the service
     */
    public $accepted;

    /**
     * @var int $resultcode response specific result code
     */
    public $resultcode;

    /**
     * @var string errormessage  may be set iff accepted above is false
     */
    public $errormessage;

    /**
     * @var array
     */
    public $accountCredits;

    /**
     * GetOrdersResponse constructor.
     * @param $message
     */
    function __construct($message)
    {
        $this->formatObject($message);
    }

    protected function formatObject($message)
    {
        $this->errormessage = isset($message->ErrorMessage) ? $message->ErrorMessage : "";
        $this->resultcode = $message->ResultCode;

        // if successful, set deliverOrderResult, using the same attributes as for DeliverOrderEU?
        if (property_exists($message, "AccountCredits")) {

            // @todo  -this is for searching AccountCredit orders and for formatting response, it will be implemented in the future
            foreach($message->AccountCredits as $accountCredit)
            {
                // @todo - create AccountCredits class

                    // @todo - create AccountCreditItem class

                        // @todo - new class AccountCreditRows
                            // @todo - new property NumberedOrderRow that contain list of OrderRows
            }

            // populate GetOrdersResponse select attributes from the raw GetOrders response
            $order = $message->Orders->Order;

            $this->changedDate = $order->ChangedDate;
            $this->clientId = $order->ClientId;
            $this->clientOrderId = $order->ClientOrderId;
            $this->createdDate = $order->CreatedDate;

            $this->creditReportStatusAccepted = ($order->CreditReportStatus->Accepted === "true") ? true : false;
            $this->creditReportStatusCreationDate = $order->CreditReportStatus->CreationDate;

            $this->currency = $order->Currency;

            //individual customer?
            if ($order->Customer->CustomerType === "Individual") {

                $this->customer = new IndividualCustomer;

                $this->customer->setNationalIdNumber($order->Customer->NationalIdNumber);
                $this->customer->setInitials($order->Customer->IndividualIdentity->Initials);
                if (isset($order->Customer->IndividualIdentity->BirthDate)) { // setBirthDate is picky about the argument format
                    $this->customer->setBirthDate($order->Customer->IndividualIdentity->BirthDate);
                }
                $this->customer->setName($order->Customer->IndividualIdentity->FirstName, $order->Customer->IndividualIdentity->LastName); // sets firstName, lastName if present
                $this->customer->setName($order->Customer->FullName); // sets compounded fullName if present
                $this->customer->setEmail($order->Customer->Email);
                $this->customer->setPhoneNumber($order->Customer->PhoneNumber);
                $this->customer->setStreetAddress($order->Customer->Street); // sets compounded streetAddress if present, as well as street
                $this->customer->setStreetAddress($order->Customer->Street, $order->Customer->HouseNumber); // sets Individual street, houseNumber if present
                $this->customer->setCoAddress($order->Customer->CoAddress);
                $this->customer->setZipCode($order->Customer->ZipCode);
                $this->customer->setLocality($order->Customer->Locality);
            }

            if ($order->Customer->CustomerType === "Company") {

                $this->customer = new CompanyCustomer;

                $this->customer->setNationalIdNumber($order->Customer->NationalIdNumber);
                $this->customer->setVatNumber($order->Customer->CompanyIdentity->CompanyVatNumber);
                $this->customer->setCompanyName($order->Customer->FullName);
                $this->customer->setEmail($order->Customer->Email);
                $this->customer->setPhoneNumber($order->Customer->PhoneNumber);
                $this->customer->setStreetAddress($order->Customer->Street); // sets compounded streetAddress if present, as well as street
                $this->customer->setStreetAddress($order->Customer->Street, $order->Customer->HouseNumber);
                $this->customer->setCoAddress($order->Customer->CoAddress);
                $this->customer->setZipCode($order->Customer->ZipCode);
                $this->customer->setLocality($order->Customer->Locality);
            }

            $this->customerId = $order->CustomerId;
            $this->customerReference = $order->CustomerReference;
            //$this->deliveryAddress = $order->DeliveryAddress; // not supported
            $this->isPossibleToAdminister = ($order->IsPossibleToAdminister === "true") ? true : false;
            $this->isPossibleToCancel = ($order->IsPossibleToCancel === 'true') ? true : false;
            $this->notes = $order->Notes;
            $this->orderDeliveryStatus = $order->OrderDeliveryStatus;

            // a single order row is returned as type stdClass
            if (is_a($order->OrderRows->NumberedOrderRow, "stdClass")) {
                $row = $order->OrderRows->NumberedOrderRow;
                $newrow = new NumberedOrderRow(); // webpay orderrow
                $newrow
                    //->setName()
//                    ->setAmountExVat( $row->PricePerUnit )
                    ->setDescription($row->Description)
                    ->setQuantity($row->NumberOfUnits)
                    ->setArticleNumber($row->ArticleNumber)
                    ->setUnit($row->Unit)
                    ->setVatPercent((int)$row->VatPercent)
                    ->setDiscountPercent($row->DiscountPercent);
                if ($row->PriceIncludingVat === 'true') {
                    $newrow->setAmountIncVat($row->PricePerUnit);
                } else {
                    $newrow->setAmountExVat($row->PricePerUnit);
                }


                $newrow->creditInvoiceId = $row->CreditInvoiceId;
                $newrow->invoiceId = $row->InvoiceId;
                $newrow->rowNumber = $row->RowNumber;
                $newrow->status = $row->Status;

                $this->numberedOrderRows[] = $newrow;
            } // multiple order rows are returned as an array
            elseif (is_array($order->OrderRows->NumberedOrderRow)) {
                // for each numbered orderrow, add it to the numberedOrderRow array
                foreach ($order->OrderRows->NumberedOrderRow as $row) {
                    //GetOrders NumberedOrderRow:
                    // [ArticleNumber]
                    // [Description]
                    // [DiscountPercent]
                    // [NumberOfUnits]
                    // [PricePerUnit]
                    // [Unit]
                    // [VatPercent]
                    // [CreditInvoiceId]
                    // [InvoiceId]
                    // [RowNumber]
                    // [Status]

                    $newrow = new NumberedOrderRow(); // webpay orderrow
                    //Svea\WebPay\WebPayItem OrderRow:
                    // $articleNumber   *
                    // $quantity        *
                    // $unit            *
                    // $amountExVat     * depends on bool priceincludingvat
                    // $amountIncVat    * depends on bool priceincludingvat
                    // $vatPercent      *
                    // $name            not used
                    // $description     *
                    // $discountPercent *
                    // $vatDiscount     not used

                    $newrow
                        //->setName()
//                        ->setAmountExVat( $row->PricePerUnit )
                        ->setDescription($row->Description)
                        ->setQuantity($row->NumberOfUnits)
                        ->setArticleNumber($row->ArticleNumber)
                        ->setUnit($row->Unit)
                        ->setVatPercent((int)$row->VatPercent)
                        ->setDiscountPercent($row->DiscountPercent);
                    if ($row->PriceIncludingVat === 'true') {
                        $newrow->setAmountIncVat($row->PricePerUnit);
                    } else {
                        $newrow->setAmountExVat($row->PricePerUnit);
                    }

                    $newrow->creditInvoiceId = $row->CreditInvoiceId;
                    $newrow->invoiceId = $row->InvoiceId;
                    $newrow->rowNumber = $row->RowNumber;
                    $newrow->status = $row->Status;

                    $this->numberedOrderRows[] = $newrow;
                }
            }

            $this->orderStatus = $order->OrderStatus;
            $this->orderType = $order->OrderType;

            if (is_a($order->PaymentPlanDetails, "stdClass") && property_exists($order->PaymentPlanDetails, "ContractLengthMonths")) {
                $this->paymentPlanDetailsContractLengthMonths = $order->PaymentPlanDetails->ContractLengthMonths;
            }
            if (is_a($order->PaymentPlanDetails, "stdClass") && property_exists($order->PaymentPlanDetails, "ContractNumber")) {
                $this->paymentPlanDetailsContractNumber = $order->PaymentPlanDetails->ContractNumber;
            }

            $this->pendingReasons = $order->PendingReasons;
            if (is_a($order->PendingReasons, "stdClass") && property_exists($order->PendingReasons, "PendingType")) {
                $this->pendingReasonsPendingType = $order->PendingReasons->PendingType;
            }
            if (is_a($order->PendingReasons, "stdClass") && property_exists($order->PendingReasons, "CreatedDate")) {
                $this->PendingReasonsCreatedDate = $order->PendingReasons->CreatedDate;
            }

            $this->orderId = $order->SveaOrderId;
            $this->sveaWillBuy = ($order->SveaWillBuy === 'true') ? true : false;

        }
    }
}
