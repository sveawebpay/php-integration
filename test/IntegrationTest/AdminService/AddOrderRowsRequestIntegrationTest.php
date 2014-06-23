<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class AddOrderRowsRequestIntegrationTest extends PHPUnit_Framework_TestCase{

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
           
        // test runs in 17.16 seconds, 17.67 s, 3.16, 17.66, 2.84
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
        $this->assertInstanceOf('Svea\AdminService\AddOrderRowsResponse', $addOrderRowsResponse);
        $this->assertEquals(1, $addOrderRowsResponse->accepted );
    }
    
    public function test_add_single_orderRow__does_not_use_TestUtil_methods() {

        // create order
        $country = "SE";

        // test runs in 3.73 seconds, 17.99 s, 17.8, 18.09 s, 3.87, 2.98, 3.3
        $customer = WebPayItem::individualCustomer()
                ->setNationalIdNumber("194605092222")
                ->setBirthDate(1946, 05, 09)
                ->setName("Tess T", "Persson")
                ->setStreetAddress("Testgatan", 1)
                ->setCoAddress("c/o Eriksson, Erik")
                ->setLocality("Stan")
                ->setZipCode("99999")
        ; 
        $config = Svea\SveaConfig::getDefaultConfig();        

        $amount = 100.00; $quantity = 2;
        $testUtil_createOrderRow = WebPayItem::orderRow()
                ->setArticleNumber("1")
                ->setQuantity( $quantity )
                ->setAmountExVat( $amount )
                ->setDescription("Specification")
                ->setName('Product')
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0);
        ;        
                
        $order = WebPay::createOrder($config)
                ->addOrderRow( $testUtil_createOrderRow )
                ->addCustomerDetails( $customer )
                ->setCountryCode("SE")
                ->setCurrency("SEK")
                ->setCustomerReference("created by TestUtil::createOrder()")
                ->setClientOrderNumber( "clientOrderNumber:".date('c'))
                ->setOrderDate( date('c') )
        ;        
        
        $order->addOrderRow( WebPayItem::orderRow()
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
        $this->assertInstanceOf('Svea\AdminService\AddOrderRowsResponse', $addOrderRowsResponse);
        $this->assertEquals(1, $addOrderRowsResponse->accepted );
    }        
    
}
