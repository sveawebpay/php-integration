<?php

namespace Svea\WebPay\Test\IntegrationTest\BuildOrder;

use \PHPUnit\Framework\TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;


/**
 * Svea\WebPay\Test\IntegrationTest\BuildOrder\CreateOrderBuilderIntegrationTest holds all tests for how to build orders for diverse
 * payment methods.
 *
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class CreateOrderBuilderIntegrationTest extends \PHPUnit\Framework\TestCase
{

    public function test_createOrder_Invoice_SE_Accepted()
    {
        $order = TestUtil::createOrder();
        $response = $order->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $response->accepted);
    }

    public function test_createOrder_Paymentplan_SE_Accepted()
    {

        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity(1)
                ->setAmountExVat(1000.00)
                ->setVatPercent(25)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setOrderDate(date('c'));
        $response = $order->usePaymentPlanPayment(TestUtil::getGetPaymentPlanParamsForTesting())->doRequest();

        $this->assertEquals(1, $response->accepted);
    }

    public function test_createCheckoutOrder_ValidationCallbackUri_Accepted()
    {
        $validationCallbackUri = 'http://localhost:51898/validation-callback';
        $myConfig = ConfigurationService::getTestConfig();
        $locale = 'sv-Se';
        $orderBuilder = WebPay::checkout($myConfig);
        $orderBuilder->setCountryCode('SE')// customer country, we recommend basing this on the customer billing address
        ->setCurrency('SEK')
            ->setClientOrderNumber(rand(270000, 670000))
            ->setCheckoutUri('http://localhost:51925/')
            ->setConfirmationUri('http://localhost:51925/checkout/confirm')
            ->setPushUri('https://svea.com/push.aspx?sid=123&svea_order=123')
            ->setTermsUri('http://localhost:51898/terms')
            ->setValidationCallbackUri($validationCallbackUri)
            ->setLocale($locale);
        $firstBoughtItem = WebPayItem::orderRow()
            ->setAmountIncVat(100.00)
            ->setVatPercent(25)
            ->setQuantity(1)
            ->setArticleNumber('123')
            ->setTemporaryReference('230')
            ->setName('Fork');
        $orderBuilder->addOrderRow($firstBoughtItem);
        $response = $orderBuilder->createOrder();
        $this->assertEquals($validationCallbackUri, $response['MerchantSettings']['CheckoutValidationCallBackUri']);
    }

    // CreateOrderBuilder card payment method
    // see Svea\WebPay\Test\IntegrationTest\HostedService\Payment\CardPaymentURLIntegrationTest->test_manual_CardPayment_getPaymentUrl()

    // CreateOrderBuilder direct bank payment method   //TODO    


}


?>