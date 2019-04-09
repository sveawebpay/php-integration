<?php

namespace Svea\WebPay\Test\UnitTest;

use Exception;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\DistributionType;
use Svea\WebPay\Config\ConfigurationProvider;


/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class WebPayAdminUnitTest extends \PHPUnit\Framework\TestCase
{

    public function test_WebPayAdmin_class_exists()
    {
        $adminObject = new WebPayAdmin();
        $this->assertInstanceOf("Svea\WebPay\WebPayAdmin", $adminObject);
    }

    // Svea\WebPay\WebPayAdmin::cancelOrder() ----------------------------------------------
    public function test_cancelOrder_returns_CancelOrderBuilder()
    {
        $builderObject = WebPayAdmin::cancelOrder(ConfigurationService::getDefaultConfig());
        $this->assertInstanceOf("Svea\WebPay\BuildOrder\CancelOrderBuilder", $builderObject);
    }
    // TODO add validation unit tests


    // Svea\WebPay\WebPayAdmin::cancelOrder() -------------------------------------------------------------------------------------	
    // returned request class
    public function test_cancelOrder_cancelInvoiceOrder_returns_CloseOrder()
    {
        $cancelOrder = WebPayAdmin::cancelOrder(ConfigurationService::getDefaultConfig());
        $request = $cancelOrder->cancelInvoiceOrder();
        $this->assertInstanceOf("Svea\WebPay\WebService\HandleOrder\CloseOrder", $request);
        $this->assertEquals(ConfigurationProvider::INVOICE_TYPE, $request->orderBuilder->orderType);
    }

    public function test_cancelOrder_cancelPaymentPlanOrder_returns_CloseOrder()
    {
        $cancelOrder = WebPayAdmin::cancelOrder(ConfigurationService::getDefaultConfig());
        $request = $cancelOrder->cancelPaymentPlanOrder();
        $this->assertInstanceOf("Svea\WebPay\WebService\HandleOrder\CloseOrder", $request);
        $this->assertEquals(ConfigurationProvider::PAYMENTPLAN_TYPE, $request->orderBuilder->orderType);
    }

    public function test_cancelOrder_cancelCardOrder_returns_AnnulTransaction()
    {
        $cancelOrder = WebPayAdmin::cancelOrder(ConfigurationService::getDefaultConfig());
        $request = $cancelOrder->cancelCardOrder();
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedAdminRequest\AnnulTransaction", $request);
    }
    /// validators
    // invoice
//    public void test_validates_all_required_methods_for_cancelOrder_cancelInvoiceOrder() {
//    public void test_missing_required_method_for_cancelOrder_cancelInvoiceOrder_setOrderId() {
//    public void test_missing_required_method_for_cancelOrder_cancelInvoiceOrder_setCountryCode() {
//    public void test_validates_all_required_methods_for_cancelOrder_cancelPaymentPlanOrder() {
//    public void test_missing_required_method_for_cancelOrder_cancelPaymentPlanOrder_setOrderId() {
//    public void test_missing_required_method_for_cancelOrder_cancelPaymentPlanOrder_setCountryCode() {
    // card
    function test_validates_all_required_methods_for_cancelOrder_cancelCardOrder()
    {
        $request = WebPayAdmin::cancelOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId("123456789")
            ->setCountryCode("SE");
        try {
            $request->cancelCardOrder()->prepareRequest();
            $this->assertTrue(true);
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : transactionId is required. Use function setTransactionId() with the SveaOrderId from the createOrder response
     */
    function test_missing_required_method_for_cancelOrder_cancelCardOrder_setOrderId()
    {
        $request = WebPayAdmin::cancelOrder(ConfigurationService::getDefaultConfig())
            //->setOrderId("123456789")                
            ->setCountryCode("SE");
        $request->cancelCardOrder()->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : CountryCode is required. Use function setCountryCode().
     */
    function test_missing_required_method_for_cancelOrder_cancelCardOrder_setCountryCode()
    {
        $request = WebPayAdmin::cancelOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId("123456789")//->setCountryCode("SE")            
        ;
        $request->cancelCardOrder()->prepareRequest();
    }

    // direct bank
    public function test_queryOrder_queryInvoiceOrder_returns_GetOrdersRequest()
    {
        $queryOrder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig());
        $request = $queryOrder->queryInvoiceOrder();
        $this->assertInstanceOf("Svea\WebPay\AdminService\GetOrdersRequest", $request);
        $this->assertEquals(ConfigurationProvider::INVOICE_TYPE, $request->orderBuilder->orderType);
    }

    public function test_queryOrder_queryPaymentPlanOrder_returns_GetOrdersRequest()
    {
        $queryOrder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig());
        $request = $queryOrder->queryPaymentPlanOrder();
        $this->assertInstanceOf("Svea\WebPay\AdminService\GetOrdersRequest", $request);
        $this->assertEquals(ConfigurationProvider::PAYMENTPLAN_TYPE, $request->orderBuilder->orderType);
    }

    public function test_queryOrder_queryCardOrder_returns_QueryTransaction()
    {
        $queryOrder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig());
        $request = $queryOrder->queryCardOrder();
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedAdminRequest\QueryTransaction", $request);
    }

    public function test_queryOrder_queryCardOrder_returns_QueryTransactionByCustomerRefNo()
    {
        $queryOrder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setClientOrderNumber("123");
        $request = $queryOrder->queryCardOrder();
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedAdminRequest\QueryTransactionByCustomerRefNo", $request);
    }

    public function test_queryOrder_queryDirectBankOrder_returns_QueryTransaction()
    {
        $queryOrder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig());
        $request = $queryOrder->queryDirectBankOrder();
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedAdminRequest\QueryTransaction", $request);
    }

    public function test_queryOrder_queryDirectBankOrder_returns_QueryTransactionByCustomerRefNo()
    {
        $queryOrder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setClientOrderNumber("123");
        $request = $queryOrder->queryDirectBankOrder();
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedAdminRequest\QueryTransactionByCustomerRefNo", $request);
    }

// TODO add validation unit tests

    // Svea\WebPay\WebPayAdmin::cancelOrderRows() ------------------------------------------
    public function test_cancelOrderRows_returns_AddOrderRowsBuilder()
    {
        $builderObject = WebPayAdmin::cancelOrderRows(ConfigurationService::getDefaultConfig());
        $this->assertInstanceOf("Svea\WebPay\BuildOrder\CancelOrderRowsBuilder", $builderObject);
    }

    // invoice
    public function test_cancelOrderRows_cancelInvoiceOrderRows_returns_CancelOrderRowsRequest()
    {
        $cancelOrderRowsBuilder = WebPayAdmin::cancelOrderRows(ConfigurationService::getDefaultConfig());
        $request = $cancelOrderRowsBuilder->cancelInvoiceOrderRows();
        $this->assertInstanceOf("Svea\WebPay\AdminService\CancelOrderRowsRequest", $request);
    }

    // partpayment
    public function test_cancelOrderRows_cancelPaymentPlanOrderRows_returns_CancelOrderRowsRequest()
    {
        $cancelOrderRowsBuilder = WebPayAdmin::cancelOrderRows(ConfigurationService::getDefaultConfig());
        $request = $cancelOrderRowsBuilder->cancelPaymentPlanOrderRows();
        $this->assertInstanceOf("Svea\WebPay\AdminService\CancelOrderRowsRequest", $request);
    }

    // card
    public function test_cancelOrderRows_cancelCardOrderRows_returns_LowerTransaction()
    {
        $cancelOrderRowsBuilder = WebPayAdmin::cancelOrderRows(ConfigurationService::getDefaultConfig())
            ->addNumberedOrderRow(TestUtil::createNumberedOrderRow(100.00, 1, 1))
            ->setRowToCancel(1);
        $request = $cancelOrderRowsBuilder->cancelCardOrderRows();
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedAdminRequest\LowerTransaction", $request);
    }
    // TODO add validation tests here

    // Svea\WebPay\WebPayAdmin::creditOrderRows --------------------------------------------    
    public function test_creditOrderRows_returns_CreditOrderRowsBuilder()
    {
        $builderObject = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig());
        $this->assertInstanceOf("Svea\WebPay\BuildOrder\CreditOrderRowsBuilder", $builderObject);
    }

    // creditInvoiceOrderRows  
    function test_validates_all_required_methods_for_creditOrderRows_creditInvoiceRows()
    {
        // needs either setRow(s)ToCredit or addCreditOrderRow(s)    
        $request = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig())
            ->setInvoiceId("123456789")
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode("SE")
            //->addCreditOrderRow( Svea\WebPay\Test\TestUtil::createOrderRow() )
            //->addCreditOrderRows( array( Svea\WebPay\Test\TestUtil::createOrderRow(), Svea\WebPay\Test\TestUtil::createOrderRow() ) )         
            ->setRowToCredit(1)//->setRowsToCredit(array(1,2))
        ;
        try {
            $request->creditInvoiceOrderRows()->prepareRequest();
            $this->assertTrue(true);
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : no rows to credit, use setRow(s)ToCredit() or addCreditOrderRow(s)().
     */
    function test_validates_missing_required_method_for_creditOrderRows_creditInvoiceOrderRows_missing_rows()
    {
        $request = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig())
            ->setInvoiceId("123456789")
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode("SE")
            //->addCreditOrderRow( Svea\WebPay\Test\TestUtil::createOrderRow() )
            //->addCreditOrderRows( array( Svea\WebPay\Test\TestUtil::createOrderRow(), Svea\WebPay\Test\TestUtil::createOrderRow() ) )         
            //->setRowToCredit(1)              
            //->setRowsToCredit(array(1,2))
        ;
        $request->creditInvoiceOrderRows()->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : countryCode is required, use setCountryCode().
     */
    function test_validates_missing_required_method_for_creditOrderRows_creditInvoiceOrderRows_missing_setCountryCode()
    {
        $request = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig())
            ->setInvoiceId("123456789")
            ->setInvoiceDistributionType(DistributionType::POST)
            //->setCountryCode("SE")            
            //->addCreditOrderRow( Svea\WebPay\Test\TestUtil::createOrderRow() )
            //->addCreditOrderRows( array( Svea\WebPay\Test\TestUtil::createOrderRow(), Svea\WebPay\Test\TestUtil::createOrderRow() ) )         
            ->setRowToCredit(1)//->setRowsToCredit(array(1,2))
        ;
        $request->creditInvoiceOrderRows()->prepareRequest();
    }
    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : distributionType is required, use setInvoiceDistributionType().
     */
    function test_validates_missing_required_method_for_creditOrderRows_creditInvoiceOrderRows_missing_setInvoiceDistributionType()
    {
        $request = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig())
            ->setInvoiceId("123456789")
            //->setInvoiceDistributionType(Svea\WebPay\Constant\DistributionType::POST)
            ->setCountryCode("SE")
            //->addCreditOrderRow( Svea\WebPay\Test\TestUtil::createOrderRow() )
            //->addCreditOrderRows( array( Svea\WebPay\Test\TestUtil::createOrderRow(), Svea\WebPay\Test\TestUtil::createOrderRow() ) )         
            ->setRowToCredit(1)//->setRowsToCredit(array(1,2))
        ;
        $request->creditInvoiceOrderRows()->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : invoiceId is required, use setInvoiceId().
     */
    function test_validates_missing_required_method_for_creditOrderRows_creditInvoiceOrderRows_missing_setInvoiceId()
    {
        $request = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig())
            //->setInvoiceId("123456789")                
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode("SE")
            //->addCreditOrderRow( Svea\WebPay\Test\TestUtil::createOrderRow() )
            //->addCreditOrderRows( array( Svea\WebPay\Test\TestUtil::createOrderRow(), Svea\WebPay\Test\TestUtil::createOrderRow() ) )         
            ->setRowToCredit(1)//->setRowsToCredit(array(1,2))
        ;
        $request->creditInvoiceOrderRows()->prepareRequest();
    }

    // creditCardOrderRows
    function test_validates_all_required_methods_for_creditOrderRows_creditCardOrderRows()
    {
        $request = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig())
            ->setOrderId("123456")
            ->setCountryCode("SE")
            ->addCreditOrderRow(TestUtil::createOrderRow())
            ->addCreditOrderRows(array(TestUtil::createOrderRow(), TestUtil::createOrderRow()))
            ->setRowToCredit(1)
            ->setRowsToCredit(array(2, 3))
            ->addNumberedOrderRow(TestUtil::createNumberedOrderRow(100.00, 1, 1))
            ->addNumberedOrderRows(array(TestUtil::createNumberedOrderRow(100.00, 1, 2), TestUtil::createNumberedOrderRow(100.00, 1, 3)));
        try {
            $request->creditCardOrderRows()->prepareRequest();
            $this->assertTrue(true);
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage orderId is required for creditCardOrderRows(). Use method setOrderId()
     */
    function test_validates_missing_required_method_for_creditOrderRows_creditCardOrderRows_missing_setOrderId()
    {
        $request = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig())
            //->setOrderId("123456")              
            //->setCountryCode("SE")              
            //->addCreditOrderRow( Svea\WebPay\Test\TestUtil::createOrderRow() )
            //->addCreditOrderRows( array( Svea\WebPay\Test\TestUtil::createOrderRow(), Svea\WebPay\Test\TestUtil::createOrderRow() ) )    
            //->setRowToCredit(1)
            //->setRowsToCredit(array(2,3))
            //->addNumberedOrderRow( Svea\WebPay\Test\TestUtil::createNumberedOrderRow( 100.00, 1, 1 ) )
            //->addNumberedOrderRows( array( Svea\WebPay\Test\TestUtil::createNumberedOrderRow( 100.00, 1, 2),Svea\WebPay\Test\TestUtil::createNumberedOrderRow( 100.00, 1, 3 ) ) )
        ;
        $request->creditCardOrderRows()->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage countryCode is required for creditCardOrderRows(). Use method setCountryCode().
     */
    function test_validates_missing_required_method_for_creditOrderRows_creditCardOrderRows_missing_setCountryCode()
    {
        $request = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig())
            ->setOrderId("123456")
            //->setCountryCode("SE")              
            //->addCreditOrderRow( Svea\WebPay\Test\TestUtil::createOrderRow() )
            //->addCreditOrderRows( array( Svea\WebPay\Test\TestUtil::createOrderRow(), Svea\WebPay\Test\TestUtil::createOrderRow() ) )    
            //->setRowToCredit(1)
            //->setRowsToCredit(array(2,3))
            //->addNumberedOrderRow( Svea\WebPay\Test\TestUtil::createNumberedOrderRow( 100.00, 1, 1 ) )
            //->addNumberedOrderRows( array( Svea\WebPay\Test\TestUtil::createNumberedOrderRow( 100.00, 1, 2),Svea\WebPay\Test\TestUtil::createNumberedOrderRow( 100.00, 1, 3 ) ) )
        ;
        $request->creditCardOrderRows()->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage at least one of rowsToCredit or creditOrderRows must be set. Use setRowToCredit() or addCreditOrderRow().
     */
    function test_validates_missing_required_method_for_creditOrderRows_creditCardOrderRows_missing_rows_to_credit()
    {
        $request = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig())
            ->setOrderId("123456")
            ->setCountryCode("SE")
            //->addCreditOrderRow( Svea\WebPay\Test\TestUtil::createOrderRow() )
            //->addCreditOrderRows( array( Svea\WebPay\Test\TestUtil::createOrderRow(), Svea\WebPay\Test\TestUtil::createOrderRow() ) )    
            //->setRowToCredit(1)
            //->setRowsToCredit(array(2,3))
            //->addNumberedOrderRow( Svea\WebPay\Test\TestUtil::createNumberedOrderRow( 100.00, 1, 1 ) )
            //->addNumberedOrderRows( array( Svea\WebPay\Test\TestUtil::createNumberedOrderRow( 100.00, 1, 2),Svea\WebPay\Test\TestUtil::createNumberedOrderRow( 100.00, 1, 3 ) ) )
        ;
        $request->creditCardOrderRows()->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage every entry in rowsToCredit must have a corresponding numberedOrderRows. Use setRowsToCredit() and addNumberedOrderRow().
     */
    function test_validates_missing_required_method_for_creditOrderRows_creditCardOrderRows_missing_numberedOrderRows()
    {
        $request = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig())
            ->setOrderId("123456")
            ->setCountryCode("SE")
            //->addCreditOrderRow( Svea\WebPay\Test\TestUtil::createOrderRow() )
            //->addCreditOrderRows( array( Svea\WebPay\Test\TestUtil::createOrderRow(), Svea\WebPay\Test\TestUtil::createOrderRow() ) )    
            ->setRowToCredit(1)
            //->setRowsToCredit(array(2,3))
            //->addNumberedOrderRow( Svea\WebPay\Test\TestUtil::createNumberedOrderRow( 100.00, 1, 1 ) )
            //->addNumberedOrderRows( array( Svea\WebPay\Test\TestUtil::createNumberedOrderRow( 100.00, 1, 2),Svea\WebPay\Test\TestUtil::createNumberedOrderRow( 100.00, 1, 3 ) ) )
        ;
        $request->creditCardOrderRows()->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage every entry in rowsToCredit must match a numberedOrderRows. Use setRowsToCredit() and addNumberedOrderRow().
     */
    function test_validates_missing_required_method_for_creditOrderRows_creditCardOrderRows__mismatched_numberedOrderRows()
    {
        $creditOrderRowsObject = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig())
            ->setOrderId("123456")
            ->setCountryCode("SE")
            ->addNumberedOrderRow(TestUtil::createNumberedOrderRow(100.00, 1, 1))
            ->setRowToCredit(9);
        $request = $creditOrderRowsObject->creditCardOrderRows(); // exception thrown in builder when selecting request class   
    }

    // creditDirectBankOrderRows
    /**
     * @doesNotPerformAssertions
     */
    function test_no_separate_validation_tests_for_creditOrderRows_creditDirectBankOrderRows()
    {
        // creditDirectBankOrderRows is an alias of creditCardOrderRows, so no separate tests are needed
    }

    // end creditOrderRows tests -----------------------------------------------

    // Svea\WebPay\WebPayAdmin::addOrderRows() ---------------------------------------------
    public function test_addOrderRows_returns_AddOrderRowsBuilder()
    {
        $builderObject = WebPayAdmin::addOrderRows(ConfigurationService::getDefaultConfig());
        $this->assertInstanceOf("Svea\WebPay\BuildOrder\AddOrderRowsBuilder", $builderObject);
    }
    // TODO add validation unit tests
    // end addOrderRows tests --------------------------------------------------

    // Svea\WebPay\WebPayAdmin::updateOrderRows() ------------------------------------------
    public function test_updateOrderRows_returns_AddOrderRowsBuilder()
    {
        $builderObject = WebPayAdmin::updateOrderRows(ConfigurationService::getDefaultConfig());
        $this->assertInstanceOf("Svea\WebPay\BuildOrder\UpdateOrderRowsBuilder", $builderObject);
    }
    // TODO add validation unit tests
    // end updateOrderRows tests -----------------------------------------------

    // Svea\WebPay\WebPayAdmin::deliverOrderRows() -----------------------------------------
    // TODO add validation unit tests
    // end deliverOrderRows tests ----------------------------------------------


    // Verify that new orderRows may be specified with zero amount (INT-581) with WPA::addOrderRows
    public function test_addOrderRows_addInvoiceOrderRows_allows_orderRow_with_zero_amount()
    {
        $dummyorderid = 123456;
        $orderBuilder = WebPayAdmin::addOrderRows(ConfigurationService::getDefaultConfig())
            ->setOrderId($dummyorderid)
            ->setCountryCode("SE")
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(0)
                    ->setVatPercent(0)
                    ->setQuantity(0)
            );

        try {
            $request = $orderBuilder->addInvoiceOrderRows()->prepareRequest();
            $this->assertTrue(true);
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    public function test_addOrderRows_addPaymentPlanOrderRows_allows_orderRow_with_zero_amount()
    {
        $dummyorderid = 123456;
        $orderBuilder = WebPayAdmin::addOrderRows(ConfigurationService::getDefaultConfig())
            ->setOrderId($dummyorderid)
            ->setCountryCode("SE")
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(0.0)
                    ->setVatPercent(0)
                    ->setQuantity(0)
            );

        try {
            $request = $orderBuilder->addPaymentPlanOrderRows()->prepareRequest();
            $this->assertTrue(true);
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    // Verify that new orderRows may be specified with zero amount (INT-581) with WPA::updateOrderRows
    public function test_updateOrderRows_updateInvoiceOrderRows_allows_orderRow_with_zero_amount()
    {
        $dummyorderid = 123456;
        $orderBuilder = WebPayAdmin::updateOrderRows(ConfigurationService::getDefaultConfig())
            ->setOrderId($dummyorderid)
            ->setCountryCode("SE")
            ->updateOrderRow(
                WebPayItem::numberedOrderRow()
                    ->setAmountExVat(0.0)
                    ->setVatPercent(0)
                    ->setQuantity(0)
                    ->setRowNumber(1)
            );

        try {
            $request = $orderBuilder->updateInvoiceOrderRows()->prepareRequest();
            $this->assertTrue(true);
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    public function test_updateOrderRows_updatePaymentPlanOrderRows_allows_orderRow_with_zero_amount()
    {
        $dummyorderid = 123456;
        $orderBuilder = WebPayAdmin::updateOrderRows(ConfigurationService::getDefaultConfig())
            ->setOrderId($dummyorderid)
            ->setCountryCode("SE")
            ->updateOrderRow(
                WebPayItem::numberedOrderRow()
                    ->setAmountExVat(0.0)
                    ->setVatPercent(0)
                    ->setQuantity(0)
                    ->setRowNumber(1)
            );

        try {
            $request = $orderBuilder->updatePaymentPlanOrderRows()->prepareRequest();
            $this->assertTrue(true);
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    // Verify that new orderRows may be specified with zero amount (INT-581) with WPA::creditOrderRows
    public function test_creditOrderRows_creditInvoiceOrderRows_allows_orderRow_with_zero_amount()
    {
        $dummyorderid = 123456;
        $orderBuilder = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig())
            ->setInvoiceId($dummyorderid)
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode("SE")
            ->addCreditOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(0.0)
                    ->setVatPercent(0)
                    ->setQuantity(0)
            );

        try {
            $request = $orderBuilder->creditInvoiceOrderRows()->prepareRequest();
            $this->assertTrue(true);
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    public function test_creditOrderRows_creditCardOrderRows_allows_orderRow_with_zero_amount()
    {
        $dummyorderid = 123456;
        $orderBuilder = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig())
            ->setOrderId($dummyorderid)
            ->setCountryCode("SE")
            ->addCreditOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(0.0)
                    ->setVatPercent(0)
                    ->setQuantity(0)
            );

        try {
            $request = $orderBuilder->creditCardOrderRows()->prepareRequest();
            $this->assertTrue(true);
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }
}
