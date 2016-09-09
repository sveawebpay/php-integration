<?php

namespace Svea\WebPay\Test\IntegrationTest\AdminService;

use PHPUnit_Framework_TestCase;
use Svea\WebPay\AdminService\GetOrdersRequest;
use Svea\WebPay\BuildOrder\QueryOrderBuilder;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\WebPayItem;


/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class GetOrdersRequestIntegrationTest extends PHPUnit_Framework_TestCase
{

    /**
     * 1. create an Invoice|PaymentPlan order
     * 2. note the client credentials, order number and type, and insert below
     * 3. run the test
     */
    public function test_manual_GetOrdersRequest_for_invoice_individual_customer_order()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(  // 150626 -- removed due to corrupt customerids w/no ssn in test database (known error)
            'skeleton for test_manual_GetOrdersRequest_for_invoice_individual_customer_order'
        );

        $countryCode = "SE";
        $sveaOrderIdToGet = 348629;
        $orderType = ConfigurationProvider::INVOICE_TYPE;

        $getOrdersBuilder = new QueryOrderBuilder(ConfigurationService::getDefaultConfig());
        $getOrdersBuilder->setOrderId($sveaOrderIdToGet);
        $getOrdersBuilder->setCountryCode($countryCode);
        $getOrdersBuilder->orderType = $orderType;

        // Example of test_manual_GetOrdersRequest_for_invoice_individual_customer_order 348629 raw request response to parse:
        //        stdClass Object
        //        (
        //            [ErrorMessage] =>
        //            [ResultCode] => 0
        //            [Orders] => stdClass Object
        //                (
        //                    [Order] => stdClass Object
        //                        (
        //                            [ChangedDate] =>
        //                            [ClientId] => 79021
        //                            [ClientOrderId] => 449
        //                            [CreatedDate] => 2014-05-19T16:04:54.787
        //                            [CreditReportStatus] => stdClass Object
        //                                (
        //                                    [Accepted] => true
        //                                    [CreationDate] => 2014-05-19T16:04:54.893
        //                                )
        //
        //                            [Currency] => SEK
        //                            [Customer] => stdClass Object
        //                                (
        //                                    [CoAddress] => c/o Eriksson, Erik
        //                                    [CompanyIdentity] =>
        //                                    [CountryCode] => SE
        //                                    [CustomerType] => Individual
        //                                    [Email] => test@svea.com
        //                                    [FullName] => Persson, Tess T
        //                                    [HouseNumber] =>
        //                                    [IndividualIdentity] => stdClass Object
        //                                        (
        //                                            [BirthDate] =>
        //                                            [FirstName] =>
        //                                            [Initials] =>
        //                                            [LastName] =>
        //                                        )
        //
        //                                    [Locality] => Stan
        //                                    [NationalIdNumber] => 194605092222
        //                                    [PhoneNumber] => 999999
        //                                    [PublicKey] =>
        //                                    [Street] => Testgatan 1
        //                                    [ZipCode] => 99999
        //                                )
        //
        //                            [CustomerId] => 1000117
        //                            [CustomerReference] =>
        //                            [DeliveryAddress] =>
        //                            [IsPossibleToAdminister] => false
        //                            [IsPossibleToCancel] => true
        //                            [Notes] =>
        //                            [OrderDeliveryStatus] => Created
        //                            [OrderRows] => stdClass Object
        //                                (
        //                                    [NumberedOrderRow] => Array
        //                                        (
        //                                            [0] => stdClass Object
        //                                                (
        //                                                    [ArticleNumber] =>
        //                                                    [Description] => Dyr produkt 25%
        //                                                    [DiscountPercent] => 0.00
        //                                                    [NumberOfUnits] => 2.00
        //                                                    [PricePerUnit] => 2000.00
        //                                                    [Unit] =>
        //                                                    [VatPercent] => 25.00
        //                                                    [CreditInvoiceId] =>
        //                                                    [InvoiceId] =>
        //                                                    [RowNumber] => 1
        //                                                    [Status] => NotDelivered
        //                                                )
        //
        //                                            [1] => stdClass Object
        //                                                (
        //                                                    [ArticleNumber] =>
        //                                                    [Description] => Testprodukt 1kr 25%
        //                                                    [DiscountPercent] => 0.00
        //                                                    [NumberOfUnits] => 1.00
        //                                                    [PricePerUnit] => 1.00
        //                                                    [Unit] =>
        //                                                    [VatPercent] => 25.00
        //                                                    [CreditInvoiceId] =>
        //                                                    [InvoiceId] =>
        //                                                    [RowNumber] => 2
        //                                                    [Status] => NotDelivered
        //                                                )
        //
        //                                            [2] => stdClass Object
        //                                                (
        //                                                    [ArticleNumber] =>
        //                                                    [Description] => Fastpris (Fast fraktpris)
        //                                                    [DiscountPercent] => 0.00
        //                                                    [NumberOfUnits] => 1.00
        //                                                    [PricePerUnit] => 4.00
        //                                                    [Unit] =>
        //                                                    [VatPercent] => 25.00
        //                                                    [CreditInvoiceId] =>
        //                                                    [InvoiceId] =>
        //                                                    [RowNumber] => 3
        //                                                    [Status] => NotDelivered
        //                                                )
        //
        //                                            [3] => stdClass Object
        //                                                (
        //                                                    [ArticleNumber] =>
        //                                                    [Description] => Svea Fakturaavgift:: 20.00kr (SE)
        //                                                    [DiscountPercent] => 0.00
        //                                                    [NumberOfUnits] => 1.00
        //                                                    [PricePerUnit] => 20.00
        //                                                    [Unit] =>
        //                                                    [VatPercent] => 0.00
        //                                                    [CreditInvoiceId] =>
        //                                                    [InvoiceId] =>
        //                                                    [RowNumber] => 4
        //                                                    [Status] => NotDelivered
        //                                                )
        //
        //                                        )
        //
        //                                )
        //
        //                            [OrderStatus] => Active
        //                            [OrderType] => Invoice
        //                            [PaymentPlanDetails] =>
        //                            [PendingReasons] =>
        //                            [SveaOrderId] => 348629
        //                            [SveaWillBuy] => true
        //                        )
        //
        //                )
        //
        //        )

        $request = new GetOrdersRequest($getOrdersBuilder);
        $getOrdersResponse = $request->doRequest();

//        print_r( $getOrdersResponse );

        $this->assertInstanceOf('Svea\WebPay\AdminService\AdminServiceResponse\GetOrdersResponse', $getOrdersResponse);
        $this->assertEquals(1, $getOrdersResponse->accepted);
        $this->assertEquals(0, $getOrdersResponse->resultcode);
        $this->assertEquals(null, $getOrdersResponse->errormessage);

        $this->assertEquals(null, $getOrdersResponse->changedDate);  // TODO add test for changed order later
        $this->assertEquals(79021, $getOrdersResponse->clientId);
        $this->assertEquals(449, $getOrdersResponse->clientOrderId);
        $this->assertEquals("2014-05-19T16:04:54.787", $getOrdersResponse->createdDate);

        $this->assertEquals(true, $getOrdersResponse->creditReportStatusAccepted);
        $this->assertEquals("2014-05-19T16:04:54.893", $getOrdersResponse->creditReportStatusCreationDate);

        $this->assertEquals("SEK", $getOrdersResponse->currency);

        $this->assertInstanceOf("Svea\WebPay\BuildOrder\RowBuilders\IndividualCustomer", $getOrdersResponse->customer);
        $this->assertEquals("194605092222", $getOrdersResponse->customer->ssn);
        $this->assertEquals(null, $getOrdersResponse->customer->initials);
        $this->assertEquals(null, $getOrdersResponse->customer->birthDate);
        $this->assertEquals(null, $getOrdersResponse->customer->firstname);
        $this->assertEquals(null, $getOrdersResponse->customer->lastname);
        //$this->assertEquals( "test@svea.com", $getOrdersResponse->customer->email );  // -- returns current customer stats, may change
        //$this->assertEquals( null, $getOrdersResponse->customer->phonenumber ); // -- returns current customer stats, may change
        $this->assertEquals("Persson, Tess T", $getOrdersResponse->customer->name);   // FullName
        $this->assertEquals("Testgatan 1", $getOrdersResponse->customer->streetAddress);
        $this->assertEquals("Testgatan 1", $getOrdersResponse->customer->street);
        $this->assertEquals("c/o Eriksson, Erik", $getOrdersResponse->customer->coAddress);
        $this->assertEquals("99999", $getOrdersResponse->customer->zipCode);
        $this->assertEquals("Stan", $getOrdersResponse->customer->locality);

        $this->assertEquals("1000117", $getOrdersResponse->customerId);
        $this->assertEquals(null, $getOrdersResponse->customerReference);
        $this->assertClassNotHasAttribute("deliveryAddress", "\Svea\AdminService\GetOrdersResponse"); // deliveryAddress field is not supported
        $this->assertEquals(false, $getOrdersResponse->isPossibleToAdminister);
        $this->assertEquals(true, $getOrdersResponse->isPossibleToCancel);
        $this->assertEquals(null, $getOrdersResponse->notes);
        $this->assertEquals("Created", $getOrdersResponse->orderDeliveryStatus);

        $this->assertInstanceOf("Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow", $getOrdersResponse->numberedOrderRows[0]);
        $this->assertEquals(1, $getOrdersResponse->numberedOrderRows[0]->rowNumber);
        $this->assertEquals(null, $getOrdersResponse->numberedOrderRows[0]->articleNumber);
        $this->assertEquals(2.00, $getOrdersResponse->numberedOrderRows[0]->quantity);
        $this->assertEquals(null, $getOrdersResponse->numberedOrderRows[0]->unit);
        $this->assertEquals(2000.00, $getOrdersResponse->numberedOrderRows[0]->amountExVat);
        $this->assertEquals(25.00, $getOrdersResponse->numberedOrderRows[0]->vatPercent);
        $this->assertEquals(null, $getOrdersResponse->numberedOrderRows[0]->name);
        $this->assertEquals("Dyr produkt 25%", $getOrdersResponse->numberedOrderRows[0]->description);
        $this->assertEquals(0, $getOrdersResponse->numberedOrderRows[0]->vatDiscount);

        // only check attributes of first row
        $this->assertInstanceOf("Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow", $getOrdersResponse->numberedOrderRows[3]);
        $this->assertEquals(4, $getOrdersResponse->numberedOrderRows[3]->rowNumber);

        $this->assertEquals("Active", $getOrdersResponse->orderStatus);
        $this->assertEquals("Invoice", $getOrdersResponse->orderType);
        $this->assertEquals(null, $getOrdersResponse->paymentPlanDetailsContractLengthMonths);
        $this->assertEquals(null, $getOrdersResponse->paymentPlanDetailsContractNumber);
        $this->assertEquals(null, $getOrdersResponse->pendingReasons);
        $this->assertEquals(348629, $getOrdersResponse->orderId);
        $this->assertEquals(true, $getOrdersResponse->sveaWillBuy);
    }

    public function test_GetOrdersRequest_for_invoice_sets_individual_customer_correctly()
    {
        // create order
        $country = "SE";
        $order = TestUtil::createOrder(TestUtil::createIndividualCustomer($country));
        //case( "SE" ):
        //    return Svea\WebPay\WebPayItem::individualCustomer()
        //        ->setNationalIdNumber("194605092222")
        //        ->setBirthDate(1946, 05, 09)
        //        ->setName("Tess T", "Persson")
        //        ->setStreetAddress("Testgatan", 1)
        //        ->setCoAddress("c/o Eriksson, Erik")
        //        ->setLocality("Stan")
        //        ->setZipCode("99999");
        //    break;
        $order->addOrderRow(TestUtil::createOrderRow(1000.00));
        $orderResponse = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $countryCode = "SE";
        $sveaOrderIdToGet = $orderResponse->sveaOrderId;
        $orderType = ConfigurationProvider::INVOICE_TYPE;

        $getOrdersBuilder = new QueryOrderBuilder(ConfigurationService::getDefaultConfig());
        $getOrdersBuilder->setOrderId($sveaOrderIdToGet);
        $getOrdersBuilder->setCountryCode($countryCode);
        $getOrdersBuilder->orderType = $orderType;

        $request = new GetOrdersRequest($getOrdersBuilder);
        $getOrdersResponse = $request->doRequest();

        // Example test_GetOrdersRequest_for_invoice_company_customer_order raw request response
        //
        //stdClass Object
        //(
        //      /.../
        //                    [Customer] => stdClass Object
        //                        (
        //                            [CoAddress] => c/o Eriksson, Erik
        //                            [CompanyIdentity] =>
        //                            [CountryCode] => SE
        //                            [CustomerType] => Individual
        //                            [Email] =>
        //                            [FullName] => Persson, Tess T
        //                            [HouseNumber] =>
        //                            [IndividualIdentity] => stdClass Object
        //                                (
        //                                    [BirthDate] =>
        //                                    [FirstName] =>
        //                                    [Initials] =>
        //                                    [LastName] =>
        //                                )
        //
        //                            [Locality] => Stan
        //                            [NationalIdNumber] => 194605092222
        //                            [PhoneNumber] =>
        //                            [PublicKey] =>
        //                            [Street] => Testgatan 1
        //                            [ZipCode] => 99999
        //                        )
        //      /.../
        // )

        $this->assertInstanceOf('Svea\WebPay\AdminService\AdminServiceResponse\GetOrdersResponse', $getOrdersResponse);
        $this->assertEquals(1, $getOrdersResponse->accepted);
        $this->assertEquals(0, $getOrdersResponse->resultcode);
        $this->assertEquals(null, $getOrdersResponse->errormessage);

        $this->assertInstanceOf("Svea\WebPay\BuildOrder\RowBuilders\IndividualCustomer", $getOrdersResponse->customer);
        $this->assertEquals("194605092222", $getOrdersResponse->customer->ssn);
        $this->assertEquals(null, $getOrdersResponse->customer->initials);
        $this->assertEquals(null, $getOrdersResponse->customer->birthDate);
        $this->assertEquals(null, $getOrdersResponse->customer->firstname);           // not set for SE order
        $this->assertEquals(null, $getOrdersResponse->customer->lastname);
        //$this->assertEquals( null, $getOrdersResponse->customer->email );
        //$this->assertEquals( null, $getOrdersResponse->customer->phonenumber );
        $this->assertEquals("Persson, Tess T", $getOrdersResponse->customer->name);   // FullName
        $this->assertEquals("Testgatan 1", $getOrdersResponse->customer->streetAddress);
        $this->assertEquals("Testgatan 1", $getOrdersResponse->customer->street);
        $this->assertEquals("c/o Eriksson, Erik", $getOrdersResponse->customer->coAddress);
        $this->assertEquals("99999", $getOrdersResponse->customer->zipCode);
        $this->assertEquals("Stan", $getOrdersResponse->customer->locality);
    }

    public function test_GetOrdersRequest_for_invoice_sets_company_customer_correctly()
    {
        // create order
        $country = "SE";
        $order = TestUtil::createOrder(TestUtil::createCompanyCustomer($country));
        //case( "SE" ):
        //    return Svea\WebPay\WebPayItem::companyCustomer()
        //        ->setNationalIdNumber("4608142222")
        //        ->setCompanyName("Tess T", "Persson")
        //        ->setStreetAddress("Testgatan", 1)
        //        ->setCoAddress("c/o Eriksson, Erik")
        //        ->setLocality("Stan")
        //        ->setZipCode("99999");
        //    break;
        $order->addOrderRow(TestUtil::createOrderRow(1000.00));
        $orderResponse = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $countryCode = "SE";
        $sveaOrderIdToGet = $orderResponse->sveaOrderId;
        $orderType = ConfigurationProvider::INVOICE_TYPE;

        $getOrdersBuilder = new QueryOrderBuilder(ConfigurationService::getDefaultConfig());
        $getOrdersBuilder->setOrderId($sveaOrderIdToGet);
        $getOrdersBuilder->setCountryCode($countryCode);
        $getOrdersBuilder->orderType = $orderType;

        $request = new GetOrdersRequest($getOrdersBuilder);
        $getOrdersResponse = $request->doRequest();

        // Example test_GetOrdersRequest_for_invoice_company_customer_order raw request response
        //
        //stdClass Object
        //(
        //      /.../
        //                    [Customer] => stdClass Object
        //                        (
        //                            [CoAddress] => c/o Eriksson, Erik
        //                            [CompanyIdentity] => stdClass Object
        //                                (
        //                                    [CompanyIdentification] =>
        //                                    [CompanyVatNumber] =>
        //                                )
        //
        //                            [CountryCode] => SE
        //                            [CustomerType] => Company
        //                            [Email] =>
        //                            [FullName] => Persson, Tess T
        //                            [HouseNumber] =>
        //                            [IndividualIdentity] =>
        //                            [Locality] => Stan
        //                            [NationalIdNumber] => 164608142222
        //                            [PhoneNumber] =>
        //                            [PublicKey] =>
        //                            [Street] => Testgatan 1
        //                            [ZipCode] => 99999
        //                        )
        //      /.../
        // )

        ////print_r( $getOrdersResponse );
        $this->assertInstanceOf('Svea\WebPay\AdminService\AdminServiceResponse\GetOrdersResponse', $getOrdersResponse);
        $this->assertEquals(1, $getOrdersResponse->accepted);
        $this->assertEquals(0, $getOrdersResponse->resultcode);
        $this->assertEquals(null, $getOrdersResponse->errormessage);

        $this->assertInstanceOf("Svea\WebPay\BuildOrder\RowBuilders\CompanyCustomer", $getOrdersResponse->customer);
        $this->assertEquals("194608142222", $getOrdersResponse->customer->orgNumber);
        $this->assertEquals(null, $getOrdersResponse->customer->companyVatNumber);
        $this->assertEquals("Persson, Tess T", $getOrdersResponse->customer->companyName);
        $this->assertEquals(null, $getOrdersResponse->customer->email);
        $this->assertEquals(null, $getOrdersResponse->customer->phonenumber);
        $this->assertEquals("Testgatan 1", $getOrdersResponse->customer->streetAddress);
        $this->assertEquals("Testgatan 1", $getOrdersResponse->customer->street);
        $this->assertEquals("c/o Eriksson, Erik", $getOrdersResponse->customer->coAddress);
        $this->assertEquals("99999", $getOrdersResponse->customer->zipCode);
        $this->assertEquals("Stan", $getOrdersResponse->customer->locality);
    }

    public function test_manual_GetOrdersRequest_for_paymentplan_order()
    {

        // Stop here and mark this test as incomplete.
//    $this->markTestIncomplete(
//        'skeleton for test_manual_GetOrdersRequest_for_paymentplan_order'
//    );

        // create order
        $country = "SE";
        //        $order = Svea\WebPay\Test\TestUtil::createOrder( Svea\WebPay\Test\TestUtil::createIndividualCustomer($country) );
        //        $order->addOrderRow( Svea\WebPay\Test\TestUtil::createOrderRow( 1000.00 ) );
        //        $orderResponse = $order->usePaymentPlanPayment( Svea\WebPay\Test\TestUtil::getGetPaymentPlanParamsForTesting($country) )->doRequest();
        //        $this->assertEquals(1, $orderResponse->accepted);
        //
        $getOrdersBuilder = new QueryOrderBuilder(ConfigurationService::getDefaultConfig());
        //$getOrdersBuilder->setOrderId($orderResponse->sveaOrderId);
        $getOrdersBuilder->setOrderId(414812);
        $getOrdersBuilder->setCountryCode($country);
        $getOrdersBuilder->orderType = ConfigurationProvider::PAYMENTPLAN_TYPE;

        $request = new GetOrdersRequest($getOrdersBuilder);
        $getOrdersResponse = $request->doRequest();

        // Example test_GetOrdersRequest_for_invoice_company_customer_order raw request response
        //
        // stdClass Object
        //(
        //    [ErrorMessage] =>
        //    [ResultCode] => 0
        //    [Orders] => stdClass Object
        //        (
        //            [Order] => stdClass Object
        //                (
        //                    [ChangedDate] =>
        //                    [ClientId] => 59999
        //                    [ClientOrderId] => clientOrderNumber:2014-09-11T17:57:07+02:00
        //                    [CreatedDate] => 2014-09-11T17:57:08.777
        //                    [CreditReportStatus] => stdClass Object
        //                        (
        //                            [Accepted] => true
        //                            [CreationDate] => 2014-09-11T17:57:08.87
        //                        )
        //
        //                    [Currency] => SEK
        //                    [Customer] => stdClass Object
        //                        (
        //                            [CoAddress] => c/o Eriksson, Erik
        //                            [CompanyIdentity] =>
        //                            [CountryCode] => SE
        //                            [CustomerType] => Individual
        //                            [Email] =>
        //                            [FullName] => Persson, Tess T
        //                            [HouseNumber] =>
        //                            [IndividualIdentity] => stdClass Object
        //                                (
        //                                    [BirthDate] =>
        //                                    [FirstName] =>
        //                                    [Initials] =>
        //                                    [LastName] =>
        //                                )
        //
        //                            [Locality] => Stan
        //                            [NationalIdNumber] => 194605092222
        //                            [PhoneNumber] =>
        //                            [PublicKey] =>
        //                            [Street] => Testgatan 1
        //                            [ZipCode] => 99999
        //                        )
        //
        //                    [CustomerId] => 1000013
        //                    [CustomerReference] => created by Svea\WebPay\Test\TestUtil::createOrder()
        //                    [DeliveryAddress] =>
        //                    [IsPossibleToAdminister] => false
        //                    [IsPossibleToCancel] => true
        //                    [Notes] =>
        //                    [OrderDeliveryStatus] => Created
        //                    [OrderRows] => stdClass Object
        //                        (
        //                            [NumberedOrderRow] => Array
        //                                (
        //                                    [0] => stdClass Object
        //                                        (
        //                                            [ArticleNumber] => 1
        //                                            [Description] => Product: Specification
        //                                            [DiscountPercent] => 0.00
        //                                            [NumberOfUnits] => 2.00
        //                                            [PricePerUnit] => 100.00
        //                                            [Unit] => st
        //                                            [VatPercent] => 25.00
        //                                            [CreditInvoiceId] =>
        //                                            [InvoiceId] =>
        //                                            [RowNumber] => 1
        //                                            [Status] => NotDelivered
        //                                        )
        //
        //                                    [1] => stdClass Object
        //                                        (
        //                                            [ArticleNumber] => 1
        //                                            [Description] => Product: Specification
        //                                            [DiscountPercent] => 0.00
        //                                            [NumberOfUnits] => 2.00
        //                                            [PricePerUnit] => 1000.00
        //                                            [Unit] => st
        //                                            [VatPercent] => 25.00
        //                                            [CreditInvoiceId] =>
        //                                            [InvoiceId] =>
        //                                            [RowNumber] => 2
        //                                            [Status] => NotDelivered
        //                                        )
        //
        //                                )
        //
        //                        )
        //
        //                    [OrderStatus] => Active
        //                    [OrderType] => PaymentPlan
        //                    [PaymentPlanDetails] => stdClass Object
        //                        (
        //                            [ContractLengthMonths] => 3
        //                            [ContractNumber] =>
        //                        )
        //
        //                    [PendingReasons] =>
        //                    [SveaOrderId] => 414812
        //                    [SveaWillBuy] => true
        //                )
        //
        //        )
        //
        //)

        ////print_r( $getOrdersResponse );
        $this->assertInstanceOf('Svea\WebPay\AdminService\AdminServiceResponse\GetOrdersResponse', $getOrdersResponse);
        $this->assertEquals(1, $getOrdersResponse->accepted);
        $this->assertEquals(0, $getOrdersResponse->resultcode);
        $this->assertEquals(null, $getOrdersResponse->errormessage);

        $this->assertEquals(null, $getOrdersResponse->changedDate);  // TODO add test for changed order later
        $this->assertEquals(59999, $getOrdersResponse->clientId);
        $this->assertEquals("clientOrderNumber:2014-09-11T17:57:07+02:00", $getOrdersResponse->clientOrderId);
        $this->assertEquals("2014-09-11T17:57:08.777", $getOrdersResponse->createdDate);

        $this->assertEquals(true, $getOrdersResponse->creditReportStatusAccepted);
        $this->assertEquals("2014-09-11T17:57:08.87", $getOrdersResponse->creditReportStatusCreationDate);

        $this->assertEquals("SEK", $getOrdersResponse->currency);

        $this->assertInstanceOf("Svea\WebPay\BuildOrder\RowBuilders\IndividualCustomer", $getOrdersResponse->customer);
        // asserting customer attributes in other testcases
        //$this->assertEquals( null, $getOrdersResponse->customer->email );  // -- returns current customer id email, may change

        $this->assertEquals("1000013", $getOrdersResponse->customerId);
        $this->assertEquals("created by TestUtil::createOrder()", $getOrdersResponse->customerReference);
        $this->assertEquals(false, $getOrdersResponse->isPossibleToAdminister);
        $this->assertEquals(true, $getOrdersResponse->isPossibleToCancel);
        $this->assertEquals(null, $getOrdersResponse->notes);
        $this->assertEquals("Created", $getOrdersResponse->orderDeliveryStatus);

        $this->assertInstanceOf("Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow", $getOrdersResponse->numberedOrderRows[0]);
        // asserting order row attributes in invoice testcase

        $this->assertEquals("Active", $getOrdersResponse->orderStatus);
        $this->assertEquals("PaymentPlan", $getOrdersResponse->orderType);
        $this->assertEquals(3, $getOrdersResponse->paymentPlanDetailsContractLengthMonths);
        $this->assertEquals(null, $getOrdersResponse->paymentPlanDetailsContractNumber);
        $this->assertEquals(null, $getOrdersResponse->pendingReasons);
        $this->assertEquals(414812, $getOrdersResponse->orderId);
        $this->assertEquals(true, $getOrdersResponse->sveaWillBuy);
    }

    function test_orderrow_response_incvat()
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

        $response = WebPayAdmin::queryOrder($config)
            ->setCountryCode('SE')
            ->setOrderId($orderResponse->sveaOrderId)
            ->queryInvoiceOrder()
            ->doRequest();
        //print_r($response);
        $this->assertEquals(1, $response->accepted);
        $this->assertEquals(145.00, $response->numberedOrderRows[0]->amountIncVat);
        $this->assertEquals(null, $response->numberedOrderRows[0]->amountExVat);

    }

    function test_orderrow_response_exvat()
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
            ->useInvoicePayment()
            ->doRequest();

        $response = WebPayAdmin::queryOrder($config)
            ->setCountryCode('SE')
            ->setOrderId($orderResponse->sveaOrderId)
            ->queryInvoiceOrder()
            ->doRequest();
        //print_r($response);
        $this->assertEquals(1, $response->accepted);
        $this->assertEquals(145.00, $response->numberedOrderRows[0]->amountExVat);
        $this->assertEquals(null, $response->numberedOrderRows[0]->amountIncVat);
    }


}
