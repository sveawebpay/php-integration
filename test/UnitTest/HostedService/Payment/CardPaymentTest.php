<?php

namespace Svea\WebPay\Test\UnitTest\HostedService\Payment;

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\HostedService\Payment\CardPayment as CardPayment;

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CardPaymentTest extends \PHPUnit\Framework\TestCase {

    public function testSetAuthorization() {
        $form = WebPay::createOrder(new TestConf())
                ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222)->setIpAddress("123.123.123"))
                ->addOrderRow(TestUtil::createOrderRow())
                ->setCountryCode("SE")
                ->setClientOrderNumber("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->usePayPageCardOnly() // PayPageObject
                ->setPayPageLanguage("sv")
                ->setReturnUrl("http://myurl.se")
                ->getPaymentForm();

        $this->assertEquals("1130", $form->merchantid);
        $this->assertEquals('8a9cece566e808da63c6f07ff415ff9e127909d000d259aba24daa2fed6d9e3f8b0b62e8ad1fa91c7d7cd6fc3352deaae66cdb533123edf127ad7d1f4c77e7a3', $form->secretWord);
    }

    public function testBuildCardPayment() {
        $rowFactory = new TestUtil();
        $config = ConfigurationService::getDefaultConfig();
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
                ->addCustomerDetails(WebPayItem::companyCustomer()
                    ->setNationalIdNumber("2345234")
                )
                ->setCountryCode("SE")
                ->setClientOrderNumber("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->usePayPageCardOnly() // PayPageObject
                    ->setReturnUrl("http://myurl.se")
                    ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);
        $payment = base64_decode($form->xmlMessageBase64);
        $payment_decoded = new \SimpleXMLElement($payment);

        //test values are as expected avter transforming xml to php object
        $this->assertEquals('SEK', $xmlMessage->currency);
        $this->assertEquals('18750', $xmlMessage->amount);
        $this->assertEquals('3750', $xmlMessage->vat);
        $this->assertEquals('12500', $xmlMessage->orderrows->row[0]->amount);
        $this->assertEquals('6250', $xmlMessage->orderrows->row[1]->amount);
        $this->assertEquals('-12500', $xmlMessage->orderrows->row[2]->amount);
    }

    public function testCardPaymentForEngCustomer() {
        $config = ConfigurationService::getDefaultConfig();
        $rowFactory = new TestUtil();
        $form = WebPay::createOrder($config)
                ->addOrderRow(TestUtil::createOrderRow())
                ->setCountryCode("SE")
                ->setClientOrderNumber("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("GBP")
                ->usePayPageCardOnly() // PayPageObject
                ->setReturnUrl("http://myurl.se")
                ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);
        $payment = base64_decode($form->xmlMessageBase64);
        $payment_decoded = new \SimpleXMLElement($payment);

        //test values are as expected avter transforming xml to php object
        $this->assertEquals('GBP', $xmlMessage->currency);
    }

    public function testBuildCardPaymentWithDiffrentProductVatAndDiscount() {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
                ->addOrderRow(WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountExVat(240.00)
                    ->setDescription("CD")
                    ->setVatPercent(25)
                )
                ->addOrderRow(WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountExVat(188.68)
                    ->setDescription("Bok")
                    ->setVatPercent(6)
                )
                ->addDiscount(WebPayItem::fixedDiscount()
                    ->setDiscountId("1")
                    ->setAmountIncVat(100.00)
                    ->setUnit("st")
                    ->setDescription("testBuildCardPaymentWithDiffrentProductVatAndDiscount")
                    ->setName("Fixed")
                )
                ->setCountryCode("SE")
                ->setClientOrderNumber("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->usePayPageCardOnly() // PayPageObject
                ->setReturnUrl("http://myurl.se")
                ->setPayPageLanguage("sv")
                ->getPaymentForm();
        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);

        $this->assertEquals('40000', $xmlMessage->amount);
        $this->assertEquals('5706', $xmlMessage->vat);
    }

    public function testBuildCardPaymentWithAmountIncVatWithVatPercent() {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
                ->addOrderRow(WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountIncVat(300.00)
                    //->setAmountExVat(240.00)
                    ->setDescription("CD")
                    ->setVatPercent(25)
                )
                ->addOrderRow(WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountIncVat(200.00)
                    ->setDescription("Bok")
                    ->setVatPercent(6)
                )
                ->addDiscount(WebPayItem::fixedDiscount()
                    ->setDiscountId("1")
                    ->setAmountIncVat(100.00)
                    ->setUnit("st")
                    ->setDescription("testBuildCardPaymentWithAmountIncVatWithVatPercent")
                    ->setName("Fixed")
                )
                ->addFee(WebPayItem::shippingFee()
                    ->setShippingId('33')
                    ->setName('shipping')
                    ->setDescription("Specification")
                    ->setAmountIncVat(62.50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                )
                ->setCountryCode("SE")
                ->setClientOrderNumber("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->usePayPageCardOnly() // PayPageObject
                ->setReturnUrl("http://myurl.se")
                ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);

        $this->assertEquals('46250', $xmlMessage->amount);
        $this->assertEquals('6956', $xmlMessage->vat);
    }

    public function testBuildCardPaymentWithAmountExVatWithAmountIncVat() {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
                ->addOrderRow(WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountExVat(240.00)
                    ->setAmountIncVat(300.00)
                    ->setDescription("CD")
                    //->setVatPercent(25)
                )
                ->addOrderRow(WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountExVat(188.68)
                    ->setAmountIncVat(200.00)
                    ->setDescription("Bok")
                    //->setVatPercent(6)
                )
                ->addDiscount(WebPayItem::fixedDiscount()
                    ->setDiscountId("1")
                    ->setAmountIncVat(100.00)
                    ->setUnit("st")
                    ->setDescription("testBuildCardPaymentWithAmountExVatWithAmountIncVat")
                    ->setName("Fixed")
                )
                ->setCountryCode("SE")
                ->setClientOrderNumber("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->usePayPageCardOnly() // PayPageObject
                ->setReturnUrl("http://myurl.se")
                ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);

        $this->assertEquals('40000', $xmlMessage->amount);
        $this->assertEquals('5706', $xmlMessage->vat);
        $this->assertEquals('30000', $xmlMessage->orderrows->row[0]->amount);
        $this->assertEquals('20000', $xmlMessage->orderrows->row[1]->amount);
        $this->assertEquals('-10000', $xmlMessage->orderrows->row[2]->amount);
    }

    public function testBuildCardPaymentWithCurrency() {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
                ->addOrderRow(WebPayItem::orderRow()
                        ->setArticleNumber("1")
                        ->setQuantity(1)
                        ->setAmountExVat(240.00)
                        ->setAmountIncVat(300.00)
                        ->setDescription("CD")
                )
                ->setClientOrderNumber("33")
                ->setCurrency(" sek")
                ->setCountryCode("SE")
                ->usePayPageCardOnly() // PayPageObject
                ->setReturnUrl("http://myurl.se")
                ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);

        $this->assertEquals('SEK', $xmlMessage->currency);
    }

    public function testBuildCardPaymentWithShippingFee() {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
                ->addOrderRow(WebPayItem::orderRow()
                        ->setArticleNumber("1")
                        ->setQuantity(1)
                        ->setAmountExVat(240.00)
                        ->setAmountIncVat(300.00)
                        ->setDescription("CD")
                )
                ->addFee(WebPayItem::shippingFee()
                        ->setAmountExVat(80)
                        ->setAmountIncVat(100)
                )
                ->setClientOrderNumber("33")
                ->setCurrency("sek")
                ->setCountryCode("SE")
                ->usePayPageCardOnly() // PayPageObject
                ->setReturnUrl("http://myurl.se")
                ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);

        $this->assertEquals("8000", $xmlMessage->vat);
        $this->assertEquals("40000", $xmlMessage->amount);
    }

    public function testBuildCardPaymentWithDecimalLongPrice() {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
                ->addOrderRow(WebPayItem::orderRow()
                        ->setArticleNumber("1")
                        ->setQuantity(1)
                        ->setAmountExVat(240.303030)
                        ->setAmountIncVat(300.00)
                        ->setDescription("CD")
                )
                ->setClientOrderNumber("33")
                ->setCurrency("sek")
                ->setCountryCode("SE")
                ->usePayPageCardOnly() // PayPageObject
                ->setReturnUrl("http://myurl.se")
                ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);
        $this->assertEquals("5970", $xmlMessage->vat);
        $this->assertEquals("30000", $xmlMessage->amount);
    }

    public function testBuildCardPaymentNLCustomer() {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
                ->addOrderRow(WebPayItem::orderRow()
                        ->setArticleNumber("1")
                        ->setQuantity(1)
                        ->setAmountExVat(240.303030)
                        ->setAmountIncVat(300.00)
                        ->setDescription("CD")
                )
                ->setClientOrderNumber("33")
                ->setCurrency("sek")
                ->setCountryCode("NL")
                ->usePayPageCardOnly() // PayPageObject
                ->setReturnUrl("http://myurl.se")
                ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);
        $this->assertEquals("5970", $xmlMessage->vat);
        $this->assertEquals("30000", $xmlMessage->amount);
    }

    public function testBuildCardPaymentWithAmountAndVatCero() {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
                ->addOrderRow(WebPayItem::orderRow()
                        ->setArticleNumber("1")
                        ->setQuantity(1)
                        ->setAmountExVat(0.00)
                        ->setAmountIncVat(0.00)
                        ->setDescription("Free shipping")
                )
                ->setClientOrderNumber("33")
                ->setCurrency("sek")
                ->setCountryCode("NL")
                ->usePaymentMethod("KORTCERT")
                ->setReturnUrl("http://myurl.se")
                ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);
        $this->assertEquals("0", $xmlMessage->orderrows->row->vat);
        $this->assertEquals("0", $xmlMessage->orderrows->row->amount);
    }

    /*
     * new feature 2013-10-08
     */
    public function testSetCardPageLanguage() {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
                ->addOrderRow(WebPayItem::orderRow()
                        ->setArticleNumber("1")
                        ->setQuantity(1)
                        ->setAmountExVat(100.00)
                        ->setAmountIncVat(125.00)
                        ->setDescription("Free shipping")
                )
                ->setClientOrderNumber("33")
                ->setCurrency("sek")
                ->setCountryCode("SE")
                ->usePaymentMethod("KORTCERT")
                    ->setCardPageLanguage("sv")
                    ->setReturnUrl("http://myurl.se")
                    ->getPaymentForm();


        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);
        $this->assertEquals("sv", $xmlMessage->lang);
    }
    public function testCallbackUrl() {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
                ->addOrderRow(WebPayItem::orderRow()
                        ->setArticleNumber("1")
                        ->setQuantity(1)
                        ->setAmountExVat(100.00)
                        ->setAmountIncVat(125.00)
                        ->setDescription("Free shipping")
                )
                ->setClientOrderNumber("33")
                ->setCurrency("sek")
                ->setCountryCode("SE")
                ->usePaymentMethod("KORTCERT")
                    ->setCallbackUrl("http://myurl.se")
                    ->setReturnUrl("http://myurl.se")
                    ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);
         $this->assertEquals("http://myurl.se", $xmlMessage->callbackurl);

    }
    public function testNegativeOrderrow() {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
                ->addOrderRow(WebPayItem::orderRow()
                        ->setArticleNumber("1")
                        ->setQuantity(1)
                        ->setAmountExVat(-100.00)
                        ->setVatPercent(25)
                        ->setDescription("Free shipping")
                )
                ->setClientOrderNumber("33")
                ->setCurrency("sek")
                ->setCountryCode("SE")
                ->usePaymentMethod("KORTCERT")
                    ->setCallbackUrl("http://myurl.se")
                    ->setReturnUrl("http://myurl.se")
                    ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);
         $this->assertEquals("-12500", $xmlMessage->amount);
         $this->assertEquals("-2500", $xmlMessage->vat);
    }

    public function test_BuildCardPayment_With_InvoiceFee_ExVat_IncVat() {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
                ->addOrderRow(WebPayItem::orderRow()
                        ->setArticleNumber("1")
                        ->setQuantity(1)
                        ->setAmountExVat(240.00)
                        ->setAmountIncVat(300.00)
                        ->setDescription("CD")
                )
                ->addFee(WebPayItem::invoiceFee()
                        ->setAmountExVat(80)
                        ->setAmountIncVat(100)
                        ->setName("test_BuildCardPayment_With_InvoiceFee title")
                        ->setDescription("test_BuildCardPayment_With_InvoiceFee description")
                        ->setUnit("kr")
                )
                ->setClientOrderNumber("33")
                ->setCurrency("SEK")
                ->setCountryCode("SE")
                ->usePayPageCardOnly() // PayPageObject
                    ->setReturnUrl("http://myurl.se")
                    ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);

        $this->assertEquals("8000", $xmlMessage->vat);
        $this->assertEquals("40000", $xmlMessage->amount);
    }


    /**
     * test that we can set the subscriptiontype using setSubscriptionType()
     */
    public function test_cardPayment_setSubscriptionType() {
        $cardPayment = new CardPayment(TestUtil::createOrder());
        $cardPayment->setSubscriptionType(CardPayment::RECURRINGCAPTURE);

        $this->assertEquals( CardPayment::RECURRINGCAPTURE, $cardPayment->subscriptionType );
    }

    /**
     * test that <subscriptiontype> is included in payment request xml
     */
    public function test_cardPayment_request_xml_includes_subscriptiontype() {
        $cardPayment = new CardPayment(TestUtil::createOrder()->setClientOrderNumber("33"));
        $cardPayment
            ->setSubscriptionType(CardPayment::RECURRINGCAPTURE)
            ->setCallbackUrl("http://myurl.se")
            ->setReturnUrl("http://myurl.se")
        ;
        $paymentForm = $cardPayment->getPaymentForm();

        $subscriptiontype = "<subscriptiontype>RECURRINGCAPTURE<\/subscriptiontype>"; // remember to escape <_/_subscriptiontype>
        //$this->assertRegExp("/[a-zA-Z0-9<>]*".$subscriptiontype."[a-zA-Z0-9<>]*/","foo<subscriptiontype>RECURRINGCAPTURE</subscriptiontype>bar");

        ////print_r($paymentForm->xmlMessage);
        $this->assertRegExp("/[a-zA-Z0-9<>]*".$subscriptiontype."[a-zA-Z0-9<>]*/", $paymentForm->xmlMessage );
    }
}

