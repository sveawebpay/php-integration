<?php

namespace Svea\WebPay\WebService\Payment;

use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\WebService\Helper\WebServiceRowFormatter;
use Svea\WebPay\WebService\SveaSoap\SveaCreateOrderInformation;

/**
 * Extends WebServicePayment. Creates Invoice order.
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class InvoicePayment extends WebServicePayment
{
    public $orderType;

    public function __construct($order)
    {
        $this->orderType = ConfigurationProvider::INVOICE_TYPE;
        parent::__construct($order);
    }

    public function setOrderType($orderInformation)
    {
        $orderInformation->AddressSelector = isset($this->order->customerIdentity->addressSelector) ? $this->order->customerIdentity->addressSelector : "";
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
