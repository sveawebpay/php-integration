<?php

namespace Svea\WebPay\Test\IntegrationTest\BuildOrder;

use PHPUnit_Framework_TestCase;
use Svea\WebPay\BuildOrder\CreditOrderRowsBuilder;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\DistributionType;
use Svea\WebPay\Constant\PaymentMethod;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\WebPayItem;


/**
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class CreditOrderRowsBuilderIntegrationTest extends PHPUnit_Framework_TestCase
{

    protected $invoiceIdToTest;
    protected $country;

    protected function setUp()
    {
        $this->country = "SE";
        $this->invoiceIdToTest = 1028204;   // set this to the approved invoice set up by test_manual_setup_CreditOrderRows_testdata()
        $this->successfulTransactionToTest = 583628; // set to a card transaction w/status success, see test_manual_setup_CreditCardOrderRows_testdata
    }

    // CreditCardOrderRows    

    function test_manual_setup_CreditInvoiceOrderRows_testdata()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'test_manual_setup_CreditOrderRows_testdata -- run this first to setup order for CreditOrderRows tests to work with. 
            1. Run once, then make sure to log as ug 79021 and approve the invoice in the admin interface. 
            2. Set $this->invoiceIdToTest to the approved invoice id in setUp() above.
            3. Then uncomment and run CreditOrderRows tests below.'
        );

        // create order
        $order = TestUtil::createOrderWithoutOrderRows(TestUtil::createIndividualCustomer($this->country));
        $order->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("1")
            ->setQuantity(1)
            ->setAmountExVat(100.00)
            ->setVatPercent(25)
            ->setDescription("A Specification")
            ->setName('A Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );
        $order->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("2")
            ->setQuantity(1)
            ->setAmountExVat(100.00)
            ->setVatPercent(12)
            ->setDescription("B Specification")
            ->setName('B Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );
        $order->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("3")
            ->setQuantity(1)
            ->setAmountExVat(1.00)
            ->setVatPercent(25)
            ->setDescription("C Specification")
            ->setName('C Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );
        $order->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("4")
            ->setQuantity(1)
            ->setAmountExVat(100.00)
            ->setVatPercent(0)
            ->setDescription("D Specification")
            ->setName('D Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );
        $order->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("5")
            ->setQuantity(1)
            ->setAmountExVat(100.00)
            ->setVatPercent(0)
            ->setDescription("E Specification")
            ->setName('E Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );
        $orderResponse = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        // deliver order
        $deliver = WebPay::deliverOrder(ConfigurationService::getDefaultConfig());
        $deliver->setCountryCode($this->country)->setOrderId($orderResponse->sveaOrderId)->setInvoiceDistributionType(DistributionType::POST);
        $deliverResponse = $deliver->deliverInvoiceOrder()->doRequest();
        $this->assertEquals(1, $deliverResponse->accepted);

        //print_r("\ntest_manual_setup_CreditOrderRows_testdata finished, now approve the following invoice: ". $deliverResponse->invoiceId . "\n");

    }

    function test_CreditOrderRows_creditInvoiceOrderRows_single_setRowToCredit_success()
    {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'first set up approved invoice and enter id in setUp()'
        );

        $creditOrderRowsRequest = new CreditOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $creditOrderRowsResponse = $creditOrderRowsRequest
            ->setInvoiceId($this->invoiceIdToTest)
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode($this->country)
            ->setRowToCredit(1)
            ->creditInvoiceOrderRows()
            ->doRequest();

        //print_r("\ntest_CreditOrderRows_creditInvoiceOrderRows_single_row_success:\n");
        //print_r( $creditOrderRowsResponse );
        $this->assertEquals(1, $creditOrderRowsResponse->accepted);
        $this->assertEquals(-125.00, $creditOrderRowsResponse->amount);
    }

    function test_CreditOrderRows_creditInvoiceOrderRows_multiple_setRowsToCredit_success()
    {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'first set up approved invoice and enter id in setUp()'
        );

        $creditOrderRowsRequest = new CreditOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $creditOrderRowsResponse = $creditOrderRowsRequest
            ->setInvoiceId($this->invoiceIdToTest)
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode($this->country)
            ->setRowsToCredit(array(2, 3))
            ->creditInvoiceOrderRows()
            ->doRequest();

        //print_r("test_CreditOrderRows_creditInvoiceOrderRows_multiple_setRowsToCredit_success:\n");
        //print_r( $creditOrderRowsResponse );
        $this->assertEquals(1, $creditOrderRowsResponse->accepted);
        $this->assertEquals(-113.25, $creditOrderRowsResponse->amount);
    }

    function test_CreditOrderRows_creditInvoiceOrderRows_single_addCreditOrderRow_success()
    {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'first set up approved invoice and enter id in setUp()'
        );

        $creditOrderRowsRequest = new CreditOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $creditOrderRowsResponse = $creditOrderRowsRequest
            ->setInvoiceId($this->invoiceIdToTest)
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode($this->country)
            ->addCreditOrderRow(WebPayItem::orderRow()
                ->setArticleNumber("101")
                ->setQuantity(1)
                ->setAmountExVat(10.00)
                ->setVatPercent(25)
                ->setDescription("101 Specification")
                ->setName('101 Name')
                ->setUnit("st")
                ->setDiscountPercent(0)
            )
            ->creditInvoiceOrderRows()
            ->doRequest();

        //print_r("test_CreditOrderRows_creditInvoiceOrderRows_single_addCreditOrderRow_success:\n");
        //print_r( $creditOrderRowsResponse );
        $this->assertEquals(1, $creditOrderRowsResponse->accepted);
        $this->assertEquals(-12.50, $creditOrderRowsResponse->amount);
    }

    function test_CreditOrderRows_creditInvoiceOrderRows_multiple_addCreditOrderRow_success()
    {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'first set up approved invoice and enter id in setUp()'
        );

        $creditOrderRowsRequest = new CreditOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $creditOrderRowsResponse = $creditOrderRowsRequest
            ->setInvoiceId($this->invoiceIdToTest)
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode($this->country)
            ->addCreditOrderRow(WebPayItem::orderRow()
                ->setArticleNumber("101")
                ->setQuantity(1)
                ->setAmountExVat(10.00)
                ->setVatPercent(25)
                ->setDescription("101 Specification")
                ->setName('101 Name')
                ->setUnit("st")
                ->setDiscountPercent(0)
            )
            ->addCreditOrderRow(WebPayItem::orderRow()
                ->setArticleNumber("101")
                ->setQuantity(1)
                ->setAmountExVat(10.00)
                ->setVatPercent(25)
                ->setDescription("101 Specification")
                ->setName('101 Name')
                ->setUnit("st")
                ->setDiscountPercent(0)
            )
            ->creditInvoiceOrderRows()
            ->doRequest();

        //print_r("test_CreditOrderRows_creditInvoiceOrderRows_multiple_addCreditOrderRow_success:\n");
        //print_r( $creditOrderRowsResponse );
        $this->assertEquals(1, $creditOrderRowsResponse->accepted);
        $this->assertEquals(-25.00, $creditOrderRowsResponse->amount);
    }

    function test_CreditOrderRows_creditInvoiceOrderRows_addCreditOrderRow_and_setRowToCredit_success()
    {
        //  Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'first set up approved invoice and enter id in setUp()'
        );

        $creditOrderRowsRequest = new CreditOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $creditOrderRowsResponse = $creditOrderRowsRequest
            ->setInvoiceId($this->invoiceIdToTest)
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode($this->country)
            ->setRowToCredit(4)
            ->addCreditOrderRow(WebPayItem::orderRow()
                ->setArticleNumber("104")
                ->setQuantity(1)
                ->setAmountExVat(10.00)
                ->setVatPercent(25)
                ->setDescription("104 Specification")
                ->setName('104 Name')
                ->setUnit("st")
                ->setDiscountPercent(0)
            )
            ->creditInvoiceOrderRows()
            ->doRequest();

        //print_r("test_CreditOrderRows_creditInvoiceOrderRows_addCreditOrderRow_and_setRowToCredit_success:\n");
        //print_r( $creditOrderRowsResponse );
        $this->assertEquals(1, $creditOrderRowsResponse->accepted);
        $this->assertEquals(-112.50, $creditOrderRowsResponse->amount);
    }


    function test_CreditOrderRows_creditInvoiceOrderRows_credit_amount_exceeds_original_order_fails()
    {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'first set up approved invoice and enter id in setUp()'
        );

        $creditOrderRowsRequest = new CreditOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $creditOrderRowsResponse = $creditOrderRowsRequest
            ->setInvoiceId($this->invoiceIdToTest)
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode($this->country)
            ->setRowToCredit(5)
            ->creditInvoiceOrderRows()
            ->doRequest();

        //print_r("test_CreditOrderRows_creditInvoiceOrderRows_credit_amount_exceeds_original_order_fails:\n");
        //print_r( $creditOrderRowsResponse );
        $this->assertEquals(0, $creditOrderRowsResponse->accepted);
        $this->assertEquals(24502, $creditOrderRowsResponse->resultcode);
        $this->assertEquals("Credit amount exceeds invoiced amount", $creditOrderRowsResponse->errormessage);
    }

    // CreditCardOrderRows

    function test_manual_setup_CreditCardOrderRows_testdata()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            '1. test_manual_setup_CreditCardOrderRows_testdata -- run this first to setup order for CreditOrderRows tests to work with. 
            Run once, then make sure to approve the invoice in the admin interface. Then uncomment and run CreditOrderRows tests.               
             
            2. verktyg / confirm, merchant 1130, use this xml w/correct transactionid, todays date => status = CONFIRMED         
            <confirm>
            <transactionid>583004</transactionid>
            <capturedate>2014-06-02</capturedate>
            </confirm>
            
            3. schemalagda jobb / dailycapture kortcert task => status = SUCCESS'

        );

        $orderLanguage = "sv";
        $returnUrl = "http://127.0.0.1";
        $ipAddress = "127.0.0.1";

        // create order
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->setCountryCode($this->country)
            ->setCurrency("SEK")
            ->setCustomerReference("CreditCardOrderRows_testdata" . date('c'))
            ->setClientOrderNumber("CreditCardOrderRows_testdata" . date('c'))
            ->setOrderDate(date('c'));

        $order->addCustomerDetails(
            WebPayItem::individualCustomer()
                ->setNationalIdNumber("194605092222")
                ->setBirthDate(1946, 05, 9)
                ->setName("Tess T", "Persson")
                ->setStreetAddress("Testgatan", 1)
                ->setCoAddress("c/o Eriksson, Erik")
                ->setLocality("Stan")
                ->setZipCode("99999")
                ->setIpAddress($ipAddress)
        );

        $order->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("1")
            ->setQuantity(1)
            ->setAmountExVat(100.00)
            ->setVatPercent(25)
            ->setDescription("A Specification")
            ->setName('A Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );
        $order->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("2")
            ->setQuantity(1)
            ->setAmountExVat(100.00)
            ->setVatPercent(12)
            ->setDescription("B Specification")
            ->setName('B Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );
        $order->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("3")
            ->setQuantity(1)
            ->setAmountExVat(1.00)
            ->setVatPercent(25)
            ->setDescription("C Specification")
            ->setName('C Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );
        $order->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("4")
            ->setQuantity(1)
            ->setAmountExVat(100.00)
            ->setVatPercent(0)
            ->setDescription("D Specification")
            ->setName('D Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );
        $order->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("5")
            ->setQuantity(1)
            ->setAmountExVat(100.00)
            ->setVatPercent(0)
            ->setDescription("E Specification")
            ->setName('E Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );

        $orderResponse = $order
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setPayPageLanguage($orderLanguage)
            ->setReturnUrl($returnUrl)
            ->getPaymentUrl();

        //print_r( $orderResponse );
        $this->assertEquals(1, $orderResponse->accepted);

        //print_r( "test_manual_setup_CreditCardOrderRows_testdata finished, now go to " . $orderResponse->testurl ." and complete payment.\n" );
    }

    function test_CreditOrderRows_CreditCardOrderRows_credit_single_row_using_addNumberedOrderRows_setRowToCredit_success()
    {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'first set up confirmed transaction and enter id in setUp()'
        );

        // query orderrows to pass in creditOrderRows->setNumberedOrderRows()
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($this->successfulTransactionToTest)
            ->setCountryCode($this->country);

        $queryResponse = $queryOrderBuilder->queryCardOrder()->doRequest();

        //print_r( $queryResponse );
        $this->assertEquals(1, $queryResponse->accepted);

        $creditOrderRowsBuilder = new CreditOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $creditOrderRowsRequest = $creditOrderRowsBuilder
            ->setOrderId($this->successfulTransactionToTest)
            ->setCountryCode($this->country)
            ->setRowToCredit(1)
            ->addNumberedOrderRows($queryResponse->numberedOrderRows)// use the queried order rows as base for what amount to credit
            ->creditCardOrderRows();
        $creditOrderRowsResponse = $creditOrderRowsRequest->doRequest();

        //print_r("test_CreditOrderRows_CreditCardOrderRows_credit_single_row_using_addNumberedOrderRows_setRowToCredit_success:\n");
        //print_r( $creditOrderRowsResponse );

        $this->assertEquals(1, $creditOrderRowsResponse->accepted);

        // query orderrows again
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($this->successfulTransactionToTest)
            ->setCountryCode($this->country);
        $queryResponse = $queryOrderBuilder->queryCardOrder()->doRequest();

        $this->assertEquals(1, $queryResponse->accepted);
        //print_r( $queryResponse );
        // credit 100 @25 *100 = 12500 => 12500
        $this->assertEquals(12500, $queryResponse->creditedamount);
    }

    function test_CreditOrderRows_CreditCardOrderRows_credit_multiple_rows_using_addNumberedOrderRows_setRowToCredit_success()
    {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'first set up confirmed transaction and enter id in setUp()'
        );

        // query orderrows to pass in creditOrderRows->setNumberedOrderRows()
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($this->successfulTransactionToTest)
            ->setCountryCode($this->country);

        $queryResponse = $queryOrderBuilder->queryCardOrder()->doRequest();

        //print_r( $queryResponse );
        $this->assertEquals(1, $queryResponse->accepted);

        $creditOrderRowsBuilder = new CreditOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $creditOrderRowsRequest = $creditOrderRowsBuilder
            ->setOrderId($this->successfulTransactionToTest)
            ->setCountryCode($this->country)
            ->setRowsToCredit(array(2, 3))
            ->addNumberedOrderRows($queryResponse->numberedOrderRows)// use the queried order rows as base for what amount to credit
            ->creditCardOrderRows();
        $creditOrderRowsResponse = $creditOrderRowsRequest->doRequest();

        //print_r("test_CreditOrderRows_CreditCardOrderRows_credit_multiple_rows_using_addNumberedOrderRows_setRowToCredit_success:\n");
        //print_r( $creditOrderRowsResponse );

        $this->assertEquals(1, $creditOrderRowsResponse->accepted);

        // query orderrows again
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($this->successfulTransactionToTest)
            ->setCountryCode($this->country);
        $queryResponse = $queryOrderBuilder->queryCardOrder()->doRequest();

        $this->assertEquals(1, $queryResponse->accepted);
        //print_r( $queryResponse );
        // credited               12500   
        // credit 100 @12 *100 =  11200
        // credit   1 @25 *100 =    125 => 23825
        $this->assertEquals(23825, $queryResponse->creditedamount);
    }

    function test_CreditOrderRows_CreditCardOrderRows_credit_single_row_and_new_row_using_addNewCreditRow_success()
    {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'first set up confirmed transaction and enter id in setUp()'
        );

        // query orderrows to pass in creditOrderRows->setNumberedOrderRows()
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($this->successfulTransactionToTest)
            ->setCountryCode($this->country);

        $queryResponse = $queryOrderBuilder->queryCardOrder()->doRequest();

        //print_r( $queryResponse );
        $this->assertEquals(1, $queryResponse->accepted);

        $creditOrderRowsBuilder = new CreditOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $creditOrderRowsRequest = $creditOrderRowsBuilder
            ->setOrderId($this->successfulTransactionToTest)
            ->setCountryCode($this->country)
            ->addNumberedOrderRows($queryResponse->numberedOrderRows)// use the queried order rows as base for what amount to credit
            ->setRowToCredit(4)
            ->addCreditOrderRow(WebPayItem::orderRow()
                ->setArticleNumber("104")
                ->setQuantity(1)
                ->setAmountExVat(10.00)
                ->setVatPercent(25)
                ->setDescription("104 Specification")
                ->setName('104 Name')
                ->setUnit("st")
                ->setDiscountPercent(0)
            )
            ->creditCardOrderRows();
        $creditOrderRowsResponse = $creditOrderRowsRequest->doRequest();

        //print_r("test_CreditOrderRows_CreditCardOrderRows_credit_single_row_and_new_row_using_addNewCreditRow_success:\n");
        //print_r( $creditOrderRowsResponse );

        $this->assertEquals(1, $creditOrderRowsResponse->accepted);

        // query orderrows again
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($this->successfulTransactionToTest)
            ->setCountryCode($this->country);
        $queryResponse = $queryOrderBuilder->queryCardOrder()->doRequest();

        $this->assertEquals(1, $queryResponse->accepted);
        //print_r( $queryResponse );
        // credited               23825   
        // credit 100 @0 *100 =   10000
        // credit  10 @25 *100 =   1250 => 35075
        $this->assertEquals(35075, $queryResponse->creditedamount);
    }

    function test_CreditOrderRows_creditCardOrderRows_addCreditOrderRow_setRowToCredit_exceeds_original_order_fails()
    {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'first set up approved invoice and enter id in setUp()'
        );

        // query orderrows to pass in creditOrderRows->setNumberedOrderRows()
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($this->successfulTransactionToTest)
            ->setCountryCode($this->country);
        $queryResponse = $queryOrderBuilder->queryCardOrder()->doRequest();

        //print_r( $queryResponse );
        $this->assertEquals(1, $queryResponse->accepted);

        $creditOrderRowsBuilder = new CreditOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $creditOrderRowsRequest = $creditOrderRowsBuilder
            ->setOrderId($this->successfulTransactionToTest)
            ->setCountryCode($this->country)
            ->addNumberedOrderRows($queryResponse->numberedOrderRows)// use the queried order rows as base for what amount to credit
            ->setRowToCredit(5)
            ->creditCardOrderRows();
        $creditOrderRowsResponse = $creditOrderRowsRequest->doRequest();

        //print_r("test_CreditOrderRows_creditCardOrderRows_addCreditOrderRow_setRowToCredit_exceeds_original_order_fails:\n");
        //print_r( $creditOrderRowsResponse );        
        $this->assertEquals(0, $creditOrderRowsResponse->accepted);
        $this->assertEquals("119 (ILLEGAL_CREDITED_AMOUNT)", $creditOrderRowsResponse->resultcode);
    }
}

?>