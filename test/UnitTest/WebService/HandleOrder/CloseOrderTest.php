<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../TestUtil.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CloseOrderTest extends PHPUnit_Framework_TestCase {

    public function testCloseInvoiceOrder() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderBuilder = WebPay::closeOrder($config);
        $request = $orderBuilder
                ->setOrderId("id")
                ->setCountryCode("SE")
                ->closeInvoiceOrder()
                ->prepareRequest();

        $this->assertEquals("id", $request->request->CloseOrderInformation->SveaOrderId);
    }

    public function testClosePaymentPlanOrder() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderBuilder = WebPay::closeOrder($config);
        $request = $orderBuilder
                ->setCountryCode("SE")
                ->setOrderId("id")
                ->closePaymentPlanOrder()
                ->prepareRequest();

        $this->assertEquals("id", $request->request->CloseOrderInformation->SveaOrderId);
    }
}
