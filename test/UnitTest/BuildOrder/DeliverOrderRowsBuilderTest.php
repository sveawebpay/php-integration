<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class DeliverOrderRowsBuilderTest extends \PHPUnit_Framework_TestCase {

    protected $deliverOrderRowsObject;
    
    function setUp() {
        $this->deliverOrderRowsObject = new deliverOrderRowsBuilder(SveaConfig::getDefaultConfig());  
    }
    
    public function test_deliverOrderRowsBuilder_class_exists() {     
        $this->assertInstanceOf("Svea\deliverOrderRowsBuilder", $this->deliverOrderRowsObject);
    }
    
    public function test_deliverOrderRowsBuilder_setOrderId() {
        $orderId = "123456";
        $this->deliverOrderRowsObject->setOrderId($orderId);
        $this->assertEquals($orderId, $this->deliverOrderRowsObject->orderId);        
    }

//    public function test_deliverOrderRowsBuilder_setTransactionId() {
//        $orderId = "123456";
//        $this->deliverOrderRowsObject->setTransactionId($orderId);
//        $this->assertEquals($orderId, $this->deliverOrderRowsObject->orderId);        
//    }  
//    
//    public function test_deliverOrderRowsBuilder_setInvoiceId() {
//        $orderId = "123456";
//        $this->deliverOrderRowsObject->setInvoiceId($orderId);
//        $this->assertEquals($orderId, $this->deliverOrderRowsObject->invoiceId);        
//    }
//    public function test_deliverOrderRowsBuilder_setCountryCode() {
//        $country = "SE";
//        $this->deliverOrderRowsObject->setCountryCode($country);
//        $this->assertEquals($country, $this->deliverOrderRowsObject->countryCode);        
//    }
//    
//    public function test_deliverOrderRowsBuilder_setInvoiceDistributionType() {
//        $distributionType = \DistributionType::POST;
//        $this->deliverOrderRowsObject->setInvoiceDistributionType($distributionType);
//        $this->assertEquals($distributionType, $this->deliverOrderRowsObject->distributionType);        
//    }
//    
//    public function test_addNumberedOrderRow() {
//        $numberedOrderRow = new \Svea\NumberedOrderRow();
//        $numberedOrderRow
//            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
//            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
//            ->setQuantity(1)                        // required
//            ->setRowNumber(1)
//        ;            
//                
//        $this->deliverOrderRowsObject->addNumberedOrderRow( $numberedOrderRow );
//        $this->assertInternalType('array', $this->deliverOrderRowsObject->numberedOrderRows);     
//    }
//    
//    public function test_addNumberedOrderRows() {
//        $numberedOrderRow1 = new \Svea\NumberedOrderRow();
//        $numberedOrderRow1
//            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
//            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
//            ->setQuantity(1)                        // required
//            ->setRowNumber(1)
//        ;   
//        $numberedOrderRow2 = new \Svea\NumberedOrderRow();
//        $numberedOrderRow2
//            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
//            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
//            ->setQuantity(1)                        // required
//            ->setRowNumber(2)
//        ;  
//                
//        $this->deliverOrderRowsObject->addNumberedOrderRows( array( $numberedOrderRow1, $numberedOrderRow2 ) );
//        $this->assertInternalType('array', $this->deliverOrderRowsObject->numberedOrderRows);     
//    }
//    
//    public function test_deliverOrderRowsBuilder_deliverInvoiceOrderRowsBuilder_returns_deliverOrderRowsRequest() {
//        $orderId = "123456";
//        $deliverOrderRowsObject = $this->deliverOrderRowsObject->setOrderId($orderId)->deliverInvoiceOrderRows();
//        
//        $this->assertInstanceOf("Svea\AdminService\deliverOrderRowsRequest", $deliverOrderRowsObject);
//    }
//    
//    public function test_deliverOrderRowsBuilder_deliverCardOrderRowsBuilder_returns_LowerTransaction() {
//        $orderId = "123456";  
//        $mockedNumberedOrderRow = new \Svea\NumberedOrderRow();
//        $mockedNumberedOrderRow
//            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
//            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
//            ->setQuantity(1)                        // required
//            ->setRowNumber(1)
//        ;            
//        
//        $deliverOrderRowsObject = $this->deliverOrderRowsObject
//                ->setOrderId($orderId)
//                ->addNumberedOrderRow( $mockedNumberedOrderRow )
//                ->setRowTodeliver(1)
//        ;
//        
//        $request = $deliverOrderRowsObject->deliverCardOrderRows();
//        
//        $this->assertInstanceOf("Svea\HostedService\deliverTransaction", $request);
//    }
//    
//    public function test_deliverOrderRowsBuilder_deliverDirectBankOrderRowsBuilder_returns_LowerTransaction() {
//        $orderId = "123456";  
//        $mockedNumberedOrderRow = new \Svea\NumberedOrderRow();
//        $mockedNumberedOrderRow
//            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
//            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
//            ->setQuantity(1)                        // required
//            ->setRowNumber(1)
//        ;            
//        
//        $deliverOrderRowsObject = $this->deliverOrderRowsObject
//                ->setOrderId($orderId)
//                ->addNumberedOrderRow( $mockedNumberedOrderRow )
//                ->setRowTodeliver(1)
//        ;
//        
//        $request = $deliverOrderRowsObject->deliverDirectBankOrderRows();
//        
//        $this->assertInstanceOf("Svea\HostedService\deliverTransaction", $request);
//    }
//    
//    /// validations
//      
//    public function test_validatedeliverCardOrder_missing_setOrderId_throws_ValidationException() {
//        
//        $this->setExpectedException(
//          '\Svea\ValidationException', 'orderId is required for deliverCardOrderRows(). Use method setOrderId().'
//        );
//        
//        $orderId = "123456";  
//        $mockedNumberedOrderRow = new \Svea\NumberedOrderRow();
//        $mockedNumberedOrderRow
//            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
//            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
//            ->setQuantity(1)                        // required
//            ->setRowNumber(1)
//        ;            
//        
//        $deliverOrderRowsObject = $this->deliverOrderRowsObject
//                //->setOrderId($orderId)
//                ->addNumberedOrderRow( $mockedNumberedOrderRow )
//                ->setRowTodeliver(1)
//        ;
//        
//        $request = $deliverOrderRowsObject->deliverCardOrderRows();
//        
//        $this->assertInstanceOf("Svea\HostedService\deliverTransaction", $request);
//    }
//     
//    public function test_validatedeliverCardOrder_missing_setRowTodeliver_and_adddeliverRow_throws_ValidationException() {
//        
//        $this->setExpectedException(
//          '\Svea\ValidationException', 'at least one of rowsTodeliver or deliverOrderRows must be set. Use setRowTodeliver() or adddeliverOrderRow().'
//        );
//        
//        $orderId = "123456";  
//        $mockedNumberedOrderRow = new \Svea\NumberedOrderRow();
//        $mockedNumberedOrderRow
//            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
//            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
//            ->setQuantity(1)                        // required
//            ->setRowNumber(1)
//        ;            
//        
//        $deliverOrderRowsObject = $this->deliverOrderRowsObject
//                ->setOrderId($orderId)
//                ->addNumberedOrderRow( $mockedNumberedOrderRow )
//        ;
//        
//        $request = $deliverOrderRowsObject->deliverCardOrderRows();
//        
//        $this->assertInstanceOf("Svea\HostedService\deliverTransaction", $request);
//    }    
//    
//    public function test_validatedeliverCardOrder_with_too_few_NumberedOrderRows_throws_ValidationException() {
//        
//        $this->setExpectedException(
//          '\Svea\ValidationException', 'every entry in rowsTodeliver must have a corresponding numberedOrderRows. Use setRowsTodeliver() and addNumberedOrderRow().'
//        );
//        
//        $orderId = "123456";  
//        $mockedNumberedOrderRow = new \Svea\NumberedOrderRow();
//        $mockedNumberedOrderRow
//            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
//            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
//            ->setQuantity(1)                        // required
//            ->setRowNumber(1)
//        ;            
//        
//        $deliverOrderRowsObject = $this->deliverOrderRowsObject
//                ->setOrderId($orderId)
//                ->addNumberedOrderRow( $mockedNumberedOrderRow )
//                ->setRowsTodeliver(array(1,2))
//        ;
//        
//        $request = $deliverOrderRowsObject->deliverCardOrderRows();
//        
//        $this->assertInstanceOf("Svea\HostedService\deliverTransaction", $request);
//    }
//    
//    public function test_validatedeliverCardOrder_with_mismatched_NumberedOrderRows_throws_ValidationException() {
//        
//        $this->setExpectedException(
//          '\Svea\ValidationException', 'every entry in rowsTodeliver must match a numberedOrderRows. Use setRowsTodeliver() and addNumberedOrderRow().'
//        );
//        
//        $orderId = "123456";  
//        $mockedNumberedOrderRow = new \Svea\NumberedOrderRow();
//        $mockedNumberedOrderRow
//            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
//            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
//            ->setQuantity(1)                        // required
//            ->setRowNumber(1)
//        ;            
//        
//        $deliverOrderRowsObject = $this->deliverOrderRowsObject
//                ->setOrderId($orderId)
//                ->addNumberedOrderRow( $mockedNumberedOrderRow )
//                ->setRowTodeliver(9)
//        ;
//        
//        $request = $deliverOrderRowsObject->deliverCardOrderRows();        
//        
//        $this->assertInstanceOf("Svea\HostedService\deliverTransaction", $request);
//    }    
       
}
