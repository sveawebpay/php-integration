<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class WebPayAdminIntegrationTest extends PHPUnit_Framework_TestCase {

//    // CancelOrderBuilder endpoints: cancelInvoiceOrder(), cancelPaymentPlanOrder(), cancelCardOrder()
//    function test_CancelOrderBuilder_Invoice_success() {
//        $country = "SE";
//        $order = TestUtil::createOrder( TestUtil::createIndividualCustomer($country) );
//        $orderResponse = $order->useInvoicePayment()->doRequest();
//       
//        $this->assertEquals(1, $orderResponse->accepted);
//         
//        $cancelResponse = WebPayAdmin::cancelOrder( Svea\SveaConfig::getDefaultConfig() )
//                ->setOrderId($orderResponse->sveaOrderId)
//                ->setCountryCode($country)
//                ->cancelInvoiceOrder()
//                    ->doRequest();
//        
//        $this->assertEquals(1, $cancelResponse->accepted);
//    }
//    
//    function test_CancelOrderBuilder_PaymentPlan_success() {
//        $country = "SE";
//        $order = TestUtil::createOrder( TestUtil::createIndividualCustomer($country) )
//            ->addOrderRow( WebPayItem::orderRow()
//                ->setQuantity(1)
//                ->setAmountExVat(1000.00)
//                ->setVatPercent(25)
//            )
//        ;
//        $orderResponse = $order->usePaymentPlanPayment( TestUtil::getGetPaymentPlanParamsForTesting() )->doRequest();
//
//        $this->assertEquals(1, $orderResponse->accepted);
//        
//        $cancelResponse = WebPayAdmin::cancelOrder( Svea\SveaConfig::getDefaultConfig() )
//                ->setOrderId($orderResponse->sveaOrderId)
//                ->setCountryCode($country)
//                ->cancelPaymentPlanOrder()
//                    ->doRequest();
//        
//        $this->assertEquals(1, $cancelResponse->accepted);
//    }    
//       
//    /**
//     * test_manual_CancelOrderBuilder_Card_success 
//     * 
//     * run this manually after you've performed a card transaction and have set
//     * the transaction status to success using the tools in the logg admin.
//     */  
//    function test_manual_CancelOrderBuilder_Card_success() {
//
//        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'skeleton for manual test of cancelOrder for a card order'
//        );
//        
//        // Set the below to match the transaction, then run the test.
//        $customerrefno = "test_1396964349955";
//        $transactionId = 580658;
//
//        $request = WebPayAdmin::cancelOrder( Svea\SveaConfig::getDefaultConfig() )
//            ->setOrderId( $transactionId )
//            ->setCountryCode( "SE" )
//            ->cancelCardOrder()
//                ->doRequest();        
//         
//        $this->assertInstanceOf( "Svea\HostedAdminResponse", $response );
//        
//        $this->assertEquals( 1, $response->accepted );        
//        $this->assertEquals( $customerrefno, $response->customerrefno );
//    }    
//    
//    /**
//     *  test_queryOrder_queryInvoiceOrder_order
//     */
//    function test_queryOrder_queryInvoiceOrder_order() {
//        // create invoice order w/three rows (2xA, 1xB)
//        $country = "SE";
//
//        $a_quantity = 2;
//        $a_amountExVat = 1000.00;
//        $a_vatPercent = 25;
//        
//        $b_quantity = 1;
//        $b_amountExVat = 100.00;
//        $b_vatPercent = 12;
//        $b_articleNumber = "1071e";
//        $b_unit = "pcs.";
//        $b_name = "B Name";
//        $b_description = "B Description";    
//        $b_discount = 0;
//        
//        $order = TestUtil::createOrderWithoutOrderRows( TestUtil::createIndividualCustomer($country) )
//            ->addOrderRow( WebPayItem::orderRow()
//                
//                ->setQuantity($a_quantity)
//                ->setAmountExVat($a_amountExVat)
//                ->setVatPercent($a_vatPercent)
//            )
//            ->addOrderRow( WebPayItem::orderRow()
//                ->setQuantity($b_quantity)
//                ->setAmountExVat($b_amountExVat)
//                ->setVatPercent($b_vatPercent)
//                ->setArticleNumber($b_articleNumber)
//                ->setUnit($b_unit)
//                ->setName($b_name)
//                ->setDescription($b_description)
//                ->setDiscountPercent($b_discount)              
//            )                
//        ;
//        $orderResponse = $order->useInvoicePayment()->doRequest();
//        $this->assertEquals(1, $orderResponse->accepted);
//        
//        $createdOrderId = $orderResponse->sveaOrderId;
//        
//        // query orderrows
//        $queryOrderBuilder = WebPayAdmin::queryOrder( Svea\SveaConfig::getDefaultConfig() )
//            ->setOrderId( $createdOrderId )
//            ->setCountryCode($country)
//        ;
//                
//        $queryResponse = $queryOrderBuilder->queryInvoiceOrder()->doRequest(); 
//        
//        //print_r( $queryResponse);
//        $this->assertEquals(1, $queryResponse->accepted);
//        // assert that order rows are the same
//        $this->assertEquals( $a_quantity, $queryResponse->numberedOrderRows[0]->quantity );
//        $this->assertEquals( $a_amountExVat, $queryResponse->numberedOrderRows[0]->amountExVat );
//        
//        $this->assertEquals( $b_quantity, $queryResponse->numberedOrderRows[1]->quantity );
//        $this->assertEquals( $b_amountExVat, $queryResponse->numberedOrderRows[1]->amountExVat );        
//        $this->assertEquals( $b_vatPercent, $queryResponse->numberedOrderRows[1]->vatPercent );
//        $this->assertEquals( $b_articleNumber, $queryResponse->numberedOrderRows[1]->articleNumber );
//        $this->assertEquals( $b_unit, $queryResponse->numberedOrderRows[1]->unit );
//        $this->assertStringStartsWith( $b_name, $queryResponse->numberedOrderRows[1]->description );
//        $this->assertStringEndsWith( $b_description, $queryResponse->numberedOrderRows[1]->description );
//        $this->assertEquals( $b_discount, $queryResponse->numberedOrderRows[1]->discountPercent );
//
//        $this->assertEquals( null, $queryResponse->numberedOrderRows[1]->creditInvoiceId ); // not set
//        $this->assertEquals( null, $queryResponse->numberedOrderRows[1]->invoiceId ); // not set
//        $this->assertEquals( 2, $queryResponse->numberedOrderRows[1]->rowNumber );  // rows are 1-indexed
//        $this->assertEquals( "NotDelivered", $queryResponse->numberedOrderRows[1]->status );
//    }            
//    
//    /**
//     *  test_queryOrder_queryPaymentPlanOrder_order
//     */
//    function test_queryOrder_queryPaymentPlanOrder_order() {
//        // create order w/three rows (2xA, 1xB)
//        $country = "SE";
//
//        $a_quantity = 2;
//        $a_amountExVat = 1000.00;
//        $a_vatPercent = 25;
//        
//        $b_quantity = 1;
//        $b_amountExVat = 100.00;
//        $b_vatPercent = 12;
//        $b_articleNumber = "1071e";
//        $b_unit = "pcs.";
//        $b_name = "B Name";
//        $b_description = "B Description";    
//        $b_discount = 0;
//        
//        $order = TestUtil::createOrderWithoutOrderRows( TestUtil::createIndividualCustomer($country) )
//            ->addOrderRow( WebPayItem::orderRow()
//                ->setQuantity($a_quantity)
//                ->setAmountExVat($a_amountExVat)
//                ->setVatPercent($a_vatPercent)
//            )
//            ->addOrderRow( WebPayItem::orderRow()
//                ->setQuantity($b_quantity)
//                ->setAmountExVat($b_amountExVat)
//                ->setVatPercent($b_vatPercent)
//                ->setArticleNumber($b_articleNumber)
//                ->setUnit($b_unit)
//                ->setName($b_name)
//                ->setDescription($b_description)
//                ->setDiscountPercent($b_discount)              
//            )                
//        ;
//        $orderResponse = $order->usePaymentPlanPayment( TestUtil::getGetPaymentPlanParamsForTesting() )->doRequest();
//        $this->assertEquals(1, $orderResponse->accepted);
//        
//        $createdOrderId = $orderResponse->sveaOrderId;
//        
//        // query orderrows
//        $queryOrderBuilder = WebPayAdmin::queryOrder( Svea\SveaConfig::getDefaultConfig() )
//            ->setOrderId( $createdOrderId )
//            ->setCountryCode($country)
//        ;
//                
//        $queryResponse = $queryOrderBuilder->queryPaymentPlanOrder()->doRequest(); 
//        
//        //print_r( $queryResponse);
//        $this->assertEquals(1, $queryResponse->accepted);
//        // assert that order rows are the same
//        $this->assertEquals( $a_quantity, $queryResponse->numberedOrderRows[0]->quantity );
//        $this->assertEquals( $a_amountExVat, $queryResponse->numberedOrderRows[0]->amountExVat );
//        
//        $this->assertEquals( $b_quantity, $queryResponse->numberedOrderRows[1]->quantity );
//        $this->assertEquals( $b_amountExVat, $queryResponse->numberedOrderRows[1]->amountExVat );        
//        $this->assertEquals( $b_vatPercent, $queryResponse->numberedOrderRows[1]->vatPercent );
//        $this->assertEquals( $b_articleNumber, $queryResponse->numberedOrderRows[1]->articleNumber );
//        $this->assertEquals( $b_unit, $queryResponse->numberedOrderRows[1]->unit );
//        $this->assertStringStartsWith( $b_name, $queryResponse->numberedOrderRows[1]->description );
//        $this->assertStringEndsWith( $b_description, $queryResponse->numberedOrderRows[1]->description );
//        $this->assertEquals( $b_discount, $queryResponse->numberedOrderRows[1]->discountPercent );
//
//        $this->assertEquals( null, $queryResponse->numberedOrderRows[1]->creditInvoiceId ); // not set
//        $this->assertEquals( null, $queryResponse->numberedOrderRows[1]->invoiceId ); // not set
//        $this->assertEquals( 2, $queryResponse->numberedOrderRows[1]->rowNumber );  // rows are 1-indexed
//        $this->assertEquals( "NotDelivered", $queryResponse->numberedOrderRows[1]->status );
//    }   
//    
//    /**
//     * test_manual_queryOrder_queryCard_order_step_1
//     *
//     */
//    function test_manual_queryOrder_queryCard_order_step_1() {
//        
//        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'skeleton for manual test_manual_queryOrder_queryCard_order_step_1, step 1'
//        );
//          
//        // 1. remove (put in a comment) the above code to enable the test
//        // 2. run the test, and get the paymenturl from the output
//        // 3. go to the paymenturl and complete the transaction manually, making note of the response transactionid
//        // 4. enter the transactionid into test_manual_queryOrder_queryCard_order_step_12() below and run the test
//        
//        $orderLanguage = "sv";   
//        $returnUrl = "http://foo.bar.com";
//        $ipAddress = "127.0.0.1";
//        
//        // create order
//        $order = \TestUtil::createOrderWithoutOrderRows( TestUtil::createIndividualCustomer("SE")->setIpAddress($ipAddress) );       
//        
//        // create order w/three rows (2xA, 1xB)
//        $country = "SE";
//
//        $a_quantity = 2;
//        $a_amountExVat = 1000.00;
//        $a_vatPercent = 25;
//        
//        $b_quantity = 1;
//        $b_amountExVat = 100.00;
//        $b_vatPercent = 12;
//        $b_articleNumber = "1071e";
//        $b_unit = "pcs.";
//        $b_name = "B Name";
//        $b_description = "B Description";    
//        $b_discount = 0;
//
//        $order
//            ->addOrderRow( WebPayItem::orderRow()
//                ->setQuantity($a_quantity)
//                ->setAmountExVat($a_amountExVat)
//                ->setVatPercent($a_vatPercent)
//            )
//            ->addOrderRow( WebPayItem::orderRow()
//                ->setQuantity($b_quantity)
//                ->setAmountExVat($b_amountExVat)
//                ->setVatPercent($b_vatPercent)
//                ->setArticleNumber($b_articleNumber)
//                ->setUnit($b_unit)
//                ->setName($b_name)
//                ->setDescription($b_description)
//                ->setDiscountPercent($b_discount)              
//            )                
//        ;
//            
//        // set payment method
//        // call getPaymentURL
//        $response = $order
//            ->usePayPageCardOnly()
//            ->setPayPageLanguage($orderLanguage)
//            ->setReturnUrl($returnUrl)
//            ->getPaymentURL();       
//            
//        // check that request was accepted
//        $this->assertEquals( 1, $response->accepted );                
//
//        // print the url to use to confirm the transaction
//        print_r( " test_manual_queryOrder_queryCard_order_step_1(): " . $response->testurl ." ");
//    }
//    
//    /**
//     * test_manual_queryOrder_queryCard_order_step_2
//     * 
//     * run this test manually after you've performed a card transaction and have gotten the transaction details needed
//
//     */  
//    function test_manual_queryOrder_queryCard_order_step_2() {
//        
//        // Stop here and mark this test as incomplete.
////        $this->markTestIncomplete(
////            'skeleton for manual test_manual_queryOrder_queryCard_order_step_2, step 2'
////        );
//        
//        // 1. remove (put in a comment) the above code to enable the test
//        // 2. set $createdOrderId to the transactionid from the transaction log of the request done by following the url from step 1 above.
//        // 3. below is an example of the xml generated by paypage for the request in step 1 above, along with the transaction id, for reference.
//             
//        $createdOrderId = 582616;
//        
//    //----
//    //1400598653267 class se.sveaekonomi.webpay.util.security.Base64Util DEBUG M:1130
//    //        
//    //decoded = < ?xml version='1.0' encoding='UTF-8'? ><payment><!--Message generated by PayPage-->        
//    //    <customerRefNo>clientOrderNumber:2014-05-20T17:10:35 02:00</customerRefNo>
//    //    <amount>261200</amount>
//    //    <vat>51200</vat>
//    //    <orderrows>
//    //        <row>
//    //            <name></name>
//    //            <description></description>
//    //            <sku></sku>
//    //            <amount>125000</amount>
//    //            <vat>25000</vat>
//    //            <quantity>2.0</quantity>
//    //        </row>
//    //        <row>
//    //            <name>B Name</name>
//    //            <description>B Description</description>
//    //            <sku>1071e</sku>
//    //            <unit>pcs.</unit>
//    //            <amount>11200</amount>
//    //            <vat>1200</vat>
//    //            <quantity>1.0</quantity>
//    //        </row>
//    //    </orderrows>
//    //    <paymentMethod>KORTCERT</paymentMethod>
//    //    <amount>261200</amount>
//    //    <customerrefno>clientOrderNumber:2014-05-20T17:10:35 02:00</customerrefno>
//    //    <addinvoicefee>FALSE</addinvoicefee>
//    //    <vat>51200</vat>
//    //    <iscompany>FALSE</iscompany>
//    //    <returnurl>http://foo.bar.com</returnurl>
//    //    <ipaddress>127.0.0.1</ipaddress>
//    //    <lang>sv</lang>
//    //    <cancelurl></cancelurl>
//    //    <currency>SEK</currency>
//    //    <customer>
//    //        <firstName>Tess T</firstName>
//    //        <lastName>Persson</lastName>
//    //        <ssn>194605092222</ssn>
//    //        <address>Testgatan</address>
//    //        <address2>c/o Eriksson, Erik</address2>
//    //        <city>Stan</city>
//    //        <country>SE</country>
//    //        <zip>99999</zip>
//    //        <housenumber>1</housenumber>
//    //    </customer>
//    //</payment>      
//    //----
//    //1400598653392 se.sveaekonomi.webpay.business.businesslogic.payment.paymenthandler.PaymentHandler INFO T:582616 M:1130
//    //Created transaction with id = 582616. IP = 10.222.205.57. UserAgent = Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20100101 Firefox/29.0
//    //----        
//                
//        // create order w/three rows (2xA, 1xB) (from step 1 above)
//        $country = "SE";
//
//        $a_quantity = 2;
//        $a_amountExVat = 1000.00;
//        $a_vatPercent = 25;
//        
//        $b_quantity = 1;
//        $b_amountExVat = 100.00;
//        $b_vatPercent = 12;
//        $b_articleNumber = "1071e";
//        $b_unit = "pcs.";
//        $b_name = "B Name";
//        $b_description = "B Description";    
//        $b_discount = 0;
//        
//        // query orderrows
//        $queryOrderBuilder = WebPayAdmin::queryOrder( Svea\SveaConfig::getDefaultConfig() )
//            ->setOrderId( $createdOrderId )
//            ->setCountryCode($country)
//        ;
//                
//        $queryResponse = $queryOrderBuilder->queryCardOrder()->doRequest(); 
//        
//        //print_r( $queryResponse);
//        $this->assertEquals(1, $queryResponse->accepted);
//
////    [transactionId] => 582616
////    [customerrefno] => clientOrderNumber:2014-05-20T17:10:35 02:00
////    [merchantid] => 1130
////    [status] => AUTHORIZED
////    [amount] => 261200
////    [currency] => SEK
////    [vat] => 51200
////    [capturedamount] => 
////    [authorizedamount] => 261200
////    [created] => 2014-05-20 17:10:53.377
////    [creditstatus] => CREDNONE
////    [creditedamount] => 0
////    [merchantresponsecode] => 0
////    [paymentmethod] => KORTCERT
////    [orderrows] => Array
////        (
////            [0] => Svea\OrderRow Object
////                (
////                    [articleNumber] => 
////                    [quantity] => 2
////                    [unit] => 
////                    [amountExVat] => 1000
////                    [amountIncVat] => 
////                    [vatPercent] => 25
////                    [name] => 
////                    [description] => 
////                    [discountPercent] => 
////                    [vatDiscount] => 0
////                )
////
////            [1] => Svea\OrderRow Object
////                (
////                    [articleNumber] => 1071e
////                    [quantity] => 1
////                    [unit] => pcs.
////                    [amountExVat] => 100
////                    [amountIncVat] => 
////                    [vatPercent] => 12
////                    [name] => B Name
////                    [description] => B Description
////                    [discountPercent] => 
////                    [vatDiscount] => 0
////                )
////
////        )
////
////    [accepted] => 1
////    [resultcode] => 0
////    [errormessage] => 
////)        
//                
//        // assert that order rows are the same 
//        $this->assertEquals( $a_quantity, $queryResponse->numberedOrderRows[0]->quantity );
//        $this->assertEquals( $a_amountExVat, $queryResponse->numberedOrderRows[0]->amountExVat );
//        
//        $this->assertEquals( $b_quantity, $queryResponse->numberedOrderRows[1]->quantity );
//        $this->assertEquals( $b_amountExVat, $queryResponse->numberedOrderRows[1]->amountExVat );        
//        $this->assertEquals( $b_vatPercent, $queryResponse->numberedOrderRows[1]->vatPercent );
//        $this->assertEquals( $b_articleNumber, $queryResponse->numberedOrderRows[1]->articleNumber );
//        $this->assertEquals( $b_unit, $queryResponse->numberedOrderRows[1]->unit );
//        $this->assertStringStartsWith( $b_name, $queryResponse->numberedOrderRows[1]->name );
//        $this->assertStringEndsWith( $b_description, $queryResponse->numberedOrderRows[1]->description );
//        $this->assertEquals( $b_discount, $queryResponse->numberedOrderRows[1]->discountPercent );
//    }   
//    
//    /**
//     * test_manual_queryOrder_queryDirectBank_order_step_1
//     *
//     */
//    function test_manual_queryOrder_queryDirectBank_order_step_1() {
//        
//        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'skeleton for test_manual_queryOrder_queryDirectBank_order_step_1, step 1'
//        );
//          
//        // 1. remove (put in a comment) the above code to enable the test
//        // 2. run the test, and get the paymenturl from the output
//        // 3. go to the paymenturl and complete the transaction manually, making note of the response transactionid
//        // 4. enter the transactionid into test_manual_queryOrder_queryCard_order_step_12() below and run the test
//        
//        $orderLanguage = "sv";   
//        $returnUrl = "http://foo.bar.com";
//        $ipAddress = "127.0.0.1";
//        
//        // create order
//        $order = \TestUtil::createOrderWithoutOrderRows( TestUtil::createIndividualCustomer("SE")->setIpAddress($ipAddress) );       
//        
//        // create order w/three rows (2xA, 1xB)
//        $country = "SE";
//
//        $a_quantity = 2;
//        $a_amountExVat = 1000.00;
//        $a_vatPercent = 25;
//        
//        $b_quantity = 1;
//        $b_amountExVat = 100.00;
//        $b_vatPercent = 12;
//        $b_articleNumber = "Red 5";
//        $b_unit = "pcs.";
//        $b_name = "B Name";
//        $b_description = "B Description";    
//        $b_discount = 0;
//
//        $order
//            ->addOrderRow( WebPayItem::orderRow()
//                ->setQuantity($a_quantity)
//                ->setAmountExVat($a_amountExVat)
//                ->setVatPercent($a_vatPercent)
//            )
//            ->addOrderRow( WebPayItem::orderRow()
//                ->setQuantity($b_quantity)
//                ->setAmountExVat($b_amountExVat)
//                ->setVatPercent($b_vatPercent)
//                ->setArticleNumber($b_articleNumber)
//                ->setUnit($b_unit)
//                ->setName($b_name)
//                ->setDescription($b_description)
//                ->setDiscountPercent($b_discount)              
//            )                
//        ;
//            
//        // set payment method
//        // call getPaymentURL
//        $response = $order
//            ->usePayPageDirectBankOnly()
//            ->setPayPageLanguage($orderLanguage)
//            ->setReturnUrl($returnUrl)
//            ->getPaymentURL();       
//            
//        // check that request was accepted
//        $this->assertEquals( 1, $response->accepted );                
//
//        // print the url to use to confirm the transaction
//        print_r( " test_manual_queryOrder_queryDirectBank_order_step_1(): " . $response->testurl ." ");
//    }
//    
//    /**
//     * test_manual_queryOrder_queryDirectBank_order_step_2
//     * 
//     * run this test manually after you've performed a direct bank transaction and have gotten the transaction details needed
//     */  
//    function test_manual_queryOrder_queryDirectBank_order_step_2() {
//        
//        // Stop here and mark this test as incomplete.
////        $this->markTestIncomplete(
////            'skeleton for test_manual_queryOrder_queryDirectBank_order_step_2, step 2'
////        );
//        
//        // 1. remove (put in a comment) the above code to enable the test
//        // 2. set $createdOrderId to the transactionid from the transaction log of the request done by following the url from step 1 above.
//        // 3. below is an example of the xml generated by paypage for the request in step 1 above, along with the transaction id, for reference.
//             
//        $createdOrderId = 582656;
//                        
//        // create order w/three rows (2xA, 1xB) (from step 1 above)
//        $country = "SE";
//
//        $a_quantity = 2;
//        $a_amountExVat = 1000.00;
//        $a_vatPercent = 25;
//        
//        $b_quantity = 1;
//        $b_amountExVat = 100.00;
//        $b_vatPercent = 12;
//        $b_articleNumber = "Red 5";
//        $b_unit = "pcs.";
//        $b_name = "B Name";
//        $b_description = "B Description";    
//        $b_discount = 0;
//        
//        // query orderrows
//        $queryOrderBuilder = WebPayAdmin::queryOrder( Svea\SveaConfig::getDefaultConfig() )
//            ->setOrderId( $createdOrderId )
//            ->setCountryCode($country)
//        ;
//                
//        $queryResponse = $queryOrderBuilder->queryCardOrder()->doRequest(); 
//        
//        //print_r( $queryResponse);
//        $this->assertEquals(1, $queryResponse->accepted);
//
//        //Svea\QueryTransactionResponse Object
//        //(
//        //    [transactionId] => 582656
//        //    [customerrefno] => clientOrderNumber:2014-05-21T11:30:53 02:00
//        //    [merchantid] => 1130
//        //    [status] => SUCCESS
//        //    [amount] => 261200
//        //    [currency] => SEK
//        //    [vat] => 51200
//        //    [capturedamount] => 261200
//        //    [authorizedamount] => 261200
//        //    [created] => 2014-05-21 11:31:15.697
//        //    [creditstatus] => CREDNONE
//        //    [creditedamount] => 0
//        //    [merchantresponsecode] => 0
//        //    [paymentmethod] => DBNORDEASE
//        //    [orderrows] => Array
//        //        (
//        //            [0] => Svea\OrderRow Object
//        //                (
//        //                    [articleNumber] => 
//        //                    [quantity] => 2
//        //                    [unit] => 
//        //                    [amountExVat] => 1000
//        //                    [amountIncVat] => 
//        //                    [vatPercent] => 25
//        //                    [name] => 
//        //                    [description] => 
//        //                    [discountPercent] => 
//        //                    [vatDiscount] => 0
//        //                )
//        //
//        //            [1] => Svea\OrderRow Object
//        //                (
//        //                    [articleNumber] => Red 5
//        //                    [quantity] => 1
//        //                    [unit] => pcs.
//        //                    [amountExVat] => 100
//        //                    [amountIncVat] => 
//        //                    [vatPercent] => 12
//        //                    [name] => B Name
//        //                    [description] => B Description
//        //                    [discountPercent] => 
//        //                    [vatDiscount] => 0
//        //                )
//        //
//        //        )
//        //
//        //    [accepted] => 1
//        //    [resultcode] => 0
//        //    [errormessage] => 
//        //)
//        
//        // assert that order rows are the same 
//        $this->assertEquals( $a_quantity, $queryResponse->numberedOrderRows[0]->quantity );
//        $this->assertEquals( $a_amountExVat, $queryResponse->numberedOrderRows[0]->amountExVat );
//        
//        $this->assertEquals( $b_quantity, $queryResponse->numberedOrderRows[1]->quantity );
//        $this->assertEquals( $b_amountExVat, $queryResponse->numberedOrderRows[1]->amountExVat );        
//        $this->assertEquals( $b_vatPercent, $queryResponse->numberedOrderRows[1]->vatPercent );
//        $this->assertEquals( $b_articleNumber, $queryResponse->numberedOrderRows[1]->articleNumber );
//        $this->assertEquals( $b_unit, $queryResponse->numberedOrderRows[1]->unit );
//        $this->assertStringStartsWith( $b_name, $queryResponse->numberedOrderRows[1]->name );
//        $this->assertStringEndsWith( $b_description, $queryResponse->numberedOrderRows[1]->description );
//        $this->assertEquals( $b_discount, $queryResponse->numberedOrderRows[1]->discountPercent );
//    }   
//
//    // CancelOrderRowsBuilder endpoints: cancelInvoiceOrderRows(), cancelPaymentPlanOrderRows(), cancelCardOrderRows()
//    function test_CancelOrderBuilderRows_Invoice_single_row_success() {
//        $country = "SE";
//        $order = TestUtil::createOrderWithoutOrderRows( TestUtil::createIndividualCustomer($country) );
//        $order->addOrderRow(TestUtil::createOrderRow(1.00));        
//        $order->addOrderRow(TestUtil::createOrderRow(2.00));
//        $orderResponse = $order->useInvoicePayment()->doRequest();
//       
//        $this->assertEquals(1, $orderResponse->accepted);
//         
//        $cancelResponse = WebPayAdmin::cancelOrderRows( Svea\SveaConfig::getDefaultConfig() )
//                ->setOrderId($orderResponse->sveaOrderId)
//                ->setCountryCode($country)
//                ->setRowToCancel( 1 )
//                ->cancelInvoiceOrderRows()
//                    ->doRequest();
//        
//        $this->assertEquals(1, $cancelResponse->accepted);
//    }
//    
//    // CancelOrderRowsBuilder endpoints: cancelInvoiceOrderRows(), cancelPaymentPlanOrderRows(), cancelCardOrderRows()
//    function test_CancelOrderBuilderRows_Invoice_multiple_rows_success() {
//        $country = "SE";
//        $order = TestUtil::createOrderWithoutOrderRows( TestUtil::createIndividualCustomer($country) );
//        $order->addOrderRow(TestUtil::createOrderRow(1.00));        
//        $order->addOrderRow(TestUtil::createOrderRow(2.00));
//        $order->addOrderRow(TestUtil::createOrderRow(3.00));
//        $orderResponse = $order->useInvoicePayment()->doRequest();
//       
//        $this->assertEquals(1, $orderResponse->accepted);
//         
//        $cancelResponse = WebPayAdmin::cancelOrderRows( Svea\SveaConfig::getDefaultConfig() )
//                ->setOrderId($orderResponse->sveaOrderId)
//                ->setCountryCode($country)
//                ->setRowsToCancel( array(1,2) )
//                ->setRowToCancel( 3 )
//                ->cancelInvoiceOrderRows()
//                    ->doRequest();
//        
//        $this->assertEquals(1, $cancelResponse->accepted);
//    } 
//
//    // CancelOrderRowsBuilder endpoints: cancelInvoiceOrderRows(), cancelPaymentPlanOrderRows(), cancelCardOrderRows()
//    function test_CancelOrderBuilderRows_PaymentPlan_single_row_success() {
//        $country = "SE";
//        $order = TestUtil::createOrderWithoutOrderRows( TestUtil::createIndividualCustomer($country) );
//        $order->addOrderRow(TestUtil::createOrderRow(1000.00));        
//        $order->addOrderRow(TestUtil::createOrderRow(2000.00));
//        $orderResponse = $order->usePaymentPlanPayment( TestUtil::getGetPaymentPlanParamsForTesting($country))->doRequest();
//       
//        $this->assertEquals(1, $orderResponse->accepted);
//         
//        $cancelResponse = WebPayAdmin::cancelOrderRows( Svea\SveaConfig::getDefaultConfig() )
//                ->setOrderId($orderResponse->sveaOrderId)
//                ->setCountryCode($country)
//                ->setRowToCancel( 2 )
//                ->cancelPaymentPlanOrderRows()
//                    ->doRequest();
//        
//        $this->assertEquals(1, $cancelResponse->accepted);
//    }    
//    
//    /**
//     * test_manual_CancelOrderBuilderRows_Card_single_row_success_step_1
//     *
//     */
//    function test_manual_CancelOrderBuilderRows_Card_single_row_success_step_1() {
//        
//        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'skeleton for test_manual_CancelOrderBuilderRows_Card_single_row_success_step_1, step 1'
//        );
//          
//        // 1. remove (put in a comment) the above code to enable the test
//        // 2. run the test, and get the paymenturl from the output
//        // 3. go to the paymenturl and complete the transaction manually, making note of the response transactionid
//        // 4. enter the transactionid into test_manual_queryOrder_queryCard_order_step_2() below and run the test
//        
//        $orderLanguage = "sv";   
//        $returnUrl = "http://foo.bar.com";
//        $ipAddress = "127.0.0.1";
//        
//        // create order w/three rows
//        $order = \TestUtil::createOrderWithoutOrderRows( TestUtil::createIndividualCustomer("SE")->setIpAddress($ipAddress) );       
//
//        // 2x100 @25 = 25000 (5000)
//        // 1x100 @25 = 12500 (2500)
//        // 1x100 @12 = 11200 (1200)
//        // amount = 48700, vat = 8700
//        $country = "SE";
//
//        $order
//            ->addOrderRow( WebPayItem::orderRow()
//                ->setQuantity(2)
//                ->setAmountExVat(100.00)
//                ->setVatPercent(25)
//            )
//            ->addOrderRow( WebPayItem::orderRow()
//                ->setQuantity(1)
//                ->setAmountExVat(100.00)
//                ->setVatPercent(25)
//            )
//            ->addOrderRow( WebPayItem::orderRow()
//                ->setQuantity(1)
//                ->setAmountExVat(100.00)
//                ->setVatPercent(12)
//            )
//        ;
//            
//        $response = $order
//            ->usePayPageCardOnly()
//            ->setPayPageLanguage($orderLanguage)
//            ->setReturnUrl($returnUrl)
//            ->getPaymentURL();       
//            
//        // check that request was accepted
//        $this->assertEquals( 1, $response->accepted );                
//
//        // print the url to use to confirm the transaction
//        print_r( "\n test_manual_CancelOrderBuilderRows_Card_single_row_success_step_1(): " . $response->testurl ."\n ");
//    }
//
//    /**
//     * test_manual_CancelOrderBuilderRows_Card_single_row_success_step_2
//     * 
//     * run this test manually after you've performed a direct bank transaction and have gotten the transaction details needed
//     */  
//    function test_manual_CancelOrderBuilderRows_Card_single_row_success_step_2() {
//        
//        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'test_manual_CancelOrderBuilderRows_Card_single_row_success_step_2, step 2'
//        );
//        
//        // 1. remove (put in a comment) the above code to enable the test
//        // 2. set $createdOrderId to the transactionid from the transaction log of the request done by following the url from step 1 above.
//        // 3. below is an example of the xml generated by paypage for the request in step 1 above, along with the transaction id, for reference.
//             
//        $createdOrderId = 582695;
//        $country = "SE";
//
//        // query orderrows
//        $queryOrderBuilder = WebPayAdmin::queryOrder( Svea\SveaConfig::getDefaultConfig() )
//            ->setOrderId( $createdOrderId )
//            ->setCountryCode($country)
//        ;     
//        $queryResponse = $queryOrderBuilder->queryCardOrder()->doRequest(); 
//
//        //print_r( $queryResponse);
//        $this->assertEquals(1, $queryResponse->accepted);
//        $this->assertEquals(48700, $queryResponse->amount);
//        $this->assertEquals(8700,  $queryResponse->vat);
//        $this->assertEquals(48700, $queryResponse->authorizedamount);
//
//        // cancel first order row
//        $cancelOrderRowsBuilder = WebPayAdmin::cancelOrderRows( Svea\SveaConfig::getDefaultConfig() )
//            ->setOrderId( $createdOrderId )
//            ->setCountryCode($country)
//            ->setRowToCancel(1)
//            ->setRowToCancel(3)
//            ->setNumberedOrderRows($queryResponse->numberedOrderRows)
//        ;
//        $cancelOrderRowsResponse = $cancelOrderRowsBuilder->cancelCardOrderRows()->doRequest();       
//        $this->assertEquals(1, $cancelOrderRowsResponse->accepted);
//
//        // query orderrows
//        $queryOrderBuilder = WebPayAdmin::queryOrder( Svea\SveaConfig::getDefaultConfig() )
//            ->setOrderId( $createdOrderId )
//            ->setCountryCode($country)
//        ;
//                
//        $query2Response = $queryOrderBuilder->queryCardOrder()->doRequest(); 
//        
//        //print_r( $query2Response);
//        $this->assertEquals(1, $query2Response->accepted);
//        $this->assertEquals(1, $query2Response->accepted);
//        $this->assertEquals(48700, $query2Response->amount);
//        $this->assertEquals(8700,  $query2Response->vat);
//        $this->assertEquals(12500, $query2Response->authorizedamount);
//
//    //Svea\QueryTransactionResponse Object
//    //(
//    //    [rawQueryTransactionsResponse] => /.../
//    //
//    //    [transactionId] => 582695
//    //    [customerrefno] => clientOrderNumber:2014-05-23T11:21:56 02:00
//    //    [merchantid] => 1130
//    //    [status] => AUTHORIZED
//    //    [amount] => 48700
//    //    [currency] => SEK
//    //    [vat] => 8700
//    //    [capturedamount] => 
//    //    [authorizedamount] => 12500
//    //    [created] => 2014-05-23 11:22:08.067
//    //    [creditstatus] => CREDNONE
//    //    [creditedamount] => 0
//    //    [merchantresponsecode] => 0
//    //    [paymentmethod] => KORTCERT
//    //    [numberedOrderRows] => Array
//    //        (
//    //            [0] => Svea\NumberedOrderRow Object
//    //                (
//    //                    [creditInvoiceId] => 
//    //                    [invoiceId] => 
//    //                    [rowNumber] => 1
//    //                    [status] => 
//    //                    [articleNumber] => 
//    //                    [quantity] => 2
//    //                    [unit] => 
//    //                    [amountExVat] => 100
//    //                    [amountIncVat] => 
//    //                    [vatPercent] => 25
//    //                    [name] => 
//    //                    [description] => 
//    //                    [discountPercent] => 
//    //                    [vatDiscount] => 0
//    //                )
//    //
//    //            [1] => Svea\NumberedOrderRow Object
//    //                (
//    //                    [creditInvoiceId] => 
//    //                    [invoiceId] => 
//    //                    [rowNumber] => 2
//    //                    [status] => 
//    //                    [articleNumber] => 
//    //                    [quantity] => 1
//    //                    [unit] => 
//    //                    [amountExVat] => 100
//    //                    [amountIncVat] => 
//    //                    [vatPercent] => 25
//    //                    [name] => 
//    //                    [description] => 
//    //                    [discountPercent] => 
//    //                    [vatDiscount] => 0
//    //                )
//    //
//    //            [2] => Svea\NumberedOrderRow Object
//    //                (
//    //                    [creditInvoiceId] => 
//    //                    [invoiceId] => 
//    //                    [rowNumber] => 3
//    //                    [status] => 
//    //                    [articleNumber] => 
//    //                    [quantity] => 1
//    //                    [unit] => 
//    //                    [amountExVat] => 100
//    //                    [amountIncVat] => 
//    //                    [vatPercent] => 12
//    //                    [name] => 
//    //                    [description] => 
//    //                    [discountPercent] => 
//    //                    [vatDiscount] => 0
//    //                )
//    //
//    //        )
//    //
//    //    [accepted] => 1
//    //    [resultcode] => 0
//    //    [errormessage] => 
//    //)
//    //        
//    }
//    
//    // AddOrderRows() ->addInvoiceOrderRows() ->addPaymentPlanOrderRows() w/WebPayItem::OrderRow/ShippingFee/InvoiceFee/FixedDiscount/RelativeDiscount
//    function test_AddOrderRows_addInvoiceOrderRows_single_row_success() {
//        $country = "SE";
//        $order = TestUtil::createOrderWithoutOrderRows( TestUtil::createIndividualCustomer($country) );
//        $order->addOrderRow(TestUtil::createOrderRow(1.00));        
//        $orderResponse = $order->useInvoicePayment()->doRequest();       
//        $this->assertEquals(1, $orderResponse->accepted);
//         
//        $addOrderRowsResponse = WebPayAdmin::addOrderRows( Svea\SveaConfig::getDefaultConfig() )
//                ->setOrderId($orderResponse->sveaOrderId)
//                ->setCountryCode($country)
//                ->addOrderRow( TestUtil::createOrderRow(2.00) )
//                ->addInvoiceOrderRows()
//                    ->doRequest();
//        
//        $this->assertEquals(1, $addOrderRowsResponse->accepted);  
//    }
//        
//    function test_AddOrderRows_addInvoiceOrderRows_multiple_rows_success() {
//        $country = "SE";
//        $order = TestUtil::createOrderWithoutOrderRows( TestUtil::createIndividualCustomer($country) );
//        $order->addOrderRow(TestUtil::createOrderRow(1.00, 1));        
//        $orderResponse = $order->useInvoicePayment()->doRequest();       
//        $this->assertEquals(1, $orderResponse->accepted);
//
//        $addOrderRowsResponse = WebPayAdmin::addOrderRows( Svea\SveaConfig::getDefaultConfig() )
//                ->setOrderId($orderResponse->sveaOrderId)
//                ->setCountryCode($country)
//                ->addOrderRow( TestUtil::createOrderRow(2.00, 1) )
//                ->addOrderRow( TestUtil::createOrderRow(3.00, 1) )
//                ->addInvoiceOrderRows()
//                    ->doRequest();
//        
//        //print_r("test_AddOrderRows_addInvoiceOrderRows_single_row_success: "); print_r( $orderResponse->sveaOrderId );
//        $this->assertEquals(1, $addOrderRowsResponse->accepted);         
//    }   
//    
//    function test_AddOrderRows_addPaymentPlanOrderRows_multiple_rows_success() {
//        $country = "SE";
//        $order = TestUtil::createOrderWithoutOrderRows( TestUtil::createIndividualCustomer($country) );
//        $order->addOrderRow(TestUtil::createOrderRow(2000.00, 1));        
//        $orderResponse = $order->usePaymentPlanPayment( TestUtil::getGetPaymentPlanParamsForTesting($country) )->doRequest();       
//        $this->assertEquals(1, $orderResponse->accepted);
//
//        $addOrderRowsResponse = WebPayAdmin::addOrderRows( Svea\SveaConfig::getDefaultConfig() )
//                ->setOrderId($orderResponse->sveaOrderId)
//                ->setCountryCode($country)
//                ->addOrderRow( TestUtil::createOrderRow(2.00, 1) )
//                ->addOrderRow( TestUtil::createOrderRow(3.00, 1) )
//                ->addPaymentPlanOrderRows()
//                    ->doRequest();
//        
//        //print_r("test_AddOrderRows_addInvoiceOrderRows_single_row_success: "); print_r( $orderResponse->sveaOrderId );
//        $this->assertEquals(1, $addOrderRowsResponse->accepted);         
//    }  
//    
//    function test_AddOrderRows_addInvoiceOrderRows_specified_with_price_specified_using_inc_vat_and_ex_vat() {
//        $country = "SE";
//        $order = TestUtil::createOrderWithoutOrderRows( TestUtil::createIndividualCustomer($country) );       
//        $order->addOrderRow( WebPayItem::orderRow()
//            ->setArticleNumber("1")
//            ->setQuantity( 1 )
//            ->setAmountExVat( 100.00 )
//            ->setVatPercent(25)
//            ->setDescription("Specification")
//            ->setName('Product')
//            ->setUnit("st")
//            ->setDiscountPercent(0)
//        );               
//        $orderResponse = $order->useInvoicePayment()->doRequest();       
//        $this->assertEquals(1, $orderResponse->accepted);
//
//        $addOrderRowsResponse = WebPayAdmin::addOrderRows( Svea\SveaConfig::getDefaultConfig() )
//                ->setOrderId($orderResponse->sveaOrderId)
//                ->setCountryCode($country)
//                ->addOrderRow( WebPayItem::orderRow()
//                    ->setArticleNumber("1")
//                    ->setQuantity( 1 )
//                    //->setAmountExVat( 1.00 )
//                    ->setAmountIncVat( 1.00 * 1.25 ) 
//                    ->setVatPercent(25)
//                    ->setDescription("Specification")
//                    ->setName('Product')
//                    ->setUnit("st")
//                    ->setDiscountPercent(0)
//                ) 
//                ->addOrderRow( WebPayItem::orderRow()
//                    ->setArticleNumber("1")
//                    ->setQuantity( 1 )
//                    ->setAmountExVat( 4.00 )
//                    ->setAmountIncVat( 4.00 * 1.25 ) 
//                    //->setVatPercent(25)
//                    ->setDescription("Specification")
//                    ->setName('Product')
//                    ->setUnit("st")
//                    ->setDiscountPercent(0)
//                )                
//                ->addInvoiceOrderRows()
//                    ->doRequest();
//        
//        //print_r("test_AddOrderRows_addInvoiceOrderRows_specified_with_price_specified_using_inc_vat_and_ex_vat: "); print_r( $orderResponse->sveaOrderId );
//        $this->assertEquals(1, $addOrderRowsResponse->accepted);
//        // todo query result & check amounts, description automatically
//    }   
// 
    function test_UpdateOrderRows_updateInvoiceOrderRows_single_row_success() {
        $country = "SE";

        // create order
        $order = TestUtil::createOrderWithoutOrderRows( TestUtil::createIndividualCustomer($country) );
        $order->addOrderRow( WebPayItem::orderRow()
            ->setArticleNumber("1")
            ->setQuantity( 1 )
            ->setAmountExVat( 1.00 )
            ->setVatPercent(25)
            ->setDescription("A Specification")
            ->setName('A Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );      
        $order->addOrderRow( WebPayItem::orderRow()
            ->setArticleNumber("2")
            ->setQuantity( 1 )
            ->setAmountExVat( 2.00 )
            ->setVatPercent(25)
            ->setDescription("B Specification")
            ->setName('B Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );         $orderResponse = $order->useInvoicePayment()->doRequest();       
        $this->assertEquals(1, $orderResponse->accepted);

        // query order
        $query = WebPayAdmin::queryOrder( Svea\SveaConfig::getDefaultConfig() );
        $query->setCountryCode($country)->setOrderId($orderResponse->sveaOrderId);
        $queryResponse = $query->queryInvoiceOrder()->doRequest();
        
        //print_r($queryResponse);
        $this->assertEquals(1, $queryResponse->accepted);

        // update all attributes for a numbered orderRow
    
        $updateOrderRowsResponse = WebPayAdmin::updateOrderRows( Svea\SveaConfig::getDefaultConfig() )
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode($country)
                ->updateOrderRow( WebPayItem::numberedOrderRow()
                    ->setArticleNumber("10")
                    ->setQuantity( 1 )
                    ->setAmountExVat( 10.00 )
                    ->setVatPercent(26)
                    ->setDescription("K Specification")
                    ->setName('K Name')
                    ->setUnit("st")
                    ->setDiscountPercent(1)
//                    ->setCreditInvoiceId()
//                    ->setInvoiceId()
                    ->setRowNumber(1)
                    ->setStatus(Svea\NumberedOrderRow::ORDERROWSTATUS_NOTDELIVERED)
                )    
                ->updateInvoiceOrderRows()
                    ->doRequest();
        
//        print_r( $updateOrderRowsResponse );
        print_r("test_UpdateOrderRows_updateInvoiceOrderRows_single_row_success: "); print_r( $orderResponse->sveaOrderId );
        $this->assertEquals(1, $updateOrderRowsResponse->accepted);
        // todo query result & check amounts, description automatically        
    }
}
