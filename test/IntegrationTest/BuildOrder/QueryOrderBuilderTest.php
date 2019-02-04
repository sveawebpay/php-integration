<?php

namespace Svea\WebPay\Test\IntegrationTest\BuildOrder;

use \PHPUnit\Framework\TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\WebPayItem;


/**
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class QueryOrderBuilderIntegrationTest extends \PHPUnit\Framework\TestCase
{

    /**
     *  test_queryOrder_queryInvoiceOrder_order
     */
    function test_queryOrder_queryInvoiceOrder_multiple_order_rows()
    {
        // create invoice order w/three rows (2xA, 1xB)
        $country = "SE";

        $a_quantity = 2;
        $a_amountExVat = 1000.00;
        $a_vatPercent = 25;

        $b_quantity = 1;
        $b_amountExVat = 100.00;
        $b_vatPercent = 12;
        $b_articleNumber = "1071e";
        $b_unit = "pcs.";
        $b_name = "B Name";
        $b_description = "B Description";
        $b_discount = 0;

        $order = TestUtil::createOrderWithoutOrderRows(TestUtil::createIndividualCustomer($country))
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity($a_quantity)
                ->setAmountExVat($a_amountExVat)
                ->setVatPercent($a_vatPercent)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity($b_quantity)
                ->setAmountExVat($b_amountExVat)
                ->setVatPercent($b_vatPercent)
                ->setArticleNumber($b_articleNumber)
                ->setUnit($b_unit)
                ->setName($b_name)
                ->setDescription($b_description)
                ->setDiscountPercent($b_discount)
            );
        $orderResponse = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $createdOrderId = $orderResponse->sveaOrderId;

        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($createdOrderId)
            ->setCountryCode($country);

        $queryResponse = $queryOrderBuilder->queryInvoiceOrder()->doRequest();

        ////print_r( $queryResponse);
        $this->assertEquals(1, $queryResponse->accepted);
        // assert that order rows are the same
        $this->assertEquals($a_quantity, $queryResponse->numberedOrderRows[0]->quantity);
        $this->assertEquals($a_amountExVat, $queryResponse->numberedOrderRows[0]->amountExVat);

        $this->assertEquals($b_quantity, $queryResponse->numberedOrderRows[1]->quantity);
        $this->assertEquals($b_amountExVat, $queryResponse->numberedOrderRows[1]->amountExVat);
        $this->assertEquals($b_vatPercent, $queryResponse->numberedOrderRows[1]->vatPercent);
        $this->assertEquals($b_articleNumber, $queryResponse->numberedOrderRows[1]->articleNumber);
        $this->assertEquals($b_unit, $queryResponse->numberedOrderRows[1]->unit);
        $this->assertStringStartsWith($b_name, $queryResponse->numberedOrderRows[1]->description);
        $this->assertStringEndsWith($b_description, $queryResponse->numberedOrderRows[1]->description);
        $this->assertEquals($b_discount, $queryResponse->numberedOrderRows[1]->discountPercent);

        $this->assertEquals(null, $queryResponse->numberedOrderRows[1]->creditInvoiceId); // not set
        $this->assertEquals(null, $queryResponse->numberedOrderRows[1]->invoiceId); // not set
        $this->assertEquals(2, $queryResponse->numberedOrderRows[1]->rowNumber);  // rows are 1-indexed
        $this->assertEquals("NotDelivered", $queryResponse->numberedOrderRows[1]->status);
    }

    function test_queryOrder_queryInvoiceOrder_single_order_row()
    {
        // create invoice order w/three rows (2xA, 1xB)
        $country = "SE";

        $a_quantity = 2;
        $a_amountExVat = 1000.00;
        $a_vatPercent = 25;

        $order = TestUtil::createOrderWithoutOrderRows(TestUtil::createIndividualCustomer($country))
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity($a_quantity)
                ->setAmountExVat($a_amountExVat)
                ->setVatPercent($a_vatPercent)
            );
        $orderResponse = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $createdOrderId = $orderResponse->sveaOrderId;

        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($createdOrderId)
            ->setCountryCode($country);

        $queryResponse = $queryOrderBuilder->queryInvoiceOrder()->doRequest();

        ////print_r( $queryResponse);
        $this->assertEquals(1, $queryResponse->accepted);
        // assert that order rows are the same
        $this->assertEquals($a_quantity, $queryResponse->numberedOrderRows[0]->quantity);
        $this->assertEquals($a_amountExVat, $queryResponse->numberedOrderRows[0]->amountExVat);
    }

    /**
     *  test_queryOrder_queryPaymentPlanOrder_order
     */
    function test_queryOrder_queryPaymentPlanOrder_order()
    {
        // create order w/three rows (2xA, 1xB)
        $country = "SE";

        $a_quantity = 2;
        $a_amountExVat = 1000.00;
        $a_vatPercent = 25;

        $b_quantity = 1;
        $b_amountExVat = 100.00;
        $b_vatPercent = 12;
        $b_articleNumber = "1071e";
        $b_unit = "pcs.";
        $b_name = "B Name";
        $b_description = "B Description";
        $b_discount = 0;

        $order = TestUtil::createOrderWithoutOrderRows(TestUtil::createIndividualCustomer($country))
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity($a_quantity)
                ->setAmountExVat($a_amountExVat)
                ->setVatPercent($a_vatPercent)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity($b_quantity)
                ->setAmountExVat($b_amountExVat)
                ->setVatPercent($b_vatPercent)
                ->setArticleNumber($b_articleNumber)
                ->setUnit($b_unit)
                ->setName($b_name)
                ->setDescription($b_description)
                ->setDiscountPercent($b_discount)
            );
        $orderResponse = $order->usePaymentPlanPayment(TestUtil::getGetPaymentPlanParamsForTesting())->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $createdOrderId = $orderResponse->sveaOrderId;

        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($createdOrderId)
            ->setCountryCode($country);

        $queryResponse = $queryOrderBuilder->queryPaymentPlanOrder()->doRequest();

        ////print_r( $queryResponse);
        $this->assertEquals(1, $queryResponse->accepted);
        // assert that order rows are the same
        $this->assertEquals($a_quantity, $queryResponse->numberedOrderRows[0]->quantity);
        $this->assertEquals($a_amountExVat, $queryResponse->numberedOrderRows[0]->amountExVat);

        $this->assertEquals($b_quantity, $queryResponse->numberedOrderRows[1]->quantity);
        $this->assertEquals($b_amountExVat, $queryResponse->numberedOrderRows[1]->amountExVat);
        $this->assertEquals($b_vatPercent, $queryResponse->numberedOrderRows[1]->vatPercent);
        $this->assertEquals($b_articleNumber, $queryResponse->numberedOrderRows[1]->articleNumber);
        $this->assertEquals($b_unit, $queryResponse->numberedOrderRows[1]->unit);
        $this->assertStringStartsWith($b_name, $queryResponse->numberedOrderRows[1]->description);
        $this->assertStringEndsWith($b_description, $queryResponse->numberedOrderRows[1]->description);
        $this->assertEquals($b_discount, $queryResponse->numberedOrderRows[1]->discountPercent);

        $this->assertEquals(null, $queryResponse->numberedOrderRows[1]->creditInvoiceId); // not set
        $this->assertEquals(null, $queryResponse->numberedOrderRows[1]->invoiceId); // not set
        $this->assertEquals(2, $queryResponse->numberedOrderRows[1]->rowNumber);  // rows are 1-indexed
        $this->assertEquals("NotDelivered", $queryResponse->numberedOrderRows[1]->status);
    }

    /**
     * test_manual_queryOrder_queryCard_order_step_1
     *
     */
    function test_manual_queryOrder_queryCard_order_step_1()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for manual test_manual_queryOrder_queryCard_order_step_1, step 1'
        );

        // 1. remove (put in a comment) the above code to enable the test
        // 2. run the test, and get the paymenturl from the output
        // 3. go to the paymenturl and complete the transaction manually, making note of the response transactionid
        // 4. enter the transactionid into test_manual_queryOrder_queryCard_order_step_12() below and run the test

        $orderLanguage = "sv";
        $returnUrl = "http://foo.bar.com";
        $ipAddress = "127.0.0.1";

        // create order
        $order = TestUtil::createOrderWithoutOrderRows(TestUtil::createIndividualCustomer("SE")->setIpAddress($ipAddress));

        // create order w/three rows (2xA, 1xB)
        $country = "SE";

        $a_quantity = 2;
        $a_amountExVat = 1000.00;
        $a_vatPercent = 25;

        $b_quantity = 1;
        $b_amountExVat = 100.00;
        $b_vatPercent = 12;
        $b_articleNumber = "1071e";
        $b_unit = "pcs.";
        $b_name = "B Name";
        $b_description = "B Description";
        $b_discount = 0;

        $order
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity($a_quantity)
                ->setAmountExVat($a_amountExVat)
                ->setVatPercent($a_vatPercent)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity($b_quantity)
                ->setAmountExVat($b_amountExVat)
                ->setVatPercent($b_vatPercent)
                ->setArticleNumber($b_articleNumber)
                ->setUnit($b_unit)
                ->setName($b_name)
                ->setDescription($b_description)
                ->setDiscountPercent($b_discount)
            );

        // set payment method
        // call getPaymentURL
        $response = $order
            ->usePayPageCardOnly()
            ->setPayPageLanguage($orderLanguage)
            ->setReturnUrl($returnUrl)
            ->getPaymentUrl();

        // check that request was accepted
        $this->assertEquals(1, $response->accepted);

        // print the url to use to confirm the transaction
        //print_r( " test_manual_queryOrder_queryCard_order_step_1(): " . $response->testurl ." ");
    }

    /**
     * test_manual_queryOrder_queryCard_order_step_2
     *
     * run this test manually after you've performed a card transaction and have gotten the transaction details needed
     */
    function test_manual_queryOrder_queryCard_order_step_2()
    {

        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'skeleton for manual test_manual_queryOrder_queryCard_order_step_2, step 2'
//        );

        // 1. remove (put in a comment) the above code to enable the test
        // 2. set $createdOrderId to the transactionid from the transaction log of the request done by following the url from step 1 above.
        // 3. below is an example of the xml generated by paypage for the request in step 1 above, along with the transaction id, for reference.

        $createdOrderId = 582616;

        // create order w/three rows (2xA, 1xB) (from step 1 above)
        $country = "SE";

        $a_quantity = 2;
        $a_amountExVat = 1000.00;
        $a_vatPercent = 25;

        $b_quantity = 1;
        $b_amountExVat = 100.00;
        $b_vatPercent = 12;
        $b_articleNumber = "1071e";
        $b_unit = "pcs.";
        $b_name = "B Name";
        $b_description = "B Description";
        $b_discount = 0;

        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($createdOrderId)
            ->setCountryCode($country);

        $queryResponse = $queryOrderBuilder->queryCardOrder()->doRequest();

        ////print_r( $queryResponse);
        $this->assertEquals(1, $queryResponse->accepted);

        // assert that order rows are the same 
        $this->assertEquals($a_quantity, $queryResponse->numberedOrderRows[0]->quantity);
        $this->assertEquals($a_amountExVat, $queryResponse->numberedOrderRows[0]->amountExVat);

        $this->assertEquals($b_quantity, $queryResponse->numberedOrderRows[1]->quantity);
        $this->assertEquals($b_amountExVat, $queryResponse->numberedOrderRows[1]->amountExVat);
        $this->assertEquals($b_vatPercent, $queryResponse->numberedOrderRows[1]->vatPercent);
        $this->assertEquals($b_articleNumber, $queryResponse->numberedOrderRows[1]->articleNumber);
        $this->assertEquals($b_unit, $queryResponse->numberedOrderRows[1]->unit);
        $this->assertStringStartsWith($b_name, $queryResponse->numberedOrderRows[1]->name);
        $this->assertStringEndsWith($b_description, $queryResponse->numberedOrderRows[1]->description);
        $this->assertEquals($b_discount, $queryResponse->numberedOrderRows[1]->discountPercent);
    }

    /**
     * test_manual_queryOrder_queryDirectBank_order_step_1
     *
     */
    function test_manual_queryOrder_queryDirectBank_order_step_1()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for test_manual_queryOrder_queryDirectBank_order_step_1, step 1'
        );

        // 1. remove (put in a comment) the above code to enable the test
        // 2. run the test, and get the paymenturl from the output
        // 3. go to the paymenturl and complete the transaction manually, making note of the response transactionid
        // 4. enter the transactionid into test_manual_queryOrder_queryCard_order_step_12() below and run the test

        $orderLanguage = "sv";
        $returnUrl = "http://foo.bar.com";
        $ipAddress = "127.0.0.1";

        // create order
        $order = TestUtil::createOrderWithoutOrderRows(TestUtil::createIndividualCustomer("SE")->setIpAddress($ipAddress));

        // create order w/three rows (2xA, 1xB)
        $country = "SE";

        $a_quantity = 2;
        $a_amountExVat = 1000.00;
        $a_vatPercent = 25;

        $b_quantity = 1;
        $b_amountExVat = 100.00;
        $b_vatPercent = 12;
        $b_articleNumber = "Red 5";
        $b_unit = "pcs.";
        $b_name = "B Name";
        $b_description = "B Description";
        $b_discount = 0;

        $order
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity($a_quantity)
                ->setAmountExVat($a_amountExVat)
                ->setVatPercent($a_vatPercent)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity($b_quantity)
                ->setAmountExVat($b_amountExVat)
                ->setVatPercent($b_vatPercent)
                ->setArticleNumber($b_articleNumber)
                ->setUnit($b_unit)
                ->setName($b_name)
                ->setDescription($b_description)
                ->setDiscountPercent($b_discount)
            );

        // set payment method
        // call getPaymentURL
        $response = $order
            ->usePayPageDirectBankOnly()
            ->setPayPageLanguage($orderLanguage)
            ->setReturnUrl($returnUrl)
            ->getPaymentUrl();

        // check that request was accepted
        $this->assertEquals(1, $response->accepted);

        // print the url to use to confirm the transaction
        //print_r( " test_manual_queryOrder_queryDirectBank_order_step_1(): " . $response->testurl ." ");
    }

    /**
     * test_manual_queryOrder_queryDirectBank_order_step_2
     *
     * run this test manually after you've performed a direct bank transaction and have gotten the transaction details needed
     */
    function test_manual_queryOrder_queryDirectBank_order_step_2()
    {

        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'skeleton for test_manual_queryOrder_queryDirectBank_order_step_2, step 2'
//        );

        // 1. remove (put in a comment) the above code to enable the test
        // 2. set $createdOrderId to the transactionid from the transaction log of the request done by following the url from step 1 above.
        // 3. below is an example of the xml generated by paypage for the request in step 1 above, along with the transaction id, for reference.

        $createdOrderId = 583556;

        // create order w/three rows (2xA, 1xB) (from step 1 above)
        $country = "SE";

        $a_quantity = 2;
        $a_amountExVat = 1000.00;
        $a_vatPercent = 25;

        $b_quantity = 1;
        $b_amountExVat = 100.00;
        $b_vatPercent = 12;
        $b_articleNumber = "Red 5";
        $b_unit = "pcs.";
        $b_name = "B Name";
        $b_description = "B Description";
        $b_discount = 0;

        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($createdOrderId)
            ->setCountryCode($country);

        $queryResponse = $queryOrderBuilder->queryDirectBankOrder()->doRequest();

        ////print_r( $queryResponse);
        $this->assertEquals(1, $queryResponse->accepted);

        //Svea\QueryTransactionResponse Object
        //(
        //    [transactionId] => 582656
        //    [customerrefno] => clientOrderNumber:2014-05-21T11:30:53 02:00
        //    [merchantid] => 1130
        //    [status] => SUCCESS
        //    [amount] => 261200
        //    [currency] => SEK
        //    [vat] => 51200
        //    [capturedamount] => 261200
        //    [authorizedamount] => 261200
        //    [created] => 2014-05-21 11:31:15.697
        //    [creditstatus] => CREDNONE
        //    [creditedamount] => 0
        //    [merchantresponsecode] => 0
        //    [paymentmethod] => DBNORDEASE
        //    [orderrows] => Array
        //        (
        //            [0] => Svea\WebPay\BuildOrder\RowBuilders\OrderRow Object
        //                (
        //                    [articleNumber] => 
        //                    [quantity] => 2
        //                    [unit] => 
        //                    [amountExVat] => 1000
        //                    [amountIncVat] => 
        //                    [vatPercent] => 25
        //                    [name] => 
        //                    [description] => 
        //                    [discountPercent] => 
        //                    [vatDiscount] => 0
        //                )
        //
        //            [1] => Svea\WebPay\BuildOrder\RowBuilders\OrderRow Object
        //                (
        //                    [articleNumber] => Red 5
        //                    [quantity] => 1
        //                    [unit] => pcs.
        //                    [amountExVat] => 100
        //                    [amountIncVat] => 
        //                    [vatPercent] => 12
        //                    [name] => B Name
        //                    [description] => B Description
        //                    [discountPercent] => 
        //                    [vatDiscount] => 0
        //                )
        //
        //        )
        //
        //    [accepted] => 1
        //    [resultcode] => 0
        //    [errormessage] => 
        //)

        // assert that order rows are the same 
        $this->assertEquals($a_quantity, $queryResponse->numberedOrderRows[0]->quantity);
        $this->assertEquals($a_amountExVat, $queryResponse->numberedOrderRows[0]->amountExVat);

        $this->assertEquals($b_quantity, $queryResponse->numberedOrderRows[1]->quantity);
        $this->assertEquals($b_amountExVat, $queryResponse->numberedOrderRows[1]->amountExVat);
        $this->assertEquals($b_vatPercent, $queryResponse->numberedOrderRows[1]->vatPercent);
        $this->assertEquals($b_articleNumber, $queryResponse->numberedOrderRows[1]->articleNumber);
        $this->assertEquals($b_unit, $queryResponse->numberedOrderRows[1]->unit);
        $this->assertStringStartsWith($b_name, $queryResponse->numberedOrderRows[1]->name);
        $this->assertStringEndsWith($b_description, $queryResponse->numberedOrderRows[1]->description);
        $this->assertEquals($b_discount, $queryResponse->numberedOrderRows[1]->discountPercent);
    }

    function test_queryOrder_directbank_by_customerrefno()
    {

        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'skeleton for test_manual_queryOrder_queryDirectBank_order_step_2, step 2'
//        );

        // 1. remove (put in a comment) the above code to enable the test
        // 2. set $createdOrderId to the transactionid from the transaction log of the request done by following the url from step 1 above.
        // 3. below is an example of the xml generated by paypage for the request in step 1 above, along with the transaction id, for reference.

        $createdClientOrderNumber = "clientOrderNumber:2014-06-17T14:18:38 02:00";

        // create order w/three rows (2xA, 1xB) (from step 1 above)
        $country = "SE";

        $a_quantity = 2;
        $a_amountExVat = 1000.00;
        $a_vatPercent = 25;

        $b_quantity = 1;
        $b_amountExVat = 100.00;
        $b_vatPercent = 12;
        $b_articleNumber = "Red 5";
        $b_unit = "pcs.";
        $b_name = "B Name";
        $b_description = "B Description";
        $b_discount = 0;

        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setClientOrderNumber($createdClientOrderNumber)
            ->setCountryCode($country);

        $queryResponse = $queryOrderBuilder->queryDirectBankOrder()->doRequest();

        ////print_r( $queryResponse);
        $this->assertEquals(1, $queryResponse->accepted);

        //Svea\QueryTransactionResponse Object
        //(
        //    [transactionId] => 582656
        //    [customerrefno] => clientOrderNumber:2014-05-21T11:30:53 02:00
        //    [merchantid] => 1130
        //    [status] => SUCCESS
        //    [amount] => 261200
        //    [currency] => SEK
        //    [vat] => 51200
        //    [capturedamount] => 261200
        //    [authorizedamount] => 261200
        //    [created] => 2014-05-21 11:31:15.697
        //    [creditstatus] => CREDNONE
        //    [creditedamount] => 0
        //    [merchantresponsecode] => 0
        //    [paymentmethod] => DBNORDEASE
        //    [orderrows] => Array
        //        (
        //            [0] => Svea\WebPay\BuildOrder\RowBuilders\OrderRow Object
        //                (
        //                    [articleNumber] =>
        //                    [quantity] => 2
        //                    [unit] =>
        //                    [amountExVat] => 1000
        //                    [amountIncVat] =>
        //                    [vatPercent] => 25
        //                    [name] =>
        //                    [description] =>
        //                    [discountPercent] =>
        //                    [vatDiscount] => 0
        //                )
        //
        //            [1] => Svea\WebPay\BuildOrder\RowBuilders\OrderRow Object
        //                (
        //                    [articleNumber] => Red 5
        //                    [quantity] => 1
        //                    [unit] => pcs.
        //                    [amountExVat] => 100
        //                    [amountIncVat] =>
        //                    [vatPercent] => 12
        //                    [name] => B Name
        //                    [description] => B Description
        //                    [discountPercent] =>
        //                    [vatDiscount] => 0
        //                )
        //
        //        )
        //
        //    [accepted] => 1
        //    [resultcode] => 0
        //    [errormessage] =>
        //)

        // assert that order rows are the same
        $this->assertEquals($a_quantity, $queryResponse->numberedOrderRows[0]->quantity);
        $this->assertEquals($a_amountExVat, $queryResponse->numberedOrderRows[0]->amountExVat);

        $this->assertEquals($b_quantity, $queryResponse->numberedOrderRows[1]->quantity);
        $this->assertEquals($b_amountExVat, $queryResponse->numberedOrderRows[1]->amountExVat);
        $this->assertEquals($b_vatPercent, $queryResponse->numberedOrderRows[1]->vatPercent);
        $this->assertEquals($b_articleNumber, $queryResponse->numberedOrderRows[1]->articleNumber);
        $this->assertEquals($b_unit, $queryResponse->numberedOrderRows[1]->unit);
        $this->assertStringStartsWith($b_name, $queryResponse->numberedOrderRows[1]->name);
        $this->assertStringEndsWith($b_description, $queryResponse->numberedOrderRows[1]->description);
        $this->assertEquals($b_discount, $queryResponse->numberedOrderRows[1]->discountPercent);
    }

    function test_queryOrder_card_by_customerrefno()
    {
        $createdClientOrderNumber = "clientOrderNumber:2014-05-20T17:10:35 02:00";

        $country = "SE";

        $a_quantity = 2;
        $a_amountExVat = 1000.00;
        $a_vatPercent = 25;

        $b_quantity = 1;
        $b_amountExVat = 100.00;
        $b_vatPercent = 12;
        $b_articleNumber = "1071e";
        $b_unit = "pcs.";
        $b_name = "B Name";
        $b_description = "B Description";
        $b_discount = 0;

        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setClientOrderNumber($createdClientOrderNumber)
            ->setCountryCode($country);

        $queryResponse = $queryOrderBuilder->queryCardOrder()->doRequest();

        ////print_r( $queryResponse);
        $this->assertEquals(1, $queryResponse->accepted);

        // assert that order rows are the same
        $this->assertEquals($a_quantity, $queryResponse->numberedOrderRows[0]->quantity);
        $this->assertEquals($a_amountExVat, $queryResponse->numberedOrderRows[0]->amountExVat);

        $this->assertEquals($b_quantity, $queryResponse->numberedOrderRows[1]->quantity);
        $this->assertEquals($b_amountExVat, $queryResponse->numberedOrderRows[1]->amountExVat);
        $this->assertEquals($b_vatPercent, $queryResponse->numberedOrderRows[1]->vatPercent);
        $this->assertEquals($b_articleNumber, $queryResponse->numberedOrderRows[1]->articleNumber);
        $this->assertEquals($b_unit, $queryResponse->numberedOrderRows[1]->unit);
        $this->assertStringStartsWith($b_name, $queryResponse->numberedOrderRows[1]->name);
        $this->assertStringEndsWith($b_description, $queryResponse->numberedOrderRows[1]->description);
        $this->assertEquals($b_discount, $queryResponse->numberedOrderRows[1]->discountPercent);
    }

    // query invoice order w/single order row
    // query invoice order w/multiple order rows
    // query payment plan order row(s)
    // query card order row(s)
    // query direct bank order row(s)
    // negative tests?
}


?>