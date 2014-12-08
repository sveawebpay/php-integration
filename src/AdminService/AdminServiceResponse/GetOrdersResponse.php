<?php
namespace Svea\AdminService;

/**
 * Handles the Svea Admin Web Service GetOrders request response.
 *
 * @author Kristian Grossman-Madsen
 */
class GetOrdersResponse extends AdminServiceResponse {

    // phpdoc attributes below takes its info from admin service api Order structure
    /** @var string $changedDate -- Date when order status was changed, e.g when order was delivered. */
    public $changedDate;
    /** @var string $clientId -- Id that identifies a client in sveawebpay system */
    public $clientId;
    /** @var string $clientOrderId -- Order number from client's ordersystem */
    public $clientOrderId;
    /** @var string $createdDate -- Date when order was first created. */
    public $createdDate;

    /** @var boolean $creditReportStatusAccepted -- Tells if credit decision is accepted or not */
    public $creditReportStatusAccepted;
    /** @var string $creditReportStatusCreationDate -- Date of order credit decision. */
    public $creditReportStatusCreationDate;

    /** @var string $currency -- Country currency */
    public $currency;

    /** @var CompanyCustomer|IndividualCustomer $customer -- customer identity as associated with the order by Svea, also Shipping address. */
    public $customer;

    /** @var string $customerId -- Customer id that is created by SveaWebPay system. */
    public $customerId;
    /** @var string $customerReference -- Customer Reference. (Gets printed on the invoice.)*/
    public $customerReference;
    /** @var boolean $isPossibleToAdminister */
    public $isPossibleToAdminister;
    /** @var boolean $isPossibleToCancel -- Tells if order can be cancelled or not */
    public $isPossibleToCancel;
    /** @var string $notes -- Text on order created by client */
    public $notes;
    /** @var string $orderDeliveryStatus -- one of {Created,PartiallyDelivered,Delivered,Cancelled} */
    public $orderDeliveryStatus;

    /** @var Svea\OrderRow[] $numberedOrderRows  array of OrderRow objects, note that invoice and payment plan order rows name attribute will be null */
    public $numberedOrderRows;

    /** @var string $orderStatus -- one of {Created,Pending,Active,Denied,Error}*/
    public $orderStatus;
    /** @var string $orderType -- one of {Invoice,PaymentPlan} */
    public $orderType;

    /** @var string $paymentPlanDetailsContractLengthMonths */
    public $paymentPlanDetailsContractLengthMonths;
    /** @var string $paymentPlanDetailsContractContractNumber -- Contract number of a specific contract. */
    public $paymentPlanDetailsContractNumber;

    /** @var string $pendingReasonsPendingType -- one of {SMSOnHighAmount,UseOfDeliveryAddress} */
    public $pendingReasonsPendingType;
    /** @var string $pendingReasonsCreatedDate */
    public $pendingReasonsCreatedDate;

    /** @var string $orderId -- Unique Id for the created order. Used for any further order webservice requests. */
    public $orderId;
    /** @var boolean $sveaWillBuy -- Describes whether SveaWebPay will buy the order or just administrate it */
    public $sveaWillBuy;

    function __construct($message) {
        $this->formatObject($message);
    }

    protected function formatObject($message) {

        // was request accepted?
        parent::formatObject($message);

        // if successful, set deliverOrderResult, using the same attributes as for DeliverOrderEU?
        if ($this->accepted == 1) {

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
            if( $order->Customer->CustomerType === "Individual" ) {
                $this->customer = new \Svea\IndividualCustomer;

                $this->customer->setNationalIdNumber($order->Customer->NationalIdNumber);
                $this->customer->setInitials($order->Customer->IndividualIdentity->Initials);
                 if( isset($order->Customer->IndividualIdentity->BirthDate) ) { // setBirthDate is picky about the argument format
                    $this->customer->setBirthDate($order->Customer->IndividualIdentity->BirthDate);
                }
                $this->customer->setName($order->Customer->IndividualIdentity->FirstName, $order->Customer->IndividualIdentity->LastName);
                $this->customer->setEmail($order->Customer->Email);
                $this->customer->setPhoneNumber($order->Customer->PhoneNumber);
                $this->customer->setStreetAddress($order->Customer->Street, $order->Customer->HouseNumber);
                $this->customer->setCoAddress($order->Customer->CoAddress);
                $this->customer->setZipCode($order->Customer->ZipCode);
                $this->customer->setLocality($order->Customer->Locality);
            }

            if( $order->Customer->CustomerType === "Company" ) {
                $this->customer = new \Svea\CompanyCustomer;

                $this->customer->setNationalIdNumber($order->Customer->NationalIdNumber);
                $this->customer->setVatNumber($order->Customer->CompanyIdentity->CompanyVatNumber);
                $this->customer->setCompanyName( $order->Customer->FullName);
                $this->customer->setEmail($order->Customer->Email);
                $this->customer->setPhoneNumber($order->Customer->PhoneNumber);
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
            if( is_a($order->OrderRows->NumberedOrderRow, "stdClass") ) {
                $row = $order->OrderRows->NumberedOrderRow;
                $newrow = new \Svea\NumberedOrderRow(); // webpay orderrow
                $newrow
                    //->setName()
//                    ->setAmountExVat( $row->PricePerUnit )
                    ->setDescription( $row->Description)
                    ->setQuantity( $row->NumberOfUnits )
                    ->setArticleNumber( $row->ArticleNumber )
                    ->setUnit( $row->Unit )
                    ->setVatPercent( (int)$row->VatPercent )
                    ->setDiscountPercent( $row->DiscountPercent )
                ;
                if($row->PriceIncludingVat === 'true'){
                    $newrow->setAmountIncVat( $row->PricePerUnit );
                }  else {
                     $newrow->setAmountExVat( $row->PricePerUnit );
                }


                $newrow->creditInvoiceId = $row->CreditInvoiceId;
                $newrow->invoiceId = $row->InvoiceId;
                $newrow->rowNumber = $row->RowNumber;
                $newrow->status = $row->Status;

                $this->numberedOrderRows[] = $newrow;
            }

            // multiple order rows are returned as an array
            elseif( is_array($order->OrderRows->NumberedOrderRow ) ) {
                // for each numbered orderrow, add it to the numberedOrderRow array
                foreach( $order->OrderRows->NumberedOrderRow as $row ) {
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

                    $newrow = new \Svea\NumberedOrderRow(); // webpay orderrow
                    //WebPayItem OrderRow:
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
                        ->setDescription( $row->Description)
                        ->setQuantity( $row->NumberOfUnits )
                        ->setArticleNumber( $row->ArticleNumber )
                        ->setUnit( $row->Unit )
                        ->setVatPercent( (int)$row->VatPercent )
                        ->setDiscountPercent( $row->DiscountPercent )
                    ;
                    if($row->PriceIncludingVat === 'true'){
                          $newrow->setAmountIncVat( $row->PricePerUnit );
                    }  else {
                        $newrow->setAmountExVat( $row->PricePerUnit );
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

            if( is_a($order->PaymentPlanDetails, "stdClass") && property_exists($order->PaymentPlanDetails, "ContractLengthMonths") ) {
                $this->paymentPlanDetailsContractLengthMonths = $order->PaymentPlanDetails->ContractLengthMonths;
            }
            if( is_a($order->PaymentPlanDetails, "stdClass") && property_exists($order->PaymentPlanDetails, "ContractNumber") ) {
                $this->paymentPlanDetailsContractNumber = $order->PaymentPlanDetails->ContractNumber;
            }

            $this->pendingReasons = $order->PendingReasons;
            if( is_a($order->PendingReasons, "stdClass") && property_exists($order->PendingReasons, "PendingType") ) {
                $this->pendingReasonsPendingType = $order->PendingReasons->PendingType;
            }
            if( is_a($order->PendingReasons, "stdClass") && property_exists($order->PendingReasons, "CreatedDate") ) {
                $this->PendingReasonsCreatedDate = $order->PendingReasons->CreatedDate;
            }

            $this->orderId = $order->SveaOrderId;
            $this->sveaWillBuy = ($order->SveaWillBuy === 'true') ? true : false;

        }
    }
}
