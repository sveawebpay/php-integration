<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../test/UnitTest/BuildOrder/OrderBuilderTest.php';

$root = realpath(dirname(__FILE__));
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
            ->addRelativeDiscount(Item::relativeDiscount()
                    ->setDiscountId("1")
                    ->setDiscountPercent(50)
                    ->setUnit("st")
                    ->setName('Relative')
                    ->setDescription("RelativeDiscount") 
                    )
            ->addCustomerDetails(Item::individualCustomer()->setSsn(194605092222))
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
}

?>
