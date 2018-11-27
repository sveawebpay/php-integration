<?php
// Integration tests should not need to use the namespace

namespace Svea\WebPay\Test\IntegrationTest\HostedService\HandleOrder;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\PaymentMethod;
use Svea\WebPay\Constant\SystemPaymentMethod;
use Svea\WebPay\HostedService\HostedAdminRequest\ListPaymentMethods;


/**
 * Svea\WebPay\Test\IntegrationTest\HostedService\HandleOrder\ListPaymentMethodsIntegrationTest
 *
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class ListPaymentMethodsIntegrationTest extends \PHPUnit\Framework\TestCase
{

    function test_listPaymentMethods_request_success()
    {
        $this->markTestSkipped('deprecated');

        $request = new ListPaymentMethods(ConfigurationService::getDefaultConfig());
        $request->countryCode = "SE";
        $response = $request->doRequest();

        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\ListPaymentMethodsResponse", $response);

        //print_r( "test_listPaymentMethods_request_success: "); //print_r( $response );
        $this->assertEquals(1, $response->accepted);
        $this->assertInternalType("array", $response->paymentmethods);

        // from getpaymentmethods call, tied to merchantid
        $this->assertEquals(PaymentMethod::BANKAXESS, $response->paymentmethods[0]);
        $this->assertEquals(PaymentMethod::NORDEA_SE, $response->paymentmethods[1]);
        $this->assertEquals(PaymentMethod::SEB_SE, $response->paymentmethods[2]);
        $this->assertEquals(PaymentMethod::KORTCERT, $response->paymentmethods[3]);
        $this->assertEquals(SystemPaymentMethod::INVOICE_SE, $response->paymentmethods[4]);
        $this->assertEquals(SystemPaymentMethod::PAYMENTPLAN_SE, $response->paymentmethods[5]);

        // from ListPaymentMethods implementation, tied to clientid
        $this->assertEquals(PaymentMethod::INVOICE, $response->paymentmethods[6]);
        $this->assertEquals(PaymentMethod::PAYMENTPLAN, $response->paymentmethods[7]);
    }
}

?>
