<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class test_AddOrderRowsRequestIntegrationTest extends PHPUnit_Framework_TestCase{

    public $builderObject;
    
    public function setUp() {
        $this->builderObject = new Svea\OrderBuilder( Svea\SveaConfig::getDefaultConfig());
        $this->builderObject->orderId = 123456;
        $this->builderObject->orderType = \ConfigurationProvider::INVOICE_TYPE;
        $this->builderObject->countryCode = "SE";
        $this->builderObject->orderRows = array( TestUtil::createOrderRow(10.00) );                    
    }

    public function test_add_single_orderRow() {

        // create order
        $country = "SE";
        $order = TestUtil::createOrder( TestUtil::createIndividualCustomer($country) )
            ->addOrderRow( WebPayItem::orderRow()
                ->setDescription("original row")
                ->setQuantity(1)
                ->setAmountExVat(1.00)
                ->setVatPercent(25)
            )
        ;
        $orderResponse = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        // add order rows to builderobject
        $this->builderObject->orderRows[] = TestUtil::createOrderRow( 1.00, 1 );
        $this->builderObject->orderId = $orderResponse->sveaOrderId;
                
        $addOrderRowsRequest = new Svea\AdminService\AddOrderRowsRequest( $this->builderObject );
        $addOrderRowsResponse = $addOrderRowsRequest->doRequest();
        
        //print_r( $addOrderRowsResponse );        
        $this->assertInstanceOf('Svea\AddOrderRowsResponse', $addOrderRowsResponse);
        $this->assertEquals(1, $addOrderRowsResponse->accepted );
    }
}
