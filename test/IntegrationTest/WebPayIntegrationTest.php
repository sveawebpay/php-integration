<?php
// Integration tests should not need to use the namespace

namespace Svea\WebPay\Test\IntegrationTest;

use \PHPUnit\Framework\TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\DistributionType;
use Svea\WebPay\Constant\PaymentMethod;
use Svea\WebPay\Helper\Helper;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;

/**
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class WebPayIntegrationTest extends \PHPUnit\Framework\TestCase
{

    /// Svea\WebPay\WebPay::createOrder() --------------------------------------------------
    //useInvoicePayment
    public function test_createOrder_useInvoicePayment()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();
        $this->assertEquals(1, $request->accepted);
    }

    //usePaymentPlanPayment
    public function test_createOrder_usePaymentPlanPayment()
    {
        $config = ConfigurationService::getDefaultConfig();
        $campaigncode = TestUtil::getGetPaymentPlanParamsForTesting();
        $request = WebPay::createOrder($config)
            ->addOrderRow(WebPayItem::orderRow()
                ->setArticleNumber("1")
                ->setQuantity(2)
                ->setAmountExVat(1000.00)
                ->setDescription("Specification")
                ->setName('Prod')
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
            )
            ->addCustomerDetails(WebPayItem::individualCustomer()
                ->setNationalIdNumber(194605092222)
                ->setInitials("SB")
                ->setBirthDate(1923, 12, 12)
                ->setName("Tess", "Testson")
                ->setEmail("test@svea.com")
                ->setPhoneNumber(999999)
                ->setIpAddress("123.123.123")
                ->setStreetAddress("Gatan", 23)
                ->setCoAddress("c/o Eriksson")
                ->setZipCode(9999)
                ->setLocality("Stan")
            )
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setClientOrderNumber("nr26")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->usePaymentPlanPayment($campaigncode)
            ->doRequest();
        $this->assertEquals(1, $request->accepted);
    }

    // card
    public function test_createOrder_usePaymentMethod_KORTCERT_redirects_to_certitrade()
    {
        $this->markTestIncomplete(
            'CertiTrade no longer used'
        );
        $config = ConfigurationService::getDefaultConfig();
        $rowFactory = new TestUtil();
        $form = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->run($rowFactory->buildShippingFee())
            ->addDiscount(WebPayItem::relativeDiscount()
                ->setDiscountId("1")
                ->setDiscountPercent(50)
                ->setUnit("st")
                ->setName('Relative')
                ->setDescription("RelativeDiscount")
            )
            ->setCountryCode("SE")
            ->setClientOrderNumber("foobar" . date('c'))
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setReturnUrl("http://myurl.se")
            ->getPaymentForm();
        $url = "https://webpaypaymentgatewaystage.svea.com/webpay/payment";

        /** CURL  **/
        $fields = array('merchantid' => urlencode($form->merchantid), 'message' => urlencode($form->xmlMessageBase64), 'mac' => urlencode($form->mac));
        $fieldsString = "";
        foreach ($fields as $key => $value) {
            $fieldsString .= $key . '=' . $value . '&';
        }
        rtrim($fieldsString, '&');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);     // follow redirects
        curl_setopt($ch, CURLOPT_HEADER, true);             // include headers in transfer history
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        // return transfer history
        $cr = curl_exec($ch);

        $info = curl_getinfo($ch);

        curl_close($ch);

        $this->assertEquals(200, $info['http_code']);
        $this->assertEquals(1, $info['redirect_count']);
        $this->assertEquals("https://etest.certitrade.net/card/paywin/index", substr($info['url'], 0, 46));
    }

    // TODO Move below to unit tests?
    //
    /// Svea\WebPay\WebPay::createOrder()
    // web service eu: invoice
    public function test_createOrder_useInvoicePayment_returns_InvoicePayment()
    {
        $createOrder = WebPay::createOrder(ConfigurationService::getDefaultConfig());
        // we should set attributes here if real request
        $request = $createOrder->useInvoicePayment();
        $this->assertInstanceOf("Svea\WebPay\WebService\Payment\InvoicePayment", $request);
    }

    // web service eu: paymentplan
    public function test_createOrder_usePaymentPlanPayment_returns_PaymentPlanPayment()
    {
        $createOrder = WebPay::createOrder(ConfigurationService::getDefaultConfig());
        $request = $createOrder->usePaymentPlanPayment(TestUtil::getGetPaymentPlanParamsForTesting());
        $this->assertInstanceOf("Svea\WebPay\WebService\Payment\PaymentPlanPayment", $request);
    }

    // paypage: cardonly
    public function test_createOrder_usePayPageCardOnly_returns_CardPayment()
    {
        $createOrder = WebPay::createOrder(ConfigurationService::getDefaultConfig());
        $request = $createOrder->usePayPageCardOnly();
        $this->assertInstanceOf("Svea\WebPay\HostedService\Payment\CardPayment", $request);
    }

    // paypage: directbankonly
    public function test_createOrder_usePayPageDirectBankOnly_returns_DirectPayment()
    {
        $createOrder = WebPay::createOrder(ConfigurationService::getDefaultConfig());
        $request = $createOrder->usePayPageDirectBankOnly();
        $this->assertInstanceOf("Svea\WebPay\HostedService\Payment\DirectPayment", $request);
    }

    // bypass paypage: usepaymentmethod
    public function test_createOrder_usePaymentMethod_returns_PaymentMethodPayment()
    {
        $createOrder = WebPay::createOrder(ConfigurationService::getDefaultConfig());
        $request = $createOrder->usePaymentMethod("mocked_paymentMethod");
        $this->assertInstanceOf("Svea\WebPay\HostedService\Payment\PaymentMethodPayment", $request);
    }

    // usepaymentmethod KORTCERT with recurring payment
    // TODO add recur example when implementing webdriver integration tests

    // paypage
    public function test_createOrder_usePayPage_returns_PayPagePayment()
    {
        $createOrder = WebPay::createOrder(ConfigurationService::getDefaultConfig());
        $request = $createOrder->usePayPage();
        $this->assertInstanceOf("Svea\WebPay\HostedService\Payment\PayPagePayment", $request);
    }

    /// Svea\WebPay\WebPay::deliverOrder()
    // invoice
    // TODO actual integration test

    // DeliverOrderEU - deliver order and credit order
    public function test_deliverOrder_deliverInvoiceOrder_with_order_rows_first_deliver_then_credit_order()
    {
        // create order using order row specified with ->setName() and ->setDescription
        $specifiedOrderRow = WebPayItem::orderRow()
            ->setAmountExVat(100.00)// recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)// recommended to specify price using AmountExVat & VatPercent
            ->setQuantity(1)                        // required
        ;

        $order = TestUtil::createOrderWithoutOrderRows()
            ->addOrderRow($specifiedOrderRow);

        $createOrderResponse = $order->useInvoicePayment()->doRequest();

        //print_r( $createOrderResponse );
        $this->assertInstanceOf("Svea\WebPay\WebService\WebServiceResponse\CreateOrderResponse", $createOrderResponse);
        $this->assertTrue($createOrderResponse->accepted);

        $createdOrderId = $createOrderResponse->sveaOrderId;

        // deliver order
        $deliverOrderBuilder = WebPay::deliverOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($createdOrderId)
            ->setCountryCode("SE")
            ->setInvoiceDistributionType(DistributionType::POST)
            ->addOrderRow($specifiedOrderRow);
        $deliverOrderResponse = $deliverOrderBuilder->deliverInvoiceOrder()->doRequest();

        //print_r( $deliverOrderResponse );
        $this->assertInstanceOf("Svea\WebPay\WebService\WebServiceResponse\DeliverOrderResult", $deliverOrderResponse);
        $this->assertTrue($createOrderResponse->accepted);

        $deliveredInvoiceId = $deliverOrderResponse->invoiceId;

        // credit order
        $creditOrderBuilder = WebPay::deliverOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($createdOrderId)
            ->setCountryCode("SE")
            ->setInvoiceDistributionType(DistributionType::POST)
            ->addOrderRow($specifiedOrderRow)
            ->setCreditInvoice($deliveredInvoiceId);
        $creditOrderResponse = $creditOrderBuilder->deliverInvoiceOrder()->doRequest();

        //print_r( $creditOrderResponse );
        $this->assertInstanceOf("Svea\WebPay\WebService\WebServiceResponse\DeliverOrderResult", $deliverOrderResponse);
        $this->assertTrue($creditOrderResponse->accepted);
    }

    // paymentplan
    public function test_deliverOrder_deliverPaymentPlanOrder_without_orderrows_delivers_order_in_full()
    {
        // create order
        $config = ConfigurationService::getDefaultConfig();
        $campaigncode = TestUtil::getGetPaymentPlanParamsForTesting();
        $order = WebPay::createOrder($config)
            ->addOrderRow(WebPayItem::orderRow()
                ->setArticleNumber("1")
                ->setQuantity(2)
                ->setAmountExVat(1000.00)
                ->setDescription("Specification")
                ->setName('Prod')
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
            )
            ->addCustomerDetails(WebPayItem::individualCustomer()
                ->setNationalIdNumber(194605092222)
                ->setInitials("SB")
                ->setBirthDate(1923, 12, 12)
                ->setName("Tess", "Testson")
                ->setEmail("test@svea.com")
                ->setPhoneNumber(999999)
                ->setIpAddress("123.123.123")
                ->setStreetAddress("Gatan", 23)
                ->setCoAddress("c/o Eriksson")
                ->setZipCode(9999)
                ->setLocality("Stan")
            )
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setClientOrderNumber("nr26")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->usePaymentPlanPayment($campaigncode)// returnerar InvoiceOrder object
            ->doRequest();

        //print_r($order);
        $this->assertEquals(1, $order->accepted);

        // deliver order
        $orderId = $order->sveaOrderId;
        $orderBuilder = WebPay::deliverOrder($config);
        $deliverResponse = $orderBuilder
            //->addOrderRow(Svea\WebPay\WebPayItem::orderRow()
            //        ->setArticleNumber("1")
            //        ->setQuantity(2)
            //        ->setAmountExVat(1000.00)
            //        ->setDescription("Specification")
            //        ->setName('Prod')
            //        ->setUnit("st")
            //        ->setVatPercent(25)
            //        ->setDiscountPercent(0)
            //)
            ->setOrderId($orderId)
            ->setCountryCode("SE")
            ->deliverPaymentPlanOrder()
            ->doRequest();

        //print_r($deliverResponse);
        $this->assertEquals(1, $deliverResponse->accepted);
        $this->assertEquals(0, $deliverResponse->resultcode);
        $this->assertEquals(2500, $deliverResponse->amount);
        $this->assertEquals('PaymentPlan', $deliverResponse->orderType);
    }

    public function test_deliverOrder_deliverPaymentPlanOrder_with_orderrows_misleadingly_delivers_order_in_full()
    {
        // create order
        $config = ConfigurationService::getDefaultConfig();
        $campaigncode = TestUtil::getGetPaymentPlanParamsForTesting();
        $order = WebPay::createOrder($config)
            ->addOrderRow(WebPayItem::orderRow()
                ->setArticleNumber("1")
                ->setQuantity(2)
                ->setAmountExVat(1000.00)
                ->setDescription("Specification")
                ->setName('Prod')
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setArticleNumber("2")
                ->setQuantity(2)
                ->setAmountExVat(1000.00)
                ->setDescription("Specification")
                ->setName('Prod')
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
            )
            ->addCustomerDetails(WebPayItem::individualCustomer()
                ->setNationalIdNumber(194605092222)
                ->setInitials("SB")
                ->setBirthDate(1923, 12, 12)
                ->setName("Tess", "Testson")
                ->setEmail("test@svea.com")
                ->setPhoneNumber(999999)
                ->setIpAddress("123.123.123")
                ->setStreetAddress("Gatan", 23)
                ->setCoAddress("c/o Eriksson")
                ->setZipCode(9999)
                ->setLocality("Stan")
            )
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setClientOrderNumber("nr26")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->usePaymentPlanPayment($campaigncode)// returnerar InvoiceOrder object
            ->doRequest();

        //print_r($order);
        $this->assertEquals(1, $order->accepted);
        $this->assertEquals(5000, $order->amount);


        // deliver order
        $orderId = $order->sveaOrderId;
        $orderBuilder = WebPay::deliverOrder($config);
        $deliverResponse = $orderBuilder
            ->addOrderRow(WebPayItem::orderRow()// TODO should raise validation exception
            ->setArticleNumber("1")
                ->setQuantity(2)
                ->setAmountExVat(1000.00)
                ->setDescription("Specification")
                ->setName('Prod')
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
            )
            ->setOrderId($orderId)
            ->setInvoiceDistributionType("Post")// TODO should raise validation exception
            ->setCountryCode("SE")
            ->deliverPaymentPlanOrder()
            ->doRequest();

        //print_r($deliverResponse);
        $this->assertEquals(1, $deliverResponse->accepted);
        $this->assertEquals(5000, $deliverResponse->amount);
    }

    // card
    // TODO actual integration test

    /// Svea\WebPay\WebPay::getAddresses()
    // TODO

    /// Svea\WebPay\WebPay::getPaymentPlanParams()
    // TODO

    /// Svea\WebPay\WebPay::listPaymentMethods()
    public function test_listPaymentMethods_returns_ListPaymentMethods()
    {
        $response = WebPay::listPaymentMethods(ConfigurationService::getDefaultConfig())
            ->setCountryCode("SE")
            ->doRequest();
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\ListPaymentMethodsResponse", $response);
        $this->assertEquals(true, $response->accepted);
    }

    /// Svea\WebPay\WebPay::paymentPlanPricePerMonth()
    public function test_paymentPlanPricePerMonth_returns_PaymentPlanPricePerMonth()
    {
        $campaigns =
            WebPay::getPaymentPlanParams(ConfigurationService::getDefaultConfig())
                ->setCountryCode("SE")
                ->doRequest();
        $this->assertTrue($campaigns->accepted);

        $pricesPerMonth = Helper::paymentPlanPricePerMonth(2000, $campaigns, true);
        $this->assertInstanceOf("Svea\WebPay\WebService\GetPaymentPlanParams\PaymentPlanPricePerMonth", $pricesPerMonth);

//        $this->assertEquals(213060, $pricesPerMonth->values[0]['campaignCode']); //don't test to be flexible
        $this->assertEquals(true, isset($pricesPerMonth->values[0]['pricePerMonth']));
    }

    public function test_Checkout_SveaConfigurationProvider()
    {
        $responseSE = WebPay::checkout(ConfigurationService::getTestConfig())
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setLocale("sv-se")
            ->setCheckoutUri("http://localhost")
            ->setTermsUri("http://localhost")
            ->setPushUri("http://localhost")
            ->setConfirmationUri("http://localhost")
            ->setClientOrderNumber("PHP_IntegrationTest_" . rand(0, 10000000))
            ->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("1")
                ->setQuantity(2)
                ->setAmountIncVat(1000.00)
                ->setDescription("Specification")
                ->setName('Prod')
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
            )->createOrder();

        $this->assertEquals($responseSE['CountryCode'], "SE");

        $responseNO = WebPay::checkout(ConfigurationService::getTestConfig())
            ->setCountryCode("NO")
            ->setCurrency("NOK")
            ->setLocale("nn-no")
            ->setCheckoutUri("http://localhost")
            ->setTermsUri("http://localhost")
            ->setPushUri("http://localhost")
            ->setConfirmationUri("http://localhost")
            ->setClientOrderNumber("PHP_IntegrationTest_" .rand(0, 10000000))
            ->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("1")
                ->setQuantity(2)
                ->setAmountIncVat(1000.00)
                ->setDescription("Specification")
                ->setName('Prod')
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
            )->createOrder();

        $this->assertEquals($responseNO['CountryCode'], "NO");
    }
}