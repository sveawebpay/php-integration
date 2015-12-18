<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/** helper class, used to return information about an order */
class orderToCreditAmount {
    var $orderId;
    var $invoiceId;
    var $contractNumber;

    function orderToCreditAmount( $orderId, $invoiceId = NULL, $contractNumber = NULL ) {
        $this->orderId = $orderId;
        $this->invoiceId = $invoiceId;
        $this->contractNumber = $contractNumber;
    }
}

/**
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class CreditAmountRequestIntegrationTest extends PHPUnit_Framework_TestCase {

    /** helper function, returns invoice for delivered order with one row, sent with PriceIncludingVat flag set to true */
    public function get_orderInfo_sent_inc_vat( $amount, $vat, $quantity, $is_paymentplan = NULL) {
        $config = Svea\SveaConfig::getDefaultConfig();
        if ($is_paymentplan)
            $campaignCode = TestUtil::getGetPaymentPlanParamsForTesting();

        $orderResponse = WebPay::createOrder($config)
                ->addOrderRow(
                        WebPayItem::orderRow()
                        ->setAmountIncVat($amount)
                        ->setVatPercent($vat)
                        ->setQuantity($quantity)
                )
                ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
                ->setCountryCode("SE")
                ->setOrderDate("2012-12-12");
        if ($is_paymentplan) {
            $orderResponse = $orderResponse->usePaymentPlanPayment($campaignCode)
                        ->doRequest();
        }
        $this->assertEquals(1, $orderResponse->accepted);
                if ($is_paymentplan) {
                     $deliver = WebPay::deliverOrder($config)
                                ->setOrderId($orderResponse->sveaOrderId)
                                ->setCountryCode('SE')
                                ->deliverPaymentPlanOrder()->doRequest();
                }
        $this->assertEquals(1, $deliver->accepted);

      return new orderToCreditAmount( $orderResponse->sveaOrderId, NULL, $deliver->contractNumber );

    }

    /** helper function, returns invoice for delivered order with one row, sent with PriceIncludingVat flag set to false */
    public function get_orderInfo_sent_ex_vat( $amount, $vat, $quantity, $is_paymentplan = NULL ) {
        $config = Svea\SveaConfig::getDefaultConfig();
         if ($is_paymentplan)
            $campaignCode = TestUtil::getGetPaymentPlanParamsForTesting();

        $orderResponse = WebPay::createOrder($config)
                ->addOrderRow(
                        WebPayItem::orderRow()
                        ->setAmountExVat($amount)
                        ->setVatPercent($vat)
                        ->setQuantity($quantity)
                )
                ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
                ->setCountryCode("SE")
                ->setOrderDate("2012-12-12");
        if ($is_paymentplan) {
            $orderResponse = $orderResponse->usePaymentPlanPayment($campaignCode)
                        ->doRequest();
        }
        $this->assertEquals(1, $orderResponse->accepted);

        if ($is_paymentplan) {
                     $deliver = WebPay::deliverOrder($config)
                                ->setOrderId($orderResponse->sveaOrderId)
                                ->setCountryCode('SE')
                                ->deliverPaymentPlanOrder()->doRequest();
                }
        $this->assertEquals(1, $deliver->accepted);
        return  new orderToCreditAmount( $orderResponse->sveaOrderId, NULL, $deliver->contractNumber );

    }


    public function test_creditAmount_creditPaymentPlan_on_order_ex_vat() {
        $config = Svea\SveaConfig::getDefaultConfig();

        $orderInfo = $this->get_orderInfo_sent_ex_vat( 999.99, 24, 1, TRUE );
        $credit = WebPayAdmin::creditAmount($config)
                ->setContractNumber($orderInfo->contractNumber)
                ->setCountryCode('SE')
                ->setDescription('credit desc')
                ->setAmountIncVat(100.00)
                ->creditPaymentPlanAmount()->doRequest();

        $this->assertEquals(1, $credit->accepted);
        //print_r($credit);
    }

    public function test_creditAmount_creditPaymentPlan_on_order_inc_vat() {
        $config = Svea\SveaConfig::getDefaultConfig();

        $orderInfo = $this->get_orderInfo_sent_inc_vat( 1000.00, 25, 1, TRUE );
       $credit = WebPayAdmin::creditAmount($config)
                ->setContractNumber($orderInfo->contractNumber)
                ->setCountryCode('SE')
                ->setDescription('credit desc')
                ->setAmountIncVat(100.00)
                ->creditPaymentPlanAmount()->doRequest();

       $this->assertEquals(1, $credit->accepted);
    }
    public function test_creditAmount_creditPaymentPlan_amount_exceeds_orderamount() {
        $config = Svea\SveaConfig::getDefaultConfig();

        $orderInfo = $this->get_orderInfo_sent_inc_vat( 1000.00, 25, 1, TRUE );
       $credit = WebPayAdmin::creditAmount($config)
                ->setContractNumber($orderInfo->contractNumber)
                ->setCountryCode('SE')
                ->setDescription('credit desc')
                ->setAmountIncVat(1500.00)
                ->creditPaymentPlanAmount()->doRequest();

        $this->assertEquals(0, $credit->accepted);
    }

}