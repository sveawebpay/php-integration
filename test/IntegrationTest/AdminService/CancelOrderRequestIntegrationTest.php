<?php

namespace Svea\WebPay\Test\IntegrationTest\AdminService;

use \PHPUnit\Framework\TestCase;
use Svea\WebPay\AdminService\CancelOrderRequest;
use Svea\WebPay\BuildOrder\CancelOrderBuilder;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\Config\ConfigurationService;

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CancelOrderRequestIntegrationTest extends \PHPUnit\Framework\TestCase
{

    /**
     * 1. create an Invoice|PaymentPlan order
     * 2. note the client credentials, order number and type, and insert below
     * 3. run the test
     */
    public function test_manual_CancelOrderRequest()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for test_manual_CancelOrderRequest'
        );

        $countryCode = "SE";
        $sveaOrderIdToClose = 349698;
        $orderType = ConfigurationProvider::INVOICE_TYPE;

        $cancelOrderBuilder = new CancelOrderBuilder(ConfigurationService::getDefaultConfig());
        $cancelOrderBuilder->setCountryCode($countryCode);
        $cancelOrderBuilder->setOrderId($sveaOrderIdToClose);
        $cancelOrderBuilder->orderType = $orderType;

        $request = new CancelOrderRequest($cancelOrderBuilder);
        $response = $request->doRequest();

        ////print_r("cancelorderrequest: "); //print_r( $response );        
        $this->assertInstanceOf('Svea\WebPay\AdminService\AdminServiceResponse\CancelOrderResponse', $response);
        $this->assertEquals(1, $response->accepted);
        $this->assertEquals(0, $response->resultcode);

    }
}
