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
class WebPayAdminUnitTest extends \PHPUnit_Framework_TestCase
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
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    function test_missing_required_method_for_cancelOrder_cancelCardOrder_setOrderId()
    {
        $request = WebPayAdmin::cancelOrder(ConfigurationService::getDefaultConfig())
            //->setOrderId("123456789")                
            ->setCountryCode("SE");
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing value : transactionId is required. Use function setTransactionId() with the SveaOrderId from the createOrder response'
        );
        $request->cancelCardOrder()->prepareRequest();
    }

    function test_missing_required_method_for_cancelOrder_cancelCardOrder_setCountryCode()
    {
        $request = WebPayAdmin::cancelOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId("123456789")//->setCountryCode("SE")            
        ;
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing value : CountryCode is required. Use function setCountryCode().'
        );
        $request->cancelCardOrder()->prepareRequest();
    }

    // Svea\WebPay\WebPayAdmin::queryOrder() -----------------------------------------------
    // returned type
    /// queryOrder()
    // invoice
    // partpayment
    // card
    function test_queryOrder_queryCardOrder()
    {
        // Set the below to match the transaction, then run the test.
        $transactionId = 590177;

        $request = WebPayAdmin::queryOrder(
            ConfigurationService::getSingleCountryConfig(
                "SE",
                "foo", "bar", "123456", // invoice
                "foo", "bar", "123456", // paymentplan
                "foo", "bar", "123456", // accountplan
                "1200", // merchantid, secret
                "27f18bfcbe4d7f39971cb3460fbe7234a82fb48f985cf22a068fa1a685fe7e6f93c7d0d92fee4e8fd7dc0c9f11e2507300e675220ee85679afa681407ee2416d",
                false // prod = false
            )
        )
            ->setTransactionId(strval($transactionId))
            ->setCountryCode("SE");
        $response = $request->queryCardOrder()->doRequest();
//        echo "foo: ";
//        var_dump($response); die;

        $this->assertEquals(1, $response->accepted);

        $this->assertEquals($transactionId, $response->transactionId);
        $this->assertInstanceOf("Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow", $response->numberedOrderRows[0]);
        $this->assertEquals("Soft213s", $response->numberedOrderRows[0]->articleNumber);
        $this->assertEquals("1.0", $response->numberedOrderRows[0]->quantity);
        $this->assertEquals("st", $response->numberedOrderRows[0]->unit);
        $this->assertEquals(3212.00, $response->numberedOrderRows[0]->amountExVat);   // amount = 401500, vat = 80300 => 3212.00 @25%
        $this->assertEquals(25, $response->numberedOrderRows[0]->vatPercent);
        $this->assertEquals("Soft", $response->numberedOrderRows[0]->name);
//        $this->assertEquals( "Specification", $response->numberedOrderRows[1]->description );
        $this->assertEquals(0, $response->numberedOrderRows[0]->vatDiscount);

        $this->assertInstanceOf("Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow", $response->numberedOrderRows[1]);
        $this->assertEquals("07", $response->numberedOrderRows[1]->articleNumber);
        $this->assertEquals("1.0", $response->numberedOrderRows[1]->quantity);
        $this->assertEquals("st", $response->numberedOrderRows[1]->unit);
        $this->assertEquals(0, $response->numberedOrderRows[1]->amountExVat);   // amount = 401500, vat = 80300 => 3212.00 @25%
        $this->assertEquals(0, $response->numberedOrderRows[1]->vatPercent);
        $this->assertEquals("Sits: Hatfield Beige 6", $response->numberedOrderRows[1]->name);
//        $this->assertEquals( "Specification", $response->numberedOrderRows[1]->description );
        $this->assertEquals(0, $response->numberedOrderRows[1]->vatDiscount);
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

    public function test_queryOrder_queryDirectBankOrder_returns_QueryTransaction()
    {
        $queryOrder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig());
        $request = $queryOrder->queryDirectBankOrder();
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedAdminRequest\QueryTransaction", $request);
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
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

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
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing value : no rows to credit, use setRow(s)ToCredit() or addCreditOrderRow(s)().'
        );
        $request->creditInvoiceOrderRows()->prepareRequest();
    }

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
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing value : countryCode is required, use setCountryCode().'
        );
        $request->creditInvoiceOrderRows()->prepareRequest();
    }

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
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing value : distributionType is required, use setInvoiceDistributionType().'
        );
        $request->creditInvoiceOrderRows()->prepareRequest();
    }

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
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing value : invoiceId is required, use setInvoiceId().'
        );
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
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

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
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            'orderId is required for creditCardOrderRows(). Use method setOrderId()'
        );
        $request->creditCardOrderRows()->prepareRequest();
    }

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
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            'countryCode is required for creditCardOrderRows(). Use method setCountryCode().'
        );
        $request->creditCardOrderRows()->prepareRequest();
    }

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
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            'at least one of rowsToCredit or creditOrderRows must be set. Use setRowToCredit() or addCreditOrderRow().'
        );
        $request->creditCardOrderRows()->prepareRequest();
    }

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
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            'every entry in rowsToCredit must have a corresponding numberedOrderRows. Use setRowsToCredit() and addNumberedOrderRow()'
        );
        $request->creditCardOrderRows()->prepareRequest();
    }

    function test_validates_missing_required_method_for_creditOrderRows_creditCardOrderRows__mismatched_numberedOrderRows()
    {
        $creditOrderRowsObject = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig())
            ->setOrderId("123456")
            ->setCountryCode("SE")
            ->addNumberedOrderRow(TestUtil::createNumberedOrderRow(100.00, 1, 1))
            ->setRowToCredit(9);
        $this->setExpectedException(
            '\Svea\WebPay\BuildOrder\Validator\ValidationException', 'every entry in rowsToCredit must match a numberedOrderRows. Use setRowsToCredit() and addNumberedOrderRow().'
        );
        $request = $creditOrderRowsObject->creditCardOrderRows(); // exception thrown in builder when selecting request class   
    }

    // creditDirectBankOrderRows
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
                    ->setAmountExVat(0.0)
                    ->setVatPercent(0)
                    ->setQuantity(0)
            );

        try {
            $request = $orderBuilder->addInvoiceOrderRows()->prepareRequest();
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
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }
}
