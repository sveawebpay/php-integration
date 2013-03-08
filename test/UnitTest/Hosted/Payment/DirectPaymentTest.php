<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../test/UnitTest/BuildOrder/OrderBuilderTest.php';
require_once $root . '/../../../../test/UnitTest/BuildOrder/TestRowFactory.php';

/**
 * Description of DirectBankPaymentTest
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class DirectPaymentTest extends PHPUnit_Framework_TestCase {
    
    function testConfigureExcludedPaymentMethods() {
        $rowFactory = new TestRowFactory();
        $form = WebPay::createOrder()
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
            ->addCustomerDetails(Item::individualCustomer()
                    ->setNationalIdNumber(194605092222)
                    )
            ->setCountryCode("ZZ")
            ->setClientOrderNumber("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->usePayPageDirectBankOnly()
                ->setReturnUrl("http://myurl.se")
                ->getPaymentForm();
        
        $xmlMessage = new SimpleXMLElement($form->xmlMessage);
        $this->assertEquals('KORTCERT', $xmlMessage->excludepaymentmethods->exclude[0]);      
        $this->assertEquals('SKRILL', $xmlMessage->excludepaymentmethods->exclude[1]);
        $this->assertEquals('PAYPAL', $xmlMessage->excludepaymentmethods->exclude[2]);  
       // $this->assertEquals('SKRILL', $xmlMessage->excludepaymentmethods->exclude[3]);
    }
   
    function testBuildDirectBankPayment() {
        $rowFactory = new TestRowFactory();
        $form = WebPay::createOrder()
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
                ->addFee(Item::shippingFee()
                    ->setShippingId('33')
                    ->setName('shipping')
                    ->setDescription("Specification")
                    ->setAmountExVat(50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                    )
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
            // ->setTestmode()
            ->usePayPageDirectBankOnly()
            ->setReturnUrl("http://myurl.se")
            ->getPaymentForm();

        $xmlMessage = new SimpleXMLElement($form->xmlMessage);
        //test values are as expected avter transforming xml to php object
        $this->assertEquals('SEK', $xmlMessage->currency);
        $this->assertEquals('18750', $xmlMessage->amount);
        $this->assertEquals('3750', $xmlMessage->vat); //may change when we recaltulate in Cartpymentclass
        $this->assertEquals('12500', $xmlMessage->orderrows->row[0]->amount);
        $this->assertEquals('6250', $xmlMessage->orderrows->row[1]->amount);
        $this->assertEquals('-12500', $xmlMessage->orderrows->row[2]->amount);
    }
}

?>
