<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class UpdateOrderRowsBuilderIntegrationTest extends PHPUnit_Framework_TestCase {
    
    protected $invoiceIdToTest;
    protected $country;

    protected function setUp()
    {
        $this->country = "SE";
        $this->invoiceIdToTest = 583004;   // set this to the approved invoice set up by test_manual_setup_CreditOrderRows_testdata()
    }       

    function test_UpdateOrderRows_updateInvoiceOrderRows_single_row_success() {
        $country = "SE";

        // create order
        $order = TestUtil::createOrderWithoutOrderRows( TestUtil::createIndividualCustomer($country) );
        $order->addOrderRow( WebPayItem::orderRow()
            ->setArticleNumber("1")
            ->setQuantity( 1 )
            ->setAmountExVat( 1.00 )
            ->setVatPercent(25)
            ->setDescription("A Specification")
            ->setName('A Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );      
        $order->addOrderRow( WebPayItem::orderRow()
            ->setArticleNumber("2")
            ->setQuantity( 1 )
            ->setAmountExVat( 2.00 )
            ->setVatPercent(25)
            ->setDescription("B Specification")
            ->setName('B Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );         $orderResponse = $order->useInvoicePayment()->doRequest();       
        $this->assertEquals(1, $orderResponse->accepted);

//        // query order
//        $query = WebPayAdmin::queryOrder( Svea\SveaConfig::getDefaultConfig() );
//        $query->setCountryCode($country)->setOrderId($orderResponse->sveaOrderId);
//        $queryResponse = $query->queryInvoiceOrder()->doRequest();
//        
//        //print_r($queryResponse);
//        $this->assertEquals(1, $queryResponse->accepted);

        // update all attributes for a numbered orderRow   
        $updateOrderRowsResponse = WebPayAdmin::updateOrderRows( Svea\SveaConfig::getDefaultConfig() )
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode($country)
                ->updateOrderRow( WebPayItem::numberedOrderRow()
                    ->setArticleNumber("10")
                    ->setQuantity( 1 )
                    ->setAmountExVat( 10.00 )
                    ->setVatPercent(26)
                    ->setDescription("K Specification")
                    ->setName('K Name')
                    ->setUnit("st")
                    ->setDiscountPercent(1)
//                    ->setCreditInvoiceId()
//                    ->setInvoiceId()
                    ->setRowNumber(1)
                    ->setStatus(Svea\NumberedOrderRow::ORDERROWSTATUS_NOTDELIVERED)
                )    
                ->updateInvoiceOrderRows()
                    ->doRequest();
        
//        print_r( $updateOrderRowsResponse );
//        print_r("test_UpdateOrderRows_updateInvoiceOrderRows_single_row_success: "); print_r( $orderResponse->sveaOrderId );
        $this->assertEquals(1, $updateOrderRowsResponse->accepted);
        // todo query result & check amounts, description automatically        
    }
    
    // update multiple invoice order rows    
    // update payment plan order row(s)
    // update card order rows 
    // update direct bank order rows - should fail
    // update card > original order - should fail
    
}


?>