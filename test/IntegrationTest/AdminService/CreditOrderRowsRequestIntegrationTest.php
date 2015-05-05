<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/** helper class, used to return information about an order */
class orderToCredit {
    var $orderId;
    var $invoiceId;

    function orderToCredit( $orderId, $invoiceId ) {
        $this->orderId = $orderId;
        $this->invoiceId = $invoiceId;
    }
}

/**
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class CreditOrderRowsRequestIntegrationTest extends PHPUnit_Framework_TestCase {

    /** helper function, returns invoice for delivered order with one row, sent with PriceIncludingVat flag set to true */
    public function get_orderInfo_sent_inc_vat( $amount, $vat, $quantity ) {
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
                ->addOrderRow(
                        WebPayItem::orderRow()
                        ->setAmountIncVat($amount)
                        ->setVatPercent($vat)
                        ->setQuantity($quantity)
                )
                ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
                ->setCountryCode("SE")
                ->setOrderDate("2012-12-12")
                ->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);
        
        $deliver = WebPayAdmin::deliverOrderRows($config)
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode('SE')
                ->setInvoiceDistributionType(DistributionType::POST)
                ->setRowToDeliver(1)
                ->deliverInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $deliver->accepted); 
        
        return new orderToCredit( $orderResponse->sveaOrderId, $deliver->invoiceId );
    }     
 
    /** helper function, returns invoice for delivered order with one row, sent with PriceIncludingVat flag set to false */
    public function get_orderInfo_sent_ex_vat( $amount, $vat, $quantity ) {
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
                ->addOrderRow(
                        WebPayItem::orderRow()
                        ->setAmountExVat($amount)
                        ->setVatPercent($vat)
                        ->setQuantity($quantity)
                )
                ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
                ->setCountryCode("SE")
                ->setOrderDate("2012-12-12")
                ->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);
        
        $deliver = WebPayAdmin::deliverOrderRows($config)
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode('SE')
                ->setInvoiceDistributionType(DistributionType::POST)
                ->setRowToDeliver(1)
                ->deliverInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $deliver->accepted); 
        
        return new orderToCredit( $orderResponse->sveaOrderId, $deliver->invoiceId );
    } 
    
    
    public function test_creditOrderRows_creditInvoiceOrderRows_credit_row_using_row_index() {
        $config = Svea\SveaConfig::getDefaultConfig();

        $orderInfo = $this->get_orderInfo_sent_ex_vat( 99.99, 24, 1 );

        $credit = WebPayAdmin::creditOrderRows($config)
                ->setInvoiceId($orderInfo->invoiceId)
                ->setInvoiceDistributionType(DistributionType::POST)
                ->setCountryCode('SE')
                ->setRowToCredit(1)
                ->creditInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $credit->accepted); 
        //print_r($credit);
    } 

    public function test_creditOrderRows_creditInvoiceOrderRows_credit_row_using_new_order_row_original_exvat_new_exvat() {
        $config = Svea\SveaConfig::getDefaultConfig();
        
        $orderInfo = $this->get_orderInfo_sent_ex_vat( 99.99, 24, 1 );

        // query order and assert row totals
        $query = WebPayAdmin::queryOrder($config)
                ->setOrderId($orderInfo->orderId)
                ->setCountryCode('SE')
                ->queryInvoiceOrder()->doRequest();       
        $this->assertEquals(1, $query->accepted);                
        $this->assertEquals("99.99", $query->numberedOrderRows[0]->amountExVat);
        $this->assertEquals("24", $query->numberedOrderRows[0]->vatPercent);            
        
        $credit = WebPayAdmin::creditOrderRows($config)
                ->setInvoiceId($orderInfo->invoiceId)
                ->setInvoiceDistributionType(DistributionType::POST)
                ->setCountryCode('SE')
                ->addCreditOrderRow(
                        WebPayItem::orderRow()
                        ->setAmountExVat(99.99) // => 123.9876 inc
                        ->setVatPercent(24)
                        ->setQuantity(1)
                )
                ->creditInvoiceOrderRows()->doRequest();
        //print_r($credit);
        $this->assertEquals(1, $credit->accepted); 
   
        // query order and assert row totals
        $query = WebPayAdmin::queryOrder($config)
                ->setOrderId($orderInfo->orderId)
                ->setCountryCode('SE')
                ->queryInvoiceOrder()->doRequest();       
        $this->assertEquals(1, $query->accepted);   
        // NOTE the order row status/amount does not reflect that the corresponding invoice row has been credited
        // TODO implement queryInvoice and recurse invoices to get the current order row status
        $this->assertEquals("99.99", $query->numberedOrderRows[0]->amountExVat);   // sent 99.99 ex * 1.24 => sent 123.9876 inc => 123.99 queried
        $this->assertEquals("24", $query->numberedOrderRows[0]->vatPercent);
    }
    
    public function test_creditOrderRows_creditInvoiceOrderRows_credit_row_using_original_exvat_new_order_incvat() {
        $config = Svea\SveaConfig::getDefaultConfig();
        
        $orderInfo = $this->get_orderInfo_sent_ex_vat( 99.99, 24, 1 );

        // query order and assert row totals
        $query = WebPayAdmin::queryOrder($config)
                ->setOrderId($orderInfo->orderId)
                ->setCountryCode('SE')
                ->queryInvoiceOrder()->doRequest();       
        $this->assertEquals(1, $query->accepted);                
        $this->assertEquals("99.99", $query->numberedOrderRows[0]->amountExVat);
        $this->assertEquals("24", $query->numberedOrderRows[0]->vatPercent);            

        $credit = WebPayAdmin::creditOrderRows($config)
                ->setInvoiceId($orderInfo->invoiceId)
                ->setInvoiceDistributionType(DistributionType::POST)
                ->setCountryCode('SE')
                ->setRowToCredit(1)
                ->creditInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $credit->accepted); 
        //print_r($credit);
   
        // query order and assert row totals
        $query = WebPayAdmin::queryOrder($config)
                ->setOrderId($orderInfo->orderId)
                ->setCountryCode('SE')
                ->queryInvoiceOrder()->doRequest();       
        $this->assertEquals(1, $query->accepted);   
        $this->assertEquals("99.99", $query->numberedOrderRows[0]->amountExVat);   // sent 99.99 ex * 1.24 => sent 123.9876 inc => 123.99 queried
        $this->assertEquals("24", $query->numberedOrderRows[0]->vatPercent);
    }
           
    public function test_add_single_orderRow_type_missmatch_3() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
                ->addOrderRow(
                        WebPayItem::orderRow()
                        ->setAmountExVat(99.99)
                        ->setAmountIncVat(123.9876)
                        ->setQuantity(1)
                )
                ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
                ->setCountryCode("SE")
                ->setCurrency("SEK")
                ->setOrderDate("2012-12-12")
                ->useInvoicePayment()
                ->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $response = WebPayAdmin::updateOrderRows($config)
                ->setCountryCode('SE')
                ->setOrderId($orderResponse->sveaOrderId)
                ->updateOrderRow(WebPayItem::numberedOrderRow()
                        ->setRowNumber(1)
                        ->setAmountExVat(99.99)
                        ->setVatPercent(24)
                        ->setQuantity(1)
                )
                ->updateInvoiceOrderRows()
                ->doRequest();

        $this->assertEquals(1, $response->accepted);
    }

    public function test_add_single_orderRow_type_mismatch_created_inc_updated_ex() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
                ->addOrderRow(
                        WebPayItem::orderRow()
                        ->setAmountIncVat(123.9876)
                        ->setVatPercent(24)
                        ->setQuantity(1)
                )
                ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
                ->setCountryCode("SE")
                ->setCurrency("SEK")
                ->setOrderDate("2012-12-12")
                ->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        // query order and assert row totals
        $query = WebPayAdmin::queryOrder($config)
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode('SE')
                ->queryInvoiceOrder()->doRequest();       
        $this->assertEquals(1, $query->accepted);                
        $this->assertEquals("123.99", $query->numberedOrderRows[0]->amountIncVat);  // sent 123.9876 inc => 123.99 queried
        $this->assertEquals("24", $query->numberedOrderRows[0]->vatPercent);            
        
        $response = WebPayAdmin::updateOrderRows($config)
                ->setCountryCode('SE')
                ->setOrderId($orderResponse->sveaOrderId)
                ->updateOrderRow(WebPayItem::numberedOrderRow()
                        ->setRowNumber(1)
                        ->setAmountExVat(99.99)
                        ->setVatPercent(24)
                        ->setQuantity(1)
                )
                ->updateInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $response->accepted);        

        // query order and assert row totals
        $query2 = WebPayAdmin::queryOrder($config)
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode('SE')
                ->queryInvoiceOrder()->doRequest();       
        $this->assertEquals(1, $query2->accepted);                
        $this->assertEquals("123.99", $query2->numberedOrderRows[0]->amountIncVat);   // sent 99.99 ex * 1.24 => sent 123.9876 inc => 123.99 queried
        $this->assertEquals("24", $query2->numberedOrderRows[0]->vatPercent);
        //print_r($orderResponse->sveaOrderId);
    }   
    
    
   
    /// characterizing tests for INTG-551
    function test_creditOrderRows_handles_creditOrderRows_specified_using_exvat_and_vatpercent() {       
        // needs either setRow(s)ToCredit or addCreditOrderRow(s)    
        $creditOrder = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
            ->setInvoiceId("123456789")                
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode("SE")            
            ->addCreditOrderRow( 
                    WebPayItem::orderRow()
                        ->setAmountExVat(10.00)
                        ->setVatPercent(25)
                        ->setQuantity(1)
            )
        ;
        $request = $creditOrder->creditInvoiceOrderRows()->prepareRequest();
     
        $this->assertEquals("10", $request->NewCreditInvoiceRows->enc_value[0]->enc_value->PricePerUnit->enc_value);
        $this->assertEquals("25", $request->NewCreditInvoiceRows->enc_value[0]->enc_value->VatPercent->enc_value);
        $this->assertEquals(null, $request->NewCreditInvoiceRows->enc_value[0]->enc_value->PriceIncludingVat->enc_value);       
    }
    
    function test_creditOrderRows_handles_creditOrderRows_specified_using_incvat_and_vatpercent() {       
        // needs either setRow(s)ToCredit or addCreditOrderRow(s)    
        $creditOrder = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
            ->setInvoiceId("123456789")                
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode("SE")            
            ->addCreditOrderRow( 
                    WebPayItem::orderRow()
                        ->setAmountIncVat(10.00)
                        ->setVatPercent(25)
                        ->setQuantity(1)
            )
        ;
        $request = $creditOrder->creditInvoiceOrderRows()->prepareRequest();
     
        $this->assertEquals("10", $request->NewCreditInvoiceRows->enc_value[0]->enc_value->PricePerUnit->enc_value);
        $this->assertEquals("25", $request->NewCreditInvoiceRows->enc_value[0]->enc_value->VatPercent->enc_value);
        $this->assertEquals(true, $request->NewCreditInvoiceRows->enc_value[0]->enc_value->PriceIncludingVat->enc_value);    
    }    
    
    function test_creditOrderRows_handles_creditOrderRows_specified_using_incvat_and_exvat() {       
        // needs either setRow(s)ToCredit or addCreditOrderRow(s)    
        $creditOrder = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
            ->setInvoiceId("123456789")                
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode("SE")            
            ->addCreditOrderRow( 
                    WebPayItem::orderRow()
                        ->setAmountIncVat(12.50)
                        ->setAmountExVat(10.00)
                        ->setQuantity(1)
            )
        ;
        $request = $creditOrder->creditInvoiceOrderRows()->prepareRequest();

    $this->assertEquals("12.50", $request->NewCreditInvoiceRows->enc_value[0]->enc_value->PricePerUnit->enc_value);
    $this->assertEquals("25", $request->NewCreditInvoiceRows->enc_value[0]->enc_value->VatPercent->enc_value);
    $this->assertEquals("1", $request->NewCreditInvoiceRows->enc_value[0]->enc_value->PriceIncludingVat->enc_value);    
    }
    
    
    
}