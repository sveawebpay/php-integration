<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../test/UnitTest/BuildOrder/OrderBuilderTest.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../TestUtil.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/TestConf.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CardPaymentTest extends \PHPUnit_Framework_TestCase {

    public function testSetAuthorization() {
        $form = \WebPay::createOrder(new TestConf())
                ->addCustomerDetails(\WebPayItem::individualCustomer()->setNationalIdNumber(194605092222)->setIpAddress("123.123.123"))
                ->addOrderRow(\TestUtil::createOrderRow())
                ->setCountryCode("SE")
                ->setClientOrderNumber("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->usePayPageCardOnly() // PayPageObject
                ->setPayPageLanguage("sv")
                ->setReturnUrl("http://myurl.se")
                ->getPaymentForm();

        $this->assertEquals("merchant", $form->merchantid);
        $this->assertEquals('secret', $form->secretWord);
    }

    public function testBuildCardPayment() {
        $rowFactory = new \TestUtil();
        $form = \WebPay::createOrder()
                ->addOrderRow(\TestUtil::createOrderRow())
                ->run($rowFactory->buildShippingFee())
                ->addDiscount(\WebPayItem::relativeDiscount()
                    ->setDiscountId("1")
                    ->setDiscountPercent(50)
                    ->setUnit("st")
                    ->setName('Relative')
                    ->setDescription("RelativeDiscount")
                )
                ->addCustomerDetails(\WebPayItem::companyCustomer()
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
        $rowFactory = new \TestUtil();
        $form = \WebPay::createOrder()
                ->addOrderRow(\TestUtil::createOrderRow())
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
        $form = \WebPay::createOrder()
                ->addOrderRow(\WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountExVat(240.00)
                    ->setDescription("CD")
                    ->setVatPercent(25)
                )
                ->addOrderRow(\WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountExVat(188.68)
                    ->setDescription("Bok")
                    ->setVatPercent(6)
                )
                ->addDiscount(\WebPayItem::fixedDiscount()
                    ->setDiscountId("1")
                    ->setAmountIncVat(100.00)
                    ->setUnit("st")
                    ->setDescription("FixedDiscount")
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
        $form = \WebPay::createOrder()
                ->addOrderRow(\WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountIncVat(300.0)
                    //->setAmountExVat(240.00)
                    ->setDescription("CD")
                    ->setVatPercent(25)
                )
                ->addOrderRow(\WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountIncVat(200.0)
                    ->setDescription("Bok")
                    ->setVatPercent(6)
                )
                ->addDiscount(\WebPayItem::fixedDiscount()
                    ->setDiscountId("1")
                    ->setAmountIncVat(100.00)
                    ->setUnit("st")
                    ->setDescription("FixedDiscount")
                    ->setName("Fixed")
                )
                ->addFee(\WebPayItem::shippingFee()
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
        $form = \WebPay::createOrder()
                ->addOrderRow(\WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountExVat(240.00)
                    ->setAmountIncVat(300.00)
                    ->setDescription("CD")
                    //->setVatPercent(25)
                )
                ->addOrderRow(\WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountExVat(188.68)
                    ->setAmountIncVat(200.00)
                    ->setDescription("Bok")
                    //->setVatPercent(6)
                )
                ->addDiscount(\WebPayItem::fixedDiscount()
                    ->setDiscountId("1")
                    ->setAmountIncVat(100.00)
                    ->setUnit("st")
                    ->setDescription("FixedDiscount")
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
        $form = \WebPay::createOrder()
                ->addOrderRow(\WebPayItem::orderRow()
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
        $form = \WebPay::createOrder()
                ->addOrderRow(\WebPayItem::orderRow()
                        ->setArticleNumber("1")
                        ->setQuantity(1)
                        ->setAmountExVat(240.00)
                        ->setAmountIncVat(300.00)
                        ->setDescription("CD")
                )
                ->addFee(\WebPayItem::shippingFee()
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
        $form = \WebPay::createOrder()
                ->addOrderRow(\WebPayItem::orderRow()
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
        $form = \WebPay::createOrder()
                ->addOrderRow(\WebPayItem::orderRow()
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
        $form = \WebPay::createOrder()
                ->addOrderRow(\WebPayItem::orderRow()
                        ->setArticleNumber("1")
                        ->setQuantity(1)
                        ->setAmountExVat(0.0)
                        ->setAmountIncVat(0.0)
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
}
