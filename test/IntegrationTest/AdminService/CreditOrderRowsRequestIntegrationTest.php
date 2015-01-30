<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class CreditOrderRowsRequestIntegrationTest extends PHPUnit_Framework_TestCase {

    public function test_creditOrderRows_creditInvoiceOrderRows_credit_row_using_row_index() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
                ->addOrderRow(
                        WebPayItem::orderRow()
                        ->setAmountExVat(99.99) // => 123.9876 inc
                        ->setVatPercent(24)
                        ->setQuantity(1)
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
        $this->assertEquals("123.99", $deliver->amount);
        //print_r($deliver->invoiceId);

        $credit = WebPayAdmin::creditOrderRows($config)
                ->setInvoiceId($deliver->invoiceId)
                ->setInvoiceDistributionType(DistributionType::POST)
                ->setCountryCode('SE')
                ->setRowToCredit(1)
                ->creditInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $credit->accepted); 
        //print_r($credit);
    } 

    public function test_creditOrderRows_creditInvoiceOrderRows_credit_row_using_new_order_row_original_exvat_new_exvat() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
                ->addOrderRow(
                        WebPayItem::orderRow()
                        ->setAmountExVat(99.99) // => 123.9876 inc
                        ->setVatPercent(24)
                        ->setQuantity(1)
                )
                ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
                ->setCountryCode("SE")
                ->setOrderDate("2012-12-12")
                ->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        // query order and assert row totals
        $query = WebPayAdmin::queryOrder($config)
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode('SE')
                ->queryInvoiceOrder()->doRequest();       
        $this->assertEquals(1, $query->accepted);                
        $this->assertEquals("99.99", $query->numberedOrderRows[0]->amountExVat);
        $this->assertEquals("24", $query->numberedOrderRows[0]->vatPercent);            
        
        $deliver = WebPayAdmin::deliverOrderRows($config)
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode('SE')
                ->setInvoiceDistributionType(DistributionType::POST)
                ->setRowToDeliver(1)
                ->deliverInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $deliver->accepted); 
        $this->assertEquals("123.99", $deliver->amount);
        //print_r($deliver->invoiceId);

        $credit = WebPayAdmin::creditOrderRows($config)
                ->setInvoiceId($deliver->invoiceId)
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
                ->setOrderId($orderResponse->sveaOrderId)
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
        $orderResponse = WebPay::createOrder($config)
                ->addOrderRow(
                        WebPayItem::orderRow()
                        ->setAmountExVat(99.99) // => 123.9876 inc
                        ->setVatPercent(24)
                        ->setQuantity(1)
                )
                ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
                ->setCountryCode("SE")
                ->setOrderDate("2012-12-12")
                ->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        // query order and assert row totals
        $query = WebPayAdmin::queryOrder($config)
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode('SE')
                ->queryInvoiceOrder()->doRequest();       
        $this->assertEquals(1, $query->accepted);                
        $this->assertEquals("99.99", $query->numberedOrderRows[0]->amountExVat);
        $this->assertEquals("24", $query->numberedOrderRows[0]->vatPercent);            
        
        $deliver = WebPayAdmin::deliverOrderRows($config)
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode('SE')
                ->setInvoiceDistributionType(DistributionType::POST)
                ->setRowToDeliver(1)
                ->deliverInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $deliver->accepted); 
        $this->assertEquals("123.99", $deliver->amount);
        //print_r($deliver->invoiceId);

        $credit = WebPayAdmin::creditOrderRows($config)
                ->setInvoiceId($deliver->invoiceId)
                ->setInvoiceDistributionType(DistributionType::POST)
                ->setCountryCode('SE')
                ->setRowToCredit(1)
                ->creditInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $credit->accepted); 
        //print_r($credit);
   
        // query order and assert row totals
        $query = WebPayAdmin::queryOrder($config)
                ->setOrderId($orderResponse->sveaOrderId)
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
    
}