<?php

namespace Svea\WebPay\Test\IntegrationTest\BuildOrder;

use PHPUnit_Framework_TestCase;
use Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\WebPayItem;

/**
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class UpdateOrderRowsBuilderIntegrationTest extends PHPUnit_Framework_TestCase
{

    protected $invoiceIdToTest;
    protected $country;

    protected function setUp()
    {
        $this->country = "SE";
        $this->invoiceIdToTest = 583004;   // set this to the approved invoice set up by test_manual_setup_CreditOrderRows_testdata()
    }

    function test_UpdateOrderRows_updateInvoiceOrderRows_single_row_success()
    {
        $country = "SE";

        // create order
        $order = TestUtil::createOrderWithoutOrderRows(TestUtil::createIndividualCustomer($country));
        $order->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("1")
            ->setQuantity(1)
            ->setAmountExVat(1.00)
            ->setVatPercent(25)
            ->setDescription("A Specification")
            ->setName('A Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );
        $order->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("2")
            ->setQuantity(1)
            ->setAmountExVat(2.00)
            ->setVatPercent(25)
            ->setDescription("B Specification")
            ->setName('B Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );

        $orderResponse = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        // update all attributes for a numbered orderRow   
        $updateOrderRowsResponse = WebPayAdmin::updateOrderRows(ConfigurationService::getDefaultConfig())
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode($country)
            ->updateOrderRow(WebPayItem::numberedOrderRow()
                ->setArticleNumber("10")
                ->setQuantity(1)
                ->setAmountExVat(10.00)
                ->setVatPercent(26)
                ->setDescription("K Specification")
                ->setName('K Name')
                ->setUnit("st")
                ->setDiscountPercent(1)
                ->setRowNumber(1)
                ->setStatus(NumberedOrderRow::ORDERROWSTATUS_NOTDELIVERED)
            )
            ->updateInvoiceOrderRows()
            ->doRequest();

        ////print_r( $updateOrderRowsResponse );
        ////print_r("test_UpdateOrderRows_updateInvoiceOrderRows_single_row_success: "); //print_r( $orderResponse->sveaOrderId );
        $this->assertEquals(1, $updateOrderRowsResponse->accepted);
        // todo query result & check amounts, description automatically        
    }

    function test_UpdateOrderRows_updateInvoiceOrderRows_multiple_row_success()
    {
        $country = "SE";

        // create order
        $order = TestUtil::createOrderWithoutOrderRows(TestUtil::createIndividualCustomer($country));
        $order->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("1")
            ->setQuantity(1)
            ->setAmountExVat(1.00)
            ->setVatPercent(25)
            ->setDescription("A Specification")
            ->setName('A Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );
        $order->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("2")
            ->setQuantity(1)
            ->setAmountExVat(2.00)
            ->setVatPercent(25)
            ->setDescription("B Specification")
            ->setName('B Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );
        $order->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("3")
            ->setQuantity(1)
            ->setAmountExVat(3.00)
            ->setVatPercent(25)
            ->setDescription("C Specification")
            ->setName('C Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );
        $orderResponse = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        // update all attributes for a numbered orderRow   
        $updateOrderRowsResponse = WebPayAdmin::updateOrderRows(ConfigurationService::getDefaultConfig())
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode($country)
            ->updateOrderRow(WebPayItem::numberedOrderRow()
                ->setArticleNumber("10")
                ->setQuantity(1)
                ->setAmountExVat(10.00)
                ->setVatPercent(25)
                ->setDescription("K Specification")
                ->setName('K Name')
                ->setUnit("st")
                ->setDiscountPercent(1)
                ->setRowNumber(1)
                ->setStatus(NumberedOrderRow::ORDERROWSTATUS_NOTDELIVERED)
            )
            ->updateOrderRows(
                array(
                    WebPayItem::numberedOrderRow()
                        ->setArticleNumber("20")
                        ->setQuantity(2)
                        ->setAmountExVat(20.00)
                        ->setVatPercent(25)
                        ->setDescription("K2 Specification")
                        ->setName('K2 Name')
                        ->setUnit("st")
                        ->setDiscountPercent(1)
                        ->setRowNumber(2)
                        ->setStatus(NumberedOrderRow::ORDERROWSTATUS_NOTDELIVERED)
                    ,
                    WebPayItem::numberedOrderRow()
                        ->setArticleNumber("30")
                        ->setQuantity(3)
                        ->setAmountExVat(30.00)
                        ->setVatPercent(25)
                        ->setDescription("K3 Specification")
                        ->setName('K3 Name')
                        ->setUnit("st")
                        ->setDiscountPercent(1)
                        ->setRowNumber(3)
                        ->setStatus(NumberedOrderRow::ORDERROWSTATUS_NOTDELIVERED)
                )
            )
            ->updateInvoiceOrderRows()
            ->doRequest();

        ////print_r( $updateOrderRowsResponse );
        ////print_r("test_UpdateOrderRows_updateInvoiceOrderRows_single_row_success: "); //print_r( $orderResponse->sveaOrderId );
        $this->assertEquals(1, $updateOrderRowsResponse->accepted);
        // todo query result & check amounts, description automatically        
    }

    function test_UpdateOrderRows_updatePaymentPlanOrderRows_multiple_row_success()
    {
        $country = "SE";

        // create order
        $order = TestUtil::createOrderWithoutOrderRows(TestUtil::createIndividualCustomer($country));
        $order->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("1")
            ->setQuantity(1)
            ->setAmountExVat(1000.00)
            ->setVatPercent(25)
            ->setDescription("A Specification")
            ->setName('A Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );
        $order->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("2")
            ->setQuantity(1)
            ->setAmountExVat(2000.00)
            ->setVatPercent(25)
            ->setDescription("B Specification")
            ->setName('B Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );
        $order->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("3")
            ->setQuantity(1)
            ->setAmountExVat(3000.00)
            ->setVatPercent(25)
            ->setDescription("C Specification")
            ->setName('C Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );
        $orderResponse = $order->usePaymentPlanPayment(TestUtil::getGetPaymentPlanParamsForTesting())->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        // update all attributes for a numbered orderRow   
        $updateOrderRowsResponse = WebPayAdmin::updateOrderRows(ConfigurationService::getDefaultConfig())
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode($country)
            ->updateOrderRow(WebPayItem::numberedOrderRow()
                ->setArticleNumber("10")
                ->setQuantity(1)
                ->setAmountExVat(10.00)
                ->setVatPercent(25)
                ->setDescription("K Specification")
                ->setName('K Name')
                ->setUnit("st")
                ->setDiscountPercent(1)
                ->setRowNumber(1)
                ->setStatus(NumberedOrderRow::ORDERROWSTATUS_NOTDELIVERED)
            )
            ->updateOrderRows(
                array(
                    WebPayItem::numberedOrderRow()
                        ->setArticleNumber("20")
                        ->setQuantity(2)
                        ->setAmountExVat(20.00)
                        ->setVatPercent(25)
                        ->setDescription("K2 Specification")
                        ->setName('K2 Name')
                        ->setUnit("st")
                        ->setDiscountPercent(1)
                        ->setRowNumber(2)
                        ->setStatus(NumberedOrderRow::ORDERROWSTATUS_NOTDELIVERED)
                    ,
                    WebPayItem::numberedOrderRow()
                        ->setArticleNumber("30")
                        ->setQuantity(3)
                        ->setAmountExVat(30.00)
                        ->setVatPercent(25)
                        ->setDescription("K3 Specification")
                        ->setName('K3 Name')
                        ->setUnit("st")
                        ->setDiscountPercent(1)
                        ->setRowNumber(3)
                        ->setStatus(NumberedOrderRow::ORDERROWSTATUS_NOTDELIVERED)
                )
            )
            ->updatePaymentPlanOrderRows()
            ->doRequest();

        ////print_r( $updateOrderRowsResponse );
        ////print_r("test_UpdateOrderRows_updateInvoiceOrderRows_single_row_success: "); //print_r( $orderResponse->sveaOrderId );
        $this->assertEquals(1, $updateOrderRowsResponse->accepted);
        // todo query result & check amounts, description automatically        
    }

    function _test_UpdateOrderRows_manually_created_paymentplan()
    {
        $country = "SE";

//        // create order
//        $order = Svea\WebPay\Test\TestUtil::createOrderWithoutOrderRows( Svea\WebPay\Test\TestUtil::createIndividualCustomer($country) );
//        $order->addOrderRow( Svea\WebPay\WebPayItem::orderRow()
//            ->setArticleNumber("1")
//            ->setQuantity( 1 )
//            ->setAmountExVat( 1000.00 )
//            ->setVatPercent(25)
//            ->setDescription("A Specification")
//            ->setName('A Name')
//            ->setUnit("st")
//            ->setDiscountPercent(0)
//        );      
//        $order->addOrderRow( Svea\WebPay\WebPayItem::orderRow()
//            ->setArticleNumber("2")
//            ->setQuantity( 1 )
//            ->setAmountExVat( 2000.00 )
//            ->setVatPercent(25)
//            ->setDescription("B Specification")
//            ->setName('B Name')
//            ->setUnit("st")
//            ->setDiscountPercent(0)
//        );         
//        $order->addOrderRow( Svea\WebPay\WebPayItem::orderRow()
//            ->setArticleNumber("3")
//            ->setQuantity( 1 )
//            ->setAmountExVat( 3000.00 )
//            ->setVatPercent(25)
//            ->setDescription("C Specification")
//            ->setName('C Name')
//            ->setUnit("st")
//            ->setDiscountPercent(0)
//        );         
//        $orderResponse = $order->usePaymentPlanPayment( Svea\WebPay\Test\TestUtil::getGetPaymentPlanParamsForTesting() )->doRequest();       
//        $this->assertEquals(1, $orderResponse->accepted);

        // update all attributes for a numbered orderRow   
        $updateOrderRowsResponse = WebPayAdmin::updateOrderRows(ConfigurationService::getDefaultConfig())
            ->setOrderId(364183)
            ->setCountryCode($country)
            ->updateOrderRow(WebPayItem::numberedOrderRow()
                ->setArticleNumber("10")
                ->setQuantity(1)
                ->setAmountExVat(1000.00)
                ->setVatPercent(25)
                ->setDescription("K Specification")
                ->setName('K Name')
                ->setUnit("st")
                ->setDiscountPercent(1)
                ->setRowNumber(1)
                ->setStatus(NumberedOrderRow::ORDERROWSTATUS_NOTDELIVERED)
            )
            ->updatePaymentPlanOrderRows()
            ->doRequest();

        //print_r( $updateOrderRowsResponse );
        ////print_r("test_UpdateOrderRows_updateInvoiceOrderRows_single_row_success: "); //print_r( $orderResponse->sveaOrderId );
        $this->assertEquals(1, $updateOrderRowsResponse->accepted);
        // todo query result & check amounts, description automatically        
    }
}

?>