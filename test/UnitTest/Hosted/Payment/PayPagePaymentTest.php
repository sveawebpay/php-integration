<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../test/UnitTest/BuildOrder/OrderBuilderTest.php';
require_once $root . '/../../../../test/UnitTest/BuildOrder/TestRowFactory.php';

/**
 * Description of PayPagePaymentTest
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class PayPagePaymentTest extends PHPUnit_Framework_TestCase {
   

     function testBuildPayPagePaymentWithExcludepaymentMethods(){
        $rowFactory = new TestRowFactory();
       $form = WebPay::createOrder()
            ////->setTestmode()()()
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
               ->addCustomerDetails(Item::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setClientOrderNumber("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->usePayPage()
                    ->setReturnUrl("http://myurl.se")
                    ->excludePaymentMethods(PaymentMethod::INVOICE, PaymentMethod::KORTCERT)
                    ->getPaymentForm();

        $xmlMessage = new SimpleXMLElement($form->xmlMessage);
        //test values are as expected avter transforming xml to php object
        $this->assertEquals('SEK', $xmlMessage->currency);
        $this->assertEquals('18750', $xmlMessage->amount);
        $this->assertEquals('3750', $xmlMessage->vat); //may change when we recaltulate in Cartpymentclass
        $this->assertEquals('12500', $xmlMessage->orderrows->row[0]->amount);
        $this->assertEquals('6250', $xmlMessage->orderrows->row[1]->amount);
        $this->assertEquals('-12500', $xmlMessage->orderrows->row[2]->amount);
        //  $this->assertEquals(PaymentMethod::KORTCERT,$xmlMessage->paymentMethod);
        $this->assertEquals(SystemPaymentMethod::INVOICE_SE, $xmlMessage->excludepaymentmethods->exclude[0]);
    }
   

    function testpayPagePaymentExcludeCardPayments() {
        $rowFactory = new TestRowFactory();
        $form = WebPay::createOrder()
            //->setTestmode()()
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
            ->addCustomerDetails(Item::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setClientOrderNumber("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->usePayPage()
                    ->setReturnUrl("http://myurl.se")
                    ->excludeCardPaymentMethods()
                    ->getPaymentForm();

        $xmlMessage = new SimpleXMLElement($form->xmlMessage);
        $this->assertEquals(PaymentMethod::KORTCERT, $xmlMessage->excludepaymentmethods->exclude[0]);
    }

    function testExcludeDirectPaymentMethods() {
        $rowFactory = new TestRowFactory();
        $form = WebPay::createOrder()
            //->setTestmode()()
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
            ->addCustomerDetails(Item::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setClientOrderNumber("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->usePayPage()
                    ->setReturnUrl("http://myurl.se")
                    ->excludeDirectPaymentMethods()
                    ->getPaymentForm();

        $xmlMessage = new SimpleXMLElement($form->xmlMessage);
        $this->assertEquals(PaymentMethod::BANKAXESS, $xmlMessage->excludepaymentmethods->exclude[0]);
    }

    function testpayPagePaymentIncludePaymentMethods() {
        $rowFactory = new TestRowFactory();
        $form = WebPay::createOrder()
            //->setTestmode()()
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
                 ->addCustomerDetails(Item::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setClientOrderNumber("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->usePayPage()
                    ->setReturnUrl("http://myurl.se")
                    ->includePaymentMethods(PaymentMethod::KORTCERT, PaymentMethod::KORTSKRILL)
                    ->getPaymentForm();
        
        $xmlMessage = new SimpleXMLElement($form->xmlMessage);
        //check to see if the first value is one of the excluded ones
        $this->assertEquals(SystemPaymentMethod::BANKAXESS, $xmlMessage->excludepaymentmethods->exclude[0]);
    }

   
}

?>