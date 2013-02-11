<?php

$root = realpath(dirname(__FILE__));

require_once $root . '/../../../../src/Includes.php';

/**
 * Description of OrderValidatorTest
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class OrderHandlerValidatorTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException ValidationException
     * @expectedExceptionMessage -missing value : OrderId is required. Use function setOrderId() with the id recieved when creating an order.
     */
    function testFailOnMissingOrderIdOnPaymentPlanDeliver() {
        $builder = WebPay::deliverOrder();

        $object = $builder;
        
     $object->deliverPaymentPlanOrder()
             ->prepareRequest();
    }

    /**
     * @expectedException ValidationException
     * @expectedExceptionMessage -missing value : InvoiceDistributionType is requred for deliverInvoiceOrder. Use function setInvoiceDistributionType().
     */
    function testFailOnMissingInvoiceDetailsOnInvoiceDeliver() {
        $builder = WebPay::deliverOrder();
        $object = $builder
                ->beginOrderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountExVat(100.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                ->endOrderRow()
                ->beginShippingFee()
                    ->setShippingId('33')
                    ->setName('shipping')
                    ->setDescription("Specification")
                    ->setAmountExVat(50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                ->endShippingFee()
                ->setOrderId('id')
                ->deliverInvoiceOrder();
       $object->prepareRequest();
    }

    /**
     * @expectedException ValidationException
     * @expectedExceptionMessage No rows has been included. Use function beginOrderRow(), beginShippingfee() or beginInvoiceFee().
     */
    function testFailOnMissingOrderRowsOnInvoiceDeliver() {
        $builder = WebPay::deliverOrder();
        $object = $builder
                ->setOrderId('id')
                ->setInvoiceDistributionType('Post')
                ->deliverInvoiceOrder();
        $object->prepareRequest();
    }

}

?>
