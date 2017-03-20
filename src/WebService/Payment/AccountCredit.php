<?php


namespace Svea\WebPay\WebService\Payment;


use Svea\WebPay\WebService\Helper\WebServiceRowFormatter;
use Svea\WebPay\WebService\SveaSoap\SveaCreateAccountCreditOrderInformation;

class AccountCredit extends WebServicePayment
{
    public $orderType = 'AccountCredit';

    public function __construct($order)
    {
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
        $orderInformation = new SveaCreateAccountCreditOrderInformation(
            (isset($this->order->campaignCode) ? $this->order->campaignCode : "")
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