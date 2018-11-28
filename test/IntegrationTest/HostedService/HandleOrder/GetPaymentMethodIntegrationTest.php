<?php
// Integration tests should not need to use the namespace

namespace Svea\WebPay\Test\IntegrationTest\HostedService\HandleOrder;

use Svea\WebPay\WebPay;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\PaymentMethod;
use Svea\WebPay\Constant\SystemPaymentMethod;


/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class GetPaymentMethodIntegrationTest extends \PHPUnit\Framework\TestCase
{

    function testGetAllPaymentMethods()
    {
        $this->markTestSkipped("deprecated function.");

        $config = ConfigurationService::getDefaultConfig();
        $response = WebPay::getPaymentMethods($config)
            ->setContryCode("SE")
            ->doRequest();

        //print_r( "testGetAllPaymentMethods: "); //print_r( $response );        
        $this->assertEquals(PaymentMethod::BANKAXESS, $response[0]);
        $this->assertEquals(PaymentMethod::NORDEA_SE, $response[1]);
        $this->assertEquals(PaymentMethod::SEB_SE, $response[2]);
        $this->assertEquals(PaymentMethod::KORTCERT, $response[3]);
        $this->assertEquals(SystemPaymentMethod::INVOICE_SE, $response[4]);
        $this->assertEquals(SystemPaymentMethod::PAYMENTPLAN_SE, $response[5]);
        $this->assertEquals(PaymentMethod::INVOICE, $response[6]);
        $this->assertEquals(PaymentMethod::PAYMENTPLAN, $response[7]);
    }
}

?>
