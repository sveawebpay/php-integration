<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../test/UnitTest/BuildOrder/OrderBuilderTest.php';
require_once $root . '/../../../../test/UnitTest/BuildOrder/TestRowFactory.php';

/**
 * Description of PaymentMethodTest
 *
 * @author anne-hal
 */
class PaymentMethodTest extends PHPUnit_Framework_TestCase{

     function testPayPagePaymentWithSetPaymentMethod() {
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
                    ->usePaymentMethod(PaymentMethod::KORTCERT)
                    ->setReturnUrl("http://myurl.se")
                        ->getPaymentForm();

        $xmlMessage = new SimpleXMLElement($form->xmlMessage);
        $this->assertEquals(PaymentMethod::KORTCERT, $xmlMessage->paymentmethod[0]);
    }
     function testPayPagePaymentWithSetPaymentMethodInvoice() {
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
            ->addCustomerDetails(Item::companyCustomer()->setNationalIdNumber(4608142222))
                ->setCountryCode("SE")
                ->setClientOrderNumber("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                    ->usePaymentMethod(PaymentMethod::INVOICE)
                    ->setReturnUrl("http://myurl.se")
                    ->getPaymentForm();

        $xmlMessage = new SimpleXMLElement($form->xmlMessage);
        $this->assertEquals(SystemPaymentMethod::INVOICE_SE, $xmlMessage->paymentmethod[0]);
        $this->assertEquals("TRUE", $xmlMessage->iscompany);
        $this->assertEquals("4608142222", $xmlMessage->customer->ssn);
    }
     function testPaymentMethodInvoiceNL() {
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
            ->addCustomerDetails(Item::individualCustomer()
                    ->setInitials("SB")
                    ->setBirthDate(1923, 12, 12)
                    ->setName("Sneider", "Boasman")
                    ->setEmail("test@svea.com")
                    ->setPhoneNumber(999999)
                    ->setIpAddress("123.123.123")
                    ->setStreetAddress("Gatan", 23)
                    ->setCoAddress("c/o Eriksson")
                    ->setZipCode(9999)
                    ->setLocality("Stan")
                    )
                ->setCountryCode("NL")
                ->setClientOrderNumber("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                    ->usePaymentMethod(PaymentMethod::INVOICE)
                    ->setReturnUrl("http://myurl.se")
                        ->getPaymentForm();
        $xmlMessage = new SimpleXMLElement($form->xmlMessage);

        $this->assertEquals("FALSE", $xmlMessage->iscompany);
        $this->assertEquals("Sneider", $xmlMessage->customer->firstname);
    }
}

?>
