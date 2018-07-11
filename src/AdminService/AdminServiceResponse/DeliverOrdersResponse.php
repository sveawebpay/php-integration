<?php

namespace Svea\WebPay\AdminService\AdminServiceResponse;

use Svea\WebPay\Config\ConfigurationProvider;

/**
 * Handles the Svea Admin Web Service DeliverOrder request response.
 *
 * @author Kristian Grossman-Madsen
 */
class DeliverOrdersResponse extends AdminServiceResponse
{
    /**
     * @var string $clientId
     */
    public $clientId;

    /**
     * @var float $amount (set iff accepted) the amount delivered with this request
     */
    public $amount;

    /**
     * @var string $invoiceId (set iff accepted, orderType Invoice)  the invoice id for the delivered order
     */
    public $invoiceId;

    /**
     * @var string $contractNumber (set iff accepted, orderType PaymentPlan)  the contract number for the delivered order
     */
    public $contractNumber;

    /**
     * @var string $orderType
     */
    public $orderType;

    /**
     * @var string $orderId
     */
    public $orderId;

    /**
     * @var int $deliveryReferenceNumber - this is accountCredit specific, and its returned on order delivery
     */
    public $deliveryReferenceNumber;

    /**
     * DeliverOrdersResponse constructor.
     * @param $message
     * @param $logs
     */
    function __construct($message, $logs)
    {
        $this->formatObject($message, $logs);
    }

    protected function formatObject($message, $logs)
    {
        parent::formatObject($message, $logs);

        if ($this->accepted == 1) {

            $this->clientId = $message->OrdersDelivered->DeliverOrderResult->ClientId;
            $this->amount = $message->OrdersDelivered->DeliverOrderResult->DeliveredAmount;

            if ($message->OrdersDelivered->DeliverOrderResult->OrderType == ConfigurationProvider::INVOICE_TYPE) {
                $this->invoiceId = $message->OrdersDelivered->DeliverOrderResult->DeliveryReferenceNumber;
            }

            if ($message->OrdersDelivered->DeliverOrderResult->OrderType == ConfigurationProvider::PAYMENTPLAN_TYPE) {
                $this->contractNumber = $message->OrdersDelivered->DeliverOrderResult->DeliveryReferenceNumber;
            }

            // - specific for accountCredit
            if(property_exists($message->OrdersDelivered->DeliverOrderResult, "DeliveryReferenceNumber"))
            {
                $this->deliveryReferenceNumber = $message->OrdersDelivered->DeliverOrderResult->DeliveryReferenceNumber;
            }

            $this->orderType = $message->OrdersDelivered->DeliverOrderResult->OrderType;
            $this->orderId = $message->OrdersDelivered->DeliverOrderResult->SveaOrderId;
        }
    }
}
