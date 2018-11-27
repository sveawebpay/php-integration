<?php

namespace Svea\WebPay\Test\UnitTest\WebService\HandleOrder;

use Svea\WebPay\WebPay;
use \PHPUnit\Framework\TestCase;
use Svea\WebPay\Config\ConfigurationService;


/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CloseOrderTest extends \PHPUnit\Framework\TestCase
{

    public function testCloseInvoiceOrder()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderBuilder = WebPay::closeOrder($config);
        $request = $orderBuilder
            ->setOrderId("id")
            ->setCountryCode("SE")
            ->closeInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals("id", $request->request->CloseOrderInformation->SveaOrderId);
    }

    public function testClosePaymentPlanOrder()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderBuilder = WebPay::closeOrder($config);
        $request = $orderBuilder
            ->setCountryCode("SE")
            ->setOrderId("id")
            ->closePaymentPlanOrder()
            ->prepareRequest();

        $this->assertEquals("id", $request->request->CloseOrderInformation->SveaOrderId);
    }
}
