<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class AddOrderRowsBuilderIntegrationTest extends PHPUnit_Framework_TestCase {
    
    protected $invoiceIdToTest;
    protected $country;

    protected function setUp()
    {
        $this->country = "SE";
        $this->invoiceIdToTest = 123456;   // set this to the approved invoice set up by test_manual_setup_CreditOrderRows_testdata()
    }       

    // AddOrderRows() ->addInvoiceOrderRows() ->addPaymentPlanOrderRows() w/WebPayItem::OrderRow/ShippingFee/InvoiceFee/FixedDiscount/RelativeDiscount
    function test_AddOrderRows_addInvoiceOrderRows_single_row_success() {
        $country = "SE";
        $order = TestUtil::createOrderWithoutOrderRows( TestUtil::createIndividualCustomer($country) );
        $order->addOrderRow(TestUtil::createOrderRow(1.00));        
        $orderResponse = $order->useInvoicePayment()->doRequest();       
        $this->assertEquals(1, $orderResponse->accepted);
         
        $addOrderRowsBuilder = new \Svea\AddOrderRowsBuilder( Svea\SveaConfig::getDefaultConfig() );
        $addOrderRowsResponse = $addOrderRowsBuilder
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode($country)
                ->addOrderRow( TestUtil::createOrderRow(2.00) )
                ->addInvoiceOrderRows()
                    ->doRequest();
        
        $this->assertEquals(1, $addOrderRowsResponse->accepted);  
    }
        
    function test_AddOrderRows_addInvoiceOrderRows_multiple_rows_success() {
        $country = "SE";
        $order = TestUtil::createOrderWithoutOrderRows( TestUtil::createIndividualCustomer($country) );
        $order->addOrderRow(TestUtil::createOrderRow(1.00, 1));        
        $orderResponse = $order->useInvoicePayment()->doRequest();       
        $this->assertEquals(1, $orderResponse->accepted);

        $addOrderRowsBuilder = new \Svea\AddOrderRowsBuilder( Svea\SveaConfig::getDefaultConfig() );
        $addOrderRowsResponse = $addOrderRowsBuilder
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode($country)
                ->addOrderRow( TestUtil::createOrderRow(2.00, 1) )
                ->addOrderRow( TestUtil::createOrderRow(3.00, 1) )
                ->addInvoiceOrderRows()
                    ->doRequest();
        
        //print_r("test_AddOrderRows_addInvoiceOrderRows_single_row_success: "); print_r( $orderResponse->sveaOrderId );
        $this->assertEquals(1, $addOrderRowsResponse->accepted);         
    }   
    
    function test_AddOrderRows_addPaymentPlanOrderRows_multiple_rows_success() {
        $country = "SE";
        $order = TestUtil::createOrderWithoutOrderRows( TestUtil::createIndividualCustomer($country) );
        $order->addOrderRow(TestUtil::createOrderRow(2000.00, 1));        
        $orderResponse = $order->usePaymentPlanPayment( TestUtil::getGetPaymentPlanParamsForTesting($country) )->doRequest();       
        $this->assertEquals(1, $orderResponse->accepted);

        $addOrderRowsBuilder = new \Svea\AddOrderRowsBuilder( Svea\SveaConfig::getDefaultConfig() );
        $addOrderRowsResponse = $addOrderRowsBuilder
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode($country)
                ->addOrderRow( TestUtil::createOrderRow(2.00, 1) )
                ->addOrderRow( TestUtil::createOrderRow(3.00, 1) )
                ->addPaymentPlanOrderRows()
                    ->doRequest();
        
        //print_r("test_AddOrderRows_addInvoiceOrderRows_single_row_success: "); print_r( $orderResponse->sveaOrderId );
        $this->assertEquals(1, $addOrderRowsResponse->accepted);         
    }  
    
    function test_AddOrderRows_addInvoiceOrderRows_specified_with_price_specified_using_inc_vat_and_ex_vat() {
        $country = "SE";
        $order = TestUtil::createOrderWithoutOrderRows( TestUtil::createIndividualCustomer($country) );       
        $order->addOrderRow( WebPayItem::orderRow()
            ->setArticleNumber("1")
            ->setQuantity( 1 )
            ->setAmountExVat( 100.00 )
            ->setVatPercent(25)
            ->setDescription("Specification")
            ->setName('Product')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );               
        $orderResponse = $order->useInvoicePayment()->doRequest();       
        $this->assertEquals(1, $orderResponse->accepted);

        $addOrderRowsBuilder = new \Svea\AddOrderRowsBuilder( Svea\SveaConfig::getDefaultConfig() );
        $addOrderRowsResponse = $addOrderRowsBuilder
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode($country)
                ->addOrderRow( WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity( 1 )
                    //->setAmountExVat( 1.00 )
                    ->setAmountIncVat( 1.00 * 1.25 ) 
                    ->setVatPercent(25)
                    ->setDescription("Specification")
                    ->setName('Product')
                    ->setUnit("st")
                    ->setDiscountPercent(0)
                ) 
                ->addOrderRow( WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity( 1 )
                    ->setAmountExVat( 4.00 )
                    ->setAmountIncVat( 4.00 * 1.25 ) 
                    //->setVatPercent(25)
                    ->setDescription("Specification")
                    ->setName('Product')
                    ->setUnit("st")
                    ->setDiscountPercent(0)
                )                
                ->addInvoiceOrderRows()
                    ->doRequest();
        
        //print_r("test_AddOrderRows_addInvoiceOrderRows_specified_with_price_specified_using_inc_vat_and_ex_vat: "); print_r( $orderResponse->sveaOrderId );
        $this->assertEquals(1, $addOrderRowsResponse->accepted);
        // todo query result & check amounts, description automatically
    }   
    
    
}


?>