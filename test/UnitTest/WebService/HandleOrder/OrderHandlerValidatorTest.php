<?php

use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;


/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class OrderHandlerValidatorTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : OrderId is required. Use function setOrderId() with the SveaOrderId from the createOrder response.
     */
    public function test_deliverPaymentPlanOrder_with_missing_OrderId_raises_exception() {
        $config = \Svea\WebPay\Config\SveaConfig::getDefaultConfig();
        $builder = WebPay::deliverOrder($config);
        $object = $builder;

        $object->deliverPaymentPlanOrder()
            ->prepareRequest();
    }
    
    public function test_deliverPaymentPlanOrder_with_missing_invoiceDistributionType_validates_OK() {
        $config = \Svea\WebPay\Config\SveaConfig::getDefaultConfig();
        $builder = WebPay::deliverOrder($config);
        $request = $builder
            ->setOrderId(123456)
            ->setCountryCode("SE")
            ->addOrderRow(TestUtil::createOrderRow())
            ->deliverPaymentPlanOrder()
                ->prepareRequest();
    }    

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : InvoiceDistributionType is required for deliverInvoiceOrder. Use function setInvoiceDistributionType().
     */    
    public function test_deliverInvoiceOrder_with_missing_invoiceDistributionType_raises_exception() {
        $config = \Svea\WebPay\Config\SveaConfig::getDefaultConfig();
        $builder = WebPay::deliverOrder($config);
        $request = $builder
            ->setOrderId(123456)
            ->setCountryCode("SE")
            ->addOrderRow(TestUtil::createOrderRow())
            ->deliverInvoiceOrder()
                ->prepareRequest();
    }    
    
    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : InvoiceDistributionType is required for deliverInvoiceOrder. Use function setInvoiceDistributionType().
     */
    public function testFailOnMissingInvoiceDetailsOnInvoiceDeliver() {
        $config = \Svea\WebPay\Config\SveaConfig::getDefaultConfig();
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
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage No rows has been included. Use function beginOrderRow(), beginShippingfee() or beginInvoiceFee().
     * 
     * 2.0 goes directly to DeliverInvoice
     */
    public function testFailOnMissingOrderRowsOnInvoiceDeliver() {
        $config = \Svea\WebPay\Config\SveaConfig::getDefaultConfig();
        $builder = new \Svea\WebPay\BuildOrder\DeliverOrderBuilder($config);
        $builder
            ->setOrderId('id')
            ->setInvoiceDistributionType('Post')
        ;        
        $object = new \Svea\WebPay\WebService\HandleOrder\DeliverInvoice( $builder );
        $object->prepareRequest();
    }  
}
