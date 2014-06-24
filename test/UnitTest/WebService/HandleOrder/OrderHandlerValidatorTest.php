<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../TestUtil.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class OrderHandlerValidatorTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage -missing value : orderId is required.
     */
    public function testFailOnMissingOrderIdOnPaymentPlanDeliver() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $builder = WebPay::deliverOrder($config);
        $object = $builder;

        $object->deliverPaymentPlanOrder()
            ->prepareRequest();
    }

    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage -missing value : InvoiceDistributionType is requred for deliverInvoiceOrder. Use function setInvoiceDistributionType().
     */
    public function testFailOnMissingInvoiceDetailsOnInvoiceDeliver() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $builder = WebPay::deliverOrder($config);
        $object = $builder
            ->addOrderRow(TestUtil::createOrderRow())
                ->addFee(WebPayItem::shippingFee()
                    ->setShippingId('33')
                    ->setName('shipping')
                    ->setDescription("Specification")
                    ->setAmountExVat(50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                    )
                ->setOrderId('id')
                ->deliverInvoiceOrder();
       $object->prepareRequest();
    }

    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage No rows has been included. Use function beginOrderRow(), beginShippingfee() or beginInvoiceFee().
     * 
     * 2.0 goes directly to DeliverInvoice
     */
    public function testFailOnMissingOrderRowsOnInvoiceDeliver() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $builder = new \Svea\DeliverOrderBuilder($config);
        $builder
            ->setOrderId('id')
            ->setInvoiceDistributionType('Post')
        ;        
        $object = new \Svea\WebService\DeliverInvoice( $builder );
        $object->prepareRequest();
    }

    
    
}
