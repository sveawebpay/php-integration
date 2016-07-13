<?php

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class DeliverOrdersRequestTest extends \PHPUnit_Framework_TestCase {

    public $builderObject;
    
    public function setUp() {        
        $this->builderObject = new \Svea\WebPay\BuildOrder\DeliverOrderBuilder(\Svea\WebPay\Config\SveaConfig::getDefaultConfig());  
        $this->builderObject->setOrderId(123456);
        $this->builderObject->setCountryCode("SE");
        $this->builderObject->setInvoiceDistributionType(\Svea\WebPay\Constant\DistributionType::POST);        
        $this->builderObject->orderType = \Svea\WebPay\Config\ConfigurationProvider::INVOICE_TYPE;                                                        
    }
    
    public function testClassExists() {
        $deliverOrdersRequestObject = new \Svea\WebPay\AdminService\DeliverOrdersRequest(new \Svea\WebPay\BuildOrder\DeliverOrderBuilder(\Svea\WebPay\Config\SveaConfig::getDefaultConfig()));
        $this->assertInstanceOf('Svea\WebPay\AdminService\DeliverOrdersRequest', $deliverOrdersRequestObject);
    }
    
    public function test_validate_throws_exception_on_missing_DistributionType() {

        $this->setExpectedException(
          'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : distributionType is required.'
        );
        
        unset( $this->builderObject->distributionType );

        $deliverOrderRequestObject = new \Svea\WebPay\AdminService\DeliverOrdersRequest( $this->builderObject );
        $request = $deliverOrderRequestObject->prepareRequest();
    }
    
    public function test_validate_throws_exception_on_missing_OrderId() {

        $this->setExpectedException(
          'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : orderId is required.'
        );
        
        unset( $this->builderObject->orderId );

        $deliverOrderRequestObject = new \Svea\WebPay\AdminService\DeliverOrdersRequest( $this->builderObject );
        $request = $deliverOrderRequestObject->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_OrderType() {
        
        $this->setExpectedException(
          'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : orderType is required.'
        );
        
        unset( $this->builderObject->orderType );

        $deliverOrderRequestObject = new \Svea\WebPay\AdminService\DeliverOrdersRequest( $this->builderObject );
        $request = $deliverOrderRequestObject->prepareRequest();
    }      
}
