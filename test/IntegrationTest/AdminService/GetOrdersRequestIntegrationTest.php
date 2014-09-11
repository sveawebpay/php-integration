<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class GetOrdersRequestIntegrationTest extends PHPUnit_Framework_TestCase{

    // TODO remove "manual" below & create new orders to query on the fly
    
    /**
     * 1. create an Invoice|PaymentPlan order
     * 2. note the client credentials, order number and type, and insert below
     * 3. run the test
     */
    public function test_manual_GetOrdersRequest_for_invoice_order() {
        
        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'skeleton for test_manual_GetOrdersRequest_for_invoice_order'
//        );
        
        $countryCode = "SE";
        $sveaOrderIdToGet = 348629;
        $orderType = ConfigurationProvider::INVOICE_TYPE;
        
        $getOrdersBuilder = new Svea\QueryOrderBuilder( Svea\SveaConfig::getDefaultConfig() );
        $getOrdersBuilder->setOrderId($sveaOrderIdToGet);
        $getOrdersBuilder->setCountryCode($countryCode);
        $getOrdersBuilder->orderType = $orderType;        

// Example of test_manual_GetOrdersRequest_for_invoice_order 348629 raw request response to parse:
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
        
        
        $request = new Svea\AdminService\GetOrdersRequest( $getOrdersBuilder );
        $getOrdersResponse = $request-> doRequest();
        
       print_r( $getOrdersResponse );        
        
        $this->assertInstanceOf('Svea\AdminService\GetOrdersResponse', $getOrdersResponse);
        $this->assertEquals(1, $getOrdersResponse->accepted );
        $this->assertEquals(0, $getOrdersResponse->resultcode);
        $this->assertEquals(null, $getOrdersResponse->errormessage);
        
        $this->assertEquals( null, $getOrdersResponse->changedDate );  // TODO add test for changed order later
        $this->assertEquals( 79021, $getOrdersResponse->clientId );
        $this->assertEquals( 449, $getOrdersResponse->clientOrderId );
        $this->assertEquals( "2014-05-19T16:04:54.787", $getOrdersResponse->createdDate );
       
        $this->assertEquals( true, $getOrdersResponse->creditReportStatusAccepted );
        $this->assertEquals( "2014-05-19T16:04:54.893", $getOrdersResponse->creditReportStatusCreationDate );

        $this->assertEquals( "SEK", $getOrdersResponse->currency );

        $this->assertInstanceOf( "Svea\IndividualCustomer", $getOrdersResponse->customer );
        $this->assertEquals( "194605092222", $getOrdersResponse->customer->ssn );
        $this->assertEquals( "194605092222", $getOrdersResponse->customer->ssn );
        $this->assertEquals( null, $getOrdersResponse->customer->initials );
        $this->assertEquals( null, $getOrdersResponse->customer->birthDate );
        $this->assertEquals( null, $getOrdersResponse->customer->firstname );
        $this->assertEquals( null, $getOrdersResponse->customer->lastname );
        $this->assertEquals( "test@svea.com", $getOrdersResponse->customer->email );
        $this->assertEquals( "999999", $getOrdersResponse->customer->phonenumber );
        $this->assertEquals( "Testgatan 1", $getOrdersResponse->customer->street );
        $this->assertEquals( "c/o Eriksson, Erik", $getOrdersResponse->customer->coAddress );
        $this->assertEquals( "99999", $getOrdersResponse->customer->zipCode );
        $this->assertEquals( "Stan", $getOrdersResponse->customer->locality );
       
        $this->assertEquals( "1000117", $getOrdersResponse->customerId );   
        $this->assertEquals( null, $getOrdersResponse->customerReference );
        $this->assertEquals( null, $getOrdersResponse->deliveryAddress );
        $this->assertEquals( false, $getOrdersResponse->isPossibleToAdminister );
        $this->assertEquals( true, $getOrdersResponse->isPossibleToCancel );
        $this->assertEquals( null, $getOrdersResponse->notes );
        $this->assertEquals( "Created", $getOrdersResponse->orderDeliveryStatus ); 
      
        $this->assertInstanceOf( "Svea\NumberedOrderRow", $getOrdersResponse->numberedOrderRows[0] );
        $this->assertEquals( 1, $getOrdersResponse->numberedOrderRows[0]->rowNumber );
        $this->assertEquals( null, $getOrdersResponse->numberedOrderRows[0]->articleNumber );
        $this->assertEquals( 2.00, $getOrdersResponse->numberedOrderRows[0]->quantity );
        $this->assertEquals( null, $getOrdersResponse->numberedOrderRows[0]->unit );
        $this->assertEquals( 2000.00, $getOrdersResponse->numberedOrderRows[0]->amountExVat );
        $this->assertEquals( 25.00, $getOrdersResponse->numberedOrderRows[0]->vatPercent );          // TODO check -- should be int?!
        $this->assertEquals( null, $getOrdersResponse->numberedOrderRows[0]->name );
        $this->assertEquals( "Dyr produkt 25%", $getOrdersResponse->numberedOrderRows[0]->description );
        $this->assertEquals( 0, $getOrdersResponse->numberedOrderRows[0]->vatDiscount );
                        
        // only check attributes of first row
        $this->assertInstanceOf( "Svea\NumberedOrderRow", $getOrdersResponse->numberedOrderRows[3] );
        $this->assertEquals( 4, $getOrdersResponse->numberedOrderRows[3]->rowNumber );
        
        $this->assertEquals( "Active", $getOrdersResponse->orderStatus );
        $this->assertEquals( "Invoice", $getOrdersResponse->orderType );
        $this->assertEquals( null, $getOrdersResponse->paymentPlanDetails );
        $this->assertEquals( null, $getOrdersResponse->pendingReasons );
        $this->assertEquals( 348629, $getOrdersResponse->orderId );
        $this->assertEquals( true, $getOrdersResponse->sveaWillBuy );
    }
       
        public function test_manual_GetOrdersRequest_for_paymentplan_order() {
        
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for test_manual_GetOrdersRequest_for_paymentplan_order'
        );
            
        // TODO run test for paymentplan order & returned raw request response attributes as well
            
        }
}
