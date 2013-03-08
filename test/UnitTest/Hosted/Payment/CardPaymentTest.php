<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../test/UnitTest/BuildOrder/OrderBuilderTest.php';
require_once $root . '/../../../../test/UnitTest/BuildOrder/TestRowFactory.php';

/**
 * Description of CardPaymentTest
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CardPaymentTest extends PHPUnit_Framework_TestCase {

    function testSetAuthorization() {
        $form = WebPay::createOrder()
                ->setTestmode()
                ->addCustomerDetails(Item::individualCustomer()->setNationalIdNumber(194605092222)->setIpAddress("123.123.123"))
                ->addOrderRow(Item::orderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountExVat(100.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                    )
                ->setCountryCode("SE")
                ->setClientOrderNumber("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->usePayPageCardOnly() // PayPageObject
                    ->setMerchantIdBasedAuthorization(4444, "secret")
                    ->setPayPageLanguage("sv")
                    ->setReturnUrl("http://myurl.se")
                    ->getPaymentForm();
        $this->assertEquals(4444, $form->merchantid);
        $this->assertEquals('secret', $form->secretWord);
    }

    function testBuildCardPayment() {
        $rowFactory = new TestRowFactory();
        $form = WebPay::createOrder()
                ->setTestmode()
                ->addOrderRow(Item::orderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountExVat(100.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                    )
            ->run($rowFactory->buildShippingFee())
            ->addDiscount(Item::relativeDiscount()
                    ->setDiscountId("1")
                    ->setDiscountPercent(50)
                    ->setUnit("st")
                    ->setName('Relative')
                    ->setDescription("RelativeDiscount")
                    )
                ->addCustomerDetails(Item::companyCustomer()
                        ->setNationalIdNumber("2345234")
                        )
                ->setCountryCode("SE")
                ->setClientOrderNumber("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->usePayPageCardOnly() // PayPageObject
                    ->setReturnUrl("http://myurl.se")
                    ->getPaymentForm();

       

        $xmlMessage = new SimpleXMLElement($form->xmlMessage);
        $payment = base64_decode($form->xmlMessageBase64);
        $payment_decoded = new SimpleXMLElement($payment);
        
        //test values are as expected avter transforming xml to php object
        $this->assertEquals('SEK', $xmlMessage->currency);
        $this->assertEquals('18750', $xmlMessage->amount);
        $this->assertEquals('3750', $xmlMessage->vat);
        $this->assertEquals('12500', $xmlMessage->orderrows->row[0]->amount);
        $this->assertEquals('6250', $xmlMessage->orderrows->row[1]->amount);
        $this->assertEquals('-12500', $xmlMessage->orderrows->row[2]->amount);
    }

    function testBuildCardPaymentWithDiffrentProductVatAndDiscount() {
        $form = WebPay::createOrder()
                ->addOrderRow(Item::orderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(1)
                    ->setAmountExVat(240.00)
                    ->setDescription("CD")
                    ->setVatPercent(25)
                    )
                ->addOrderRow(Item::orderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(1)
                    ->setAmountExVat(188.68)
                    ->setDescription("Bok")
                    ->setVatPercent(6)
                    )
                ->addDiscount(Item::fixedDiscount()
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
        $xmlMessage = new SimpleXMLElement($form->xmlMessage);
        
        $this->assertEquals('40000', $xmlMessage->amount);
        $this->assertEquals('5706', $xmlMessage->vat);
    }
    function testBuildCardPaymentWithAmountIncVatWithVatPercent() {
        $form = WebPay::createOrder()
                ->addOrderRow(Item::orderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(1)
                    ->setAmountIncVat(300)
                    //->setAmountExVat(240.00)
                    ->setDescription("CD")
                    ->setVatPercent(25)
                    )
                ->addOrderRow(Item::orderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(1)
                    ->setAmountIncVat(200)
                    ->setDescription("Bok")
                    ->setVatPercent(6)
                    )
                ->addDiscount(Item::fixedDiscount()
                    ->setDiscountId("1")
                    ->setAmountIncVat(100.00)
                    ->setUnit("st")
                    ->setDescription("FixedDiscount")
                    ->setName("Fixed")
                    )
                ->addFee(Item::shippingFee()
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

        $xmlMessage = new SimpleXMLElement($form->xmlMessage);
        
        $this->assertEquals('46250', $xmlMessage->amount);
        $this->assertEquals('6956', $xmlMessage->vat);
    }
    function testBuildCardPaymentWithAmountExVatWithAmountIncVat() {
        $form = WebPay::createOrder()
                ->addOrderRow(Item::orderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(1)
                    ->setAmountExVat(240.00)
                    ->setAmountIncVat(300)
                    ->setDescription("CD")
                    //->setVatPercent(25)
                    )
                ->addOrderRow(Item::orderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(1)
                    ->setAmountExVat(188.68)
                    ->setAmountIncVat(200)
                    ->setDescription("Bok")
                    //->setVatPercent(6) 
                    )
                ->addDiscount(Item::fixedDiscount()
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

        $xmlMessage = new SimpleXMLElement($form->xmlMessage);
        
        $this->assertEquals('40000', $xmlMessage->amount);
        $this->assertEquals('5706', $xmlMessage->vat);
        $this->assertEquals('30000', $xmlMessage->orderrows->row[0]->amount);
        $this->assertEquals('20000', $xmlMessage->orderrows->row[1]->amount);
        $this->assertEquals('-10000', $xmlMessage->orderrows->row[2]->amount);
    }
    function testBuildCardPaymentWithCurrency() {
        $form = WebPay::createOrder()
                ->addOrderRow(Item::orderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(1)
                    ->setAmountExVat(240.00)
                    ->setAmountIncVat(300)
                    ->setDescription("CD")     
                    )
            ->setClientOrderNumber("33")
            ->setCurrency(" sek")
            ->usePayPageCardOnly() // PayPageObject
            ->setReturnUrl("http://myurl.se")
                ->getPaymentForm();

        $xmlMessage = new SimpleXMLElement($form->xmlMessage);
        
        $this->assertEquals('SEK', $xmlMessage->currency);
    }
}

?>
