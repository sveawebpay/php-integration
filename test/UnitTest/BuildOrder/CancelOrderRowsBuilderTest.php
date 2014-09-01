<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CancelOrderRowsBuilderTest extends \PHPUnit_Framework_TestCase {

    protected $cancelOrderRowsObject;
    
    function setUp() {
        $this->cancelOrderRowsObject = new CancelOrderRowsBuilder(SveaConfig::getDefaultConfig());  
    }
    
    public function test_cancelOrderRowsBuilder_class_exists() {     
        $this->assertInstanceOf("Svea\CancelOrderRowsBuilder", $this->cancelOrderRowsObject);
    }
    
    public function test_cancelOrderRowsBuilder_setOrderId() {
        $orderId = "123456";
        $this->cancelOrderRowsObject->setOrderId($orderId);
        $this->assertEquals($orderId, $this->cancelOrderRowsObject->orderId);        
    }
    
    public function test_cancelOrderRowsBuilder_setTransactionId() {
        $orderId = "123456";
        $this->cancelOrderRowsObject->setTransactionId($orderId);
        $this->assertEquals($orderId, $this->cancelOrderRowsObject->orderId);        
    }
    
    public function test_cancelOrderRowsBuilder_setCountryCode() {
        $country = "SE";
        $this->cancelOrderRowsObject->setCountryCode($country);
        $this->assertEquals($country, $this->cancelOrderRowsObject->countryCode);        
    }
    
    public function test_addNumberedOrderRow() {
        $numberedOrderRow = new \Svea\NumberedOrderRow();
        $numberedOrderRow
            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
            ->setQuantity(1)                        // required
            ->setRowNumber(1)
        ;            
                
        $this->cancelOrderRowsObject->addNumberedOrderRow( $numberedOrderRow );
        $this->assertInternalType('array', $this->cancelOrderRowsObject->numberedOrderRows);     
    }
    
    public function test_addNumberedOrderRows() {
        $numberedOrderRow1 = new \Svea\NumberedOrderRow();
        $numberedOrderRow1
            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
            ->setQuantity(1)                        // required
            ->setRowNumber(1)
        ;   
        $numberedOrderRow2 = new \Svea\NumberedOrderRow();
        $numberedOrderRow2
            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
            ->setQuantity(1)                        // required
            ->setRowNumber(2)
        ;  
                
        $this->cancelOrderRowsObject->addNumberedOrderRows( array( $numberedOrderRow1, $numberedOrderRow2 ) );
        $this->assertInternalType('array', $this->cancelOrderRowsObject->numberedOrderRows);     
    }
    
     public function test_cancelOrderRowsBuilder_cancelInvoiceOrderRowsBuilder_returns_CancelOrderRowsRequest() {
        $orderId = "123456";
        $cancelOrderRowsObject = $this->cancelOrderRowsObject->setOrderId($orderId)->cancelInvoiceOrderRows();
        
        $this->assertInstanceOf("Svea\AdminService\CancelOrderRowsRequest", $cancelOrderRowsObject);
    }
    
    public function test_cancelOrderRowsBuilder_cancelPaymentPlanOrderRowsBuilder_returns_CancelOrderRowsRequest() {
        $orderId = "123456";  
        $cancelOrderRowsObject = $this->cancelOrderRowsObject->setOrderId($orderId)->cancelPaymentPlanOrderRows();
        
        $this->assertInstanceOf("Svea\AdminService\CancelOrderRowsRequest", $cancelOrderRowsObject);
    }
    
    public function test_cancelOrderRowsBuilder_cancelCardOrderRowsBuilder_returns_LowerTransaction() {
        $orderId = "123456";  
        $mockedNumberedOrderRow = new \Svea\NumberedOrderRow();
        $mockedNumberedOrderRow
            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
            ->setQuantity(1)                        // required
            ->setRowNumber(1)
        ;            
        
        $cancelOrderRowsObject = $this->cancelOrderRowsObject
                ->setOrderId($orderId)
                ->addNumberedOrderRow( $mockedNumberedOrderRow )
                ->setRowToCancel(1)
        ;
        
        $request = $cancelOrderRowsObject->cancelCardOrderRows();
        
        $this->assertInstanceOf("Svea\HostedService\LowerTransaction", $request);
    }
}
