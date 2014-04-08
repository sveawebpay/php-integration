<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class OrderBuilderIntegrationTest extends PHPUnit_Framework_TestCase {

    public function test_CreateOrderBuilder_Invoice_Accepted() {
        $order = TestUtil::createOrder();
        $response = $order->useInvoicePayment()->doRequest();
       
        $this->assertEquals(1, $response->accepted);
        
//        $orderId= $response->sveaOrderId;
//
//        print_r( $response);
//        print_r( $orderId);
//        
//        $cancelRequest = WebPay::cancelOrder($config)->setOrderId($orderId)->usePaymentMethod(\PaymentMethod::INVOICE)->doRequest();
//        
//        print_r( $cancelRequest);
                
    }
    
    public function test_CreateOrderBuilder_Invoice_Denied() {
        $order = TestUtil::createOrder();
        $response = $order->useInvoicePayment()->doRequest();
       
        $this->assertEquals(1, $response->accepted);
        
//        $orderId= $response->sveaOrderId;
//
//        print_r( $response);
//        print_r( $orderId);
//        
//        $cancelRequest = WebPay::cancelOrder($config)->setOrderId($orderId)->usePaymentMethod(\PaymentMethod::INVOICE)->doRequest();
//        
//        print_r( $cancelRequest);
                
    }
    
    

    public function __testInvoiceRequestDenied() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::createOrder($config)
                ->addOrderRow(TestUtil::createOrderRow())
                ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(4606082222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()
                ->doRequest();

        $this->assertEquals(0, $request->accepted);
    }

}