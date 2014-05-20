<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class WebPayAdminIntegrationTest extends PHPUnit_Framework_TestCase {

    // CancelOrderBuilder endpoints: cancelInvoiceOrder(), cancelPaymentPlanOrder(), cancelCardOrder()
    function ____test_CancelOrderBuilder_Invoice_success() {
        $country = "SE";
        $order = TestUtil::createOrder( TestUtil::createIndividualCustomer($country) );
        $orderResponse = $order->useInvoicePayment()->doRequest();
       
        $this->assertEquals(1, $orderResponse->accepted);
         
        $cancelResponse = WebPayAdmin::cancelOrder( Svea\SveaConfig::getDefaultConfig() )
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode($country)
                ->cancelInvoiceOrder()
                    ->doRequest();
        
        $this->assertEquals(1, $cancelResponse->accepted);
    }
    
    function ____test_CancelOrderBuilder_PaymentPlan_success() {
        $country = "SE";
        $order = TestUtil::createOrder( TestUtil::createIndividualCustomer($country) )
            ->addOrderRow( WebPayItem::orderRow()
                ->setQuantity(1)
                ->setAmountExVat(1000.00)
                ->setVatPercent(25)
            )
        ;
        $orderResponse = $order->usePaymentPlanPayment( TestUtil::getGetPaymentPlanParamsForTesting() )->doRequest();

        $this->assertEquals(1, $orderResponse->accepted);
        
        $cancelResponse = WebPayAdmin::cancelOrder( Svea\SveaConfig::getDefaultConfig() )
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode($country)
                ->cancelPaymentPlanOrder()
                    ->doRequest();
        
        $this->assertEquals(1, $cancelResponse->accepted);
    }    
       
    /**
     * test_manual_CancelOrderBuilder_Card_success 
     * 
     * run this manually after you've performed a card transaction and have set
     * the transaction status to success using the tools in the logg admin.
     */  
    function ____test_manual_CancelOrderBuilder_Card_success() {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for manual test of cancelOrder for a card order'
        );
        
        // Set the below to match the transaction, then run the test.
        $customerrefno = "test_1396964349955";
        $transactionId = 580658;

        $request = WebPayAdmin::cancelOrder( Svea\SveaConfig::getDefaultConfig() )
            ->setOrderId( $transactionId )
            ->setCountryCode( "SE" )
            ->cancelCardOrder()
                ->doRequest();        
         
        $this->assertInstanceOf( "Svea\HostedAdminResponse", $response );
        
        $this->assertEquals( 1, $response->accepted );        
        $this->assertEquals( $customerrefno, $response->customerrefno );  
    }    
    
    /**
     *  test_queryOrderRows_invoice_order
     */
    function test_queryOrderRows_invoice_order() {
        // create invoice order w/three rows (2xA, 1xB)
        $country = "SE";

        $a_quantity = 2;
        $a_amountExVat = 1000.00;
        $a_vatPercent = 25;
        
        $b_quantity = 1;
        $b_amountExVat = 100.00;
        $b_vatPercent = 12;
        $b_articleNumber = "1071e";
        $b_unit = "pcs.";
        $b_name = "B Name";
        $b_description = "B Description";    
        $b_discount = 0;
        
        $order = TestUtil::createOrderWithoutOrderRows( TestUtil::createIndividualCustomer($country) )
            ->addOrderRow( WebPayItem::orderRow()
                ->setQuantity($a_quantity)
                ->setAmountExVat($a_amountExVat)
                ->setVatPercent($a_vatPercent)
            )
            ->addOrderRow( WebPayItem::orderRow()
                ->setQuantity($b_quantity)
                ->setAmountExVat($b_amountExVat)
                ->setVatPercent($b_vatPercent)
                ->setArticleNumber($b_articleNumber)
                ->setUnit($b_unit)
                ->setName($b_name)
                ->setDescription($b_description)
                ->setDiscountPercent($b_discount)              
            )                
        ;
        $orderResponse = $order->useInvoicePayment()->doRequest();

//print_r($orderResponse);
        $this->assertEquals(1, $orderResponse->accepted);

        $createdOrderId = $orderResponse->sveaOrderId;
        
        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder( Svea\SveaConfig::getDefaultConfig() )
            ->setOrderId( $createdOrderId )
            ->setCountryCode($country)
        ;
                
        $queryResponse = $queryOrderBuilder->queryInvoiceOrder()->doRequest(); 
        
        print_r( $queryResponse);
        $this->assertEquals(1, $queryResponse->accepted);
        // assert that order rows are the same
        $this->assertEquals( $a_quantity, $queryResponse->numberedOrderRows[0]->quantity );
        $this->assertEquals( $a_amountExVat, $queryResponse->numberedOrderRows[0]->amountExVat );
        
        $this->assertEquals( $b_quantity, $queryResponse->numberedOrderRows[1]->quantity );
        $this->assertEquals( $b_amountExVat, $queryResponse->numberedOrderRows[1]->amountExVat );        
        $this->assertEquals( $b_vatPercent, $queryResponse->numberedOrderRows[1]->vatPercent );
        $this->assertEquals( $b_articleNumber, $queryResponse->numberedOrderRows[1]->articleNumber );
        $this->assertEquals( $b_unit, $queryResponse->numberedOrderRows[1]->unit );
        $this->assertStringStartsWith( $b_name, $queryResponse->numberedOrderRows[1]->description );
        $this->assertStringEndsWith( $b_description, $queryResponse->numberedOrderRows[1]->description );
        $this->assertEquals( $b_discount, $queryResponse->numberedOrderRows[1]->discountPercent );

        $this->assertEquals( null, $queryResponse->numberedOrderRows[1]->creditInvoiceId ); // not set
        $this->assertEquals( null, $queryResponse->numberedOrderRows[1]->invoiceId ); // not set
        $this->assertEquals( 2, $queryResponse->numberedOrderRows[1]->rowNumber );  // rows are 1-indexed
        $this->assertEquals( "NotDelivered", $queryResponse->numberedOrderRows[1]->status );

        
    }    
    
    
//        $b_quantity = 1;
//        $b_amountExVat = 100.00;
//        $b_vatPercent = 12;
//        $b_articleNumber = "197a";
//        $b_unit = "pcs.";
//        $b_name = "B Name";
//        $b_description = "B Description";    
//        $b_discount = 50.0;
//        
//        $order = TestUtil::createOrder( TestUtil::createIndividualCustomer($country) )
//            ->addOrderRow( WebPayItem::orderRow()
//                ->setQuantity($a_quantity)
//                ->setAmountExVat($a_amountExVat)
//                ->setVatPercent($a_vatPercent)
//            )
//            ->addOrderRow( WebPayItem::orderRow()
//                ->setQuantity($b_quantity)
//                ->setAmountExVat($b_amountExVat)
//                ->setVatPercent($b_vatPercent)
//                ->setArticleNumber($b_articleNumber)
//                ->setUnit($b_unit)
//                ->setName($b_name)
//                ->setDescription($b_description)
//                ->setDiscountPercent($b_discount)              
//            )    
    
    
}