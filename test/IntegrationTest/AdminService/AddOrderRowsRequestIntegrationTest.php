<?php

namespace Svea\WebPay\Test\IntegrationTest\AdminService;

use PHPUnit_Framework_TestCase;
use Svea\WebPay\AdminService\AddOrderRowsRequest;
use Svea\WebPay\BuildOrder\OrderBuilder;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\WebPayItem;


/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class AddOrderRowsRequestIntegrationTest extends PHPUnit_Framework_TestCase
{

    public $builderObject;

    public function setUp()
    {
        $this->builderObject = new OrderBuilder(ConfigurationService::getDefaultConfig());
        $this->builderObject->orderId = 123456;
        $this->builderObject->orderType = ConfigurationProvider::INVOICE_TYPE;
        $this->builderObject->countryCode = "SE";
        $this->builderObject->orderRows = array(TestUtil::createOrderRow(10.00));
    }

    public function test_add_single_orderRow()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(145.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        // add order rows to builderobject
        $this->builderObject->orderRows[] = TestUtil::createOrderRow(1.00, 1);
        $this->builderObject->orderId = $orderResponse->sveaOrderId;

        $addOrderRowsRequest = new AddOrderRowsRequest($this->builderObject);
        $addOrderRowsResponse = $addOrderRowsRequest->doRequest();

        $this->assertInstanceOf('Svea\WebPay\AdminService\AdminServiceResponse\AddOrderRowsResponse', $addOrderRowsResponse);
        $this->assertEquals(1, $addOrderRowsResponse->accepted);

    }

    public function test_add_single_orderRow_with_vat_match()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(145.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        // add order rows to builderobject
        $response = WebPayAdmin::addOrderRows($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setVatPercent(24)
                    ->setAmountExVat(80.00)
                    ->setQuantity(1)
            )
            ->addInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $response->accepted);

        // query order and assert row totals
        $query = WebPayAdmin::queryOrder($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query->accepted);
        //print_r( "\n\ntest_dd_single_orderRow_with_vat_match: created w/ 145 ex @24% = 179,80 for " .$orderResponse->sveaOrderId );
        //print_r( "\ntest_dd_single_orderRow_with_vat_match: added w/ 80 ex @24% = +99,2" );
        //print_r( "\ntest_dd_single_orderRow_with_vat_match: total amount 179,80 +99,2 = 279,00 for ".$orderResponse->sveaOrderId );
        $this->assertEquals("145.00", $query->numberedOrderRows[0]->amountExVat);   // => 179,80
        $this->assertEquals("24", $query->numberedOrderRows[0]->vatPercent);
        $this->assertEquals("80.00", $query->numberedOrderRows[1]->amountExVat);    // => 99,20
        $this->assertEquals("24", $query->numberedOrderRows[1]->vatPercent);
    }

    public function test_add_single_orderRow_original_exvat_add_incvat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(145.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        // add order rows to
        $response = WebPayAdmin::addOrderRows($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setVatPercent(24)
                    ->setAmountIncVat(80.00)
                    ->setQuantity(1)
            )
            ->addInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $response->accepted);

        // query order and assert row totals
        $query = WebPayAdmin::queryOrder($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query->accepted);
        //print_r( "\n\ntest_add_single_orderRow_with_vat_missmatch1: created w/ 145 ex @24% = 179,80 for " .$orderResponse->sveaOrderId );
        //print_r( "\ntest_add_single_orderRow_with_vat_missmatch1: added w/ 80 inc @24% = 80" );
        //print_r( "\ntest_add_single_orderRow_with_vat_missmatch1: total amount 179,80 +80 = 259,80 for ".$orderResponse->sveaOrderId );
        $this->assertEquals("145.00", $query->numberedOrderRows[0]->amountExVat);   // => 179,80        
        $this->assertEquals("24", $query->numberedOrderRows[0]->vatPercent);
        $this->assertEquals("64.52", $query->numberedOrderRows[1]->amountExVat);    // 64,5161 *1,24 => 80.00
        $this->assertEquals("24", $query->numberedOrderRows[1]->vatPercent);
        //print_r( $orderResponse->sveaOrderId );
    }

    public function test_add_single_orderRow_original_incvat_add_exvat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(145.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        // add order rows to builderobject
        $response = WebPayAdmin::addOrderRows($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setVatPercent(24)
                    ->setAmountExVat(80.00)
                    ->setQuantity(1)
            )
            ->addInvoiceOrderRows()
            ->doRequest();
        $this->assertEquals(1, $response->accepted);

        // query order and assert row totals
        $query = WebPayAdmin::queryOrder($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query->accepted);
        //print_r( "\n\ntest_add_single_orderRow_with_vat_missmatch2: created w/ 145 inc @24% = 145,00 for " .$orderResponse->sveaOrderId );
        //print_r( "\ntest_add_single_orderRow_with_vat_missmatch2: added w/ 80 ex @24% = +99,2");
        //print_r( "\ntest_add_single_orderRow_with_vat_missmatch2: total amount 145 +99,2 = 244,2 for ".$orderResponse->sveaOrderId );
        $this->assertEquals("145.00", $query->numberedOrderRows[0]->amountIncVat);
        $this->assertEquals("24", $query->numberedOrderRows[0]->vatPercent);
        $this->assertEquals("24", $query->numberedOrderRows[1]->vatPercent);
        //print_r( $orderResponse->sveaOrderId );        
    }

    //--------------------------------------------------------------------------------------------------

    public function test_add_single_orderRow_sent_with_ex_vat_may_have_rounding_errors_example()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(145.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        // add order rows to
        $response = WebPayAdmin::addOrderRows($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setVatPercent(24)
                    ->setAmountIncVat(80.00)
                    ->setQuantity(1)
            )
            ->addInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $response->accepted);
        // first sent as incvat, will cause error and resend:
        //<ns3:PriceIncludingVat>true</ns3:PriceIncludingVat>
        //<ns3:PricePerUnit>80</ns3:PricePerUnit>            
        // resent as exvat:
        //<ns3:PriceIncludingVat>false</ns3:PriceIncludingVat>
        //<ns3:PricePerUnit>64.516129032258</ns3:PricePerUnit>

        // query order and assert row totals
        $query = WebPayAdmin::queryOrder($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query->accepted);
        //print_r( "\n\ntest_add_single_orderRow_with_vat_missmatch1: created w/ 145 ex @24% = 179,80 for " .$orderResponse->sveaOrderId );
        //print_r( "\ntest_add_single_orderRow_with_vat_missmatch1: added w/ 80 inc @24% = 80" );
        //print_r( "\ntest_add_single_orderRow_with_vat_missmatch1: total amount 179,80 +80 = 259,80 for ".$orderResponse->sveaOrderId );
        $this->assertEquals("145.00", $query->numberedOrderRows[0]->amountExVat);   // => 179,80 inc        
        $this->assertEquals("24", $query->numberedOrderRows[0]->vatPercent);
        $this->assertEquals("64.52", $query->numberedOrderRows[1]->amountExVat);    // => 80.00 inc
        //print_r( $orderResponse->sveaOrderId );
    }

    public function test_add_single_orderRow_sent_with_inc_vat_has_correct_amount()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(179.80)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        // add order rows to
        $response = WebPayAdmin::addOrderRows($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setVatPercent(24)
                    ->setAmountIncVat(80.00)
                    ->setQuantity(1)
            )
            ->addInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $response->accepted);

        // query order and assert row totals
        $query = WebPayAdmin::queryOrder($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query->accepted);
        //print_r( "\n\ntest_add_single_orderRow_with_vat_missmatch1: created w/ 145 ex @24% = 179,80 for " .$orderResponse->sveaOrderId );
        //print_r( "\ntest_add_single_orderRow_with_vat_missmatch1: added w/ 80 inc @24% = 80" );
        //print_r( "\ntest_add_single_orderRow_with_vat_missmatch1: total amount 179,80 +80 = 259,80 for ".$orderResponse->sveaOrderId );
        $this->assertEquals("179.80", $query->numberedOrderRows[0]->amountIncVat);   // => 179,80        
        $this->assertEquals("24", $query->numberedOrderRows[0]->vatPercent);
        $this->assertEquals("80.00", $query->numberedOrderRows[1]->amountIncVat);    // => 80.00 // ok, pga incvat in till Svea hela vägen
        $this->assertEquals("24", $query->numberedOrderRows[1]->vatPercent);
        //print_r( $orderResponse->sveaOrderId );
    }


    public function test_add_multiple_orderRow_type_mismatch_created_inc_updated_ex()
    {
        $config = ConfigurationService::getDefaultConfig();
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

        $response = WebPayAdmin::addOrderRows($config)
            ->setCountryCode('SE')
            ->setOrderId($orderResponse->sveaOrderId)
            ->addOrderRow(WebPayItem::numberedOrderRow()
                ->setRowNumber(1)
                ->setAmountExVat(99.99)
                ->setVatPercent(24)
                ->setQuantity(1)
            )
            ->addInvoiceOrderRows()->doRequest();
        $this->assertEquals(1, $response->accepted);

        // query order and assert row totals
        $query2 = WebPayAdmin::queryOrder($config)
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query2->accepted);
        $this->assertEquals("123.99", $query2->numberedOrderRows[0]->amountIncVat);   // sent 99.99 ex * 1.24 => sent 123.9876 inc => 123.99 queried
        $this->assertEquals("24", $query2->numberedOrderRows[0]->vatPercent);
        $this->assertEquals("123.99", $query2->numberedOrderRows[1]->amountIncVat);   // sent 99.99 ex * 1.24 => sent 123.9876 inc => 123.99 queried
        $this->assertEquals("24", $query2->numberedOrderRows[1]->vatPercent);
    }
}
