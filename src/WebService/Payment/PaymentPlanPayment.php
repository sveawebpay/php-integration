<?php

namespace Svea\WebPay\WebService\Payment;
use Svea\WebPay\WebService\Helper\WebServiceRowFormatter;
use Svea\WebPay\WebService\SveaSoap\SveaCreateOrderInformation;

/**
 * Creates Payment Plan Order. Extends WebServicePayment
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class PaymentPlanPayment extends WebServicePayment
{
    public $orderType = 'PaymentPlan';

    public function __construct($order)
    {
        parent::__construct($order);
    }

    protected function setOrderType($orderInformation)
    {
        $orderInformation->AddressSelector = "";
        $orderInformation->OrderType = $this->orderType;

        return $orderInformation;
    }

    /**
     * Format Order row with svea_soap package and calculate vat
     * @param type $rows
     * @return \SveaCreateOrderInformation
     */
    protected function formatOrderInformationWithOrderRows($rows)
    {
        $orderInformation = new SveaCreateOrderInformation(
            (isset($this->order->campaignCode) ? $this->order->campaignCode : ""),
            (isset($this->order->sendAutomaticGiroPaymentForm) ? $this->order->sendAutomaticGiroPaymentForm : 0)
        );

        // rewrite order rows to soap_class order rows
        $formatter = new WebServiceRowFormatter($this->order);
        $formattedOrderRows = $formatter->formatRows();

        foreach ($formattedOrderRows as $formattedOrderRow) {
            $orderInformation->addOrderRow($formattedOrderRow);
        }

        return $orderInformation;
    }
}
