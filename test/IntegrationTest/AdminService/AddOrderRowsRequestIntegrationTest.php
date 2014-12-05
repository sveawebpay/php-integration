<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class AddOrderRowsRequestIntegrationTest extends PHPUnit_Framework_TestCase{

    public $builderObject;

    public function setUp() {
        $this->builderObject = new Svea\OrderBuilder( Svea\SveaConfig::getDefaultConfig());
        $this->builderObject->orderId = 123456;
        $this->builderObject->orderType = \ConfigurationProvider::INVOICE_TYPE;
        $this->builderObject->countryCode = "SE";
        $this->builderObject->orderRows = array( TestUtil::createOrderRow(10.00) );
    }

    public function test_add_single_orderRow() {
        $config = Svea\SveaConfig::getDefaultConfig();
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
                    ->useInvoicePayment()
                        ->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        // add order rows to builderobject
        $this->builderObject->orderRows[] = TestUtil::createOrderRow( 1.00, 1 );
        $this->builderObject->orderId = $orderResponse->sveaOrderId;

        $addOrderRowsRequest = new Svea\AdminService\AddOrderRowsRequest( $this->builderObject );
        $addOrderRowsResponse = $addOrderRowsRequest->doRequest();

        $this->assertInstanceOf('Svea\AdminService\AddOrderRowsResponse', $addOrderRowsResponse);
        $this->assertEquals(1, $addOrderRowsResponse->accepted );
    }
    public function test_dd_single_orderRow_with_vat_match() {
      $config = Svea\SveaConfig::getDefaultConfig();
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
                    ->useInvoicePayment()
                        ->doRequest();

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
        $this->assertEquals(1, $response->accepted );
    }
    public function test_add_single_orderRow_with_vat_missmatch1() {
        $config = Svea\SveaConfig::getDefaultConfig();
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
                    ->useInvoicePayment()
                        ->doRequest();
//        $this->assertEquals(1, $orderResponse->accepted);

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
                ->addInvoiceOrderRows()
                    ->doRequest();
        $this->assertEquals(1, $response->accepted );
    }
    public function test_add_single_orderRow_with_vat_missmatch2() {
         $config = Svea\SveaConfig::getDefaultConfig();
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
//                print_r($orderResponse->sveaOrderId);
//        $this->assertEquals(1, $orderResponse->accepted);

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

        $this->assertEquals(1, $response->accepted );
    }
}
