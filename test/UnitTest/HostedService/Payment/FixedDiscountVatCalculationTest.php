<?php

use Svea\WebPay\Constant\PaymentMethod;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen
 */
class BvDiscountTest extends PHPUnit_Framework_TestCase {
    
    public function test_bv_order_sent_incvat_two_decimals_with_both_discounts() {        
//    print_r("\n\n-----test_bv_order_sent_incvat_two_decimals_with_both_discounts()\n");
    
        $order = WebPay::createOrder(\Svea\WebPay\Config\ConfigurationService::getDefaultConfig())
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("1337")
            ->setOrderDate("2015-05-20")
            ->setCurrency("SEK")
            ->addOrderRow(
                WebPayItem::orderRow()
                ->setAmountIncVat(1.00)
                ->setVatPercent(6)
                ->setQuantity(800)
                ->setName("3.00i@6%*800")
            )
            ->setClientOrderNumber(date('c'))
        ;
        $order->
            addDiscount(
                WebPayItem::fixedDiscount()
                ->setAmountIncVat(240)
                ->setVatPercent(6)
                ->setDiscountId("fixedDiscount")
                ->setName("-240i@6%*1")
            )
        ;
        $order->
            addDiscount(
                WebPayItem::fixedDiscount()
                ->setAmountIncVat(20)
                ->setVatPercent(6)
                ->setDiscountId("fixedDiscount2")
                ->setName("-20i@6%*1")
            )
        ;        $request = $order->usePaymentMethod(PaymentMethod::KORTCERT)
                ->setReturnUrl("https://test.sveaekonomi.se/webpay-admin/admin/merchantresponsetest.xhtml")
                ->getPaymentForm();
        
//    print_r( $request->xmlMessage );
   
        // 240i@6% => 240 (13,58491) => 24000 (1358)
        $expectedDiscountRow =
        "  <row>\n".
        "   <sku>fixedDiscount</sku>\n".
        "   <name>-240i@6%*1</name>\n".
        "   <description></description>\n".
        "   <amount>-24000</amount>\n".      
        "   <vat>-1358</vat>\n";            
        "   <quantity>1</quantity>\n".
        "  </row>\n";   
        $this->assertEquals(1, substr_count($request->xmlMessage, $expectedDiscountRow));

        // 20i@6% => 2000 (1,132076) => 2000 (113)
        $expectedDiscountRow2 =
        "  <row>\n".
        "   <sku>fixedDiscount2</sku>\n".
        "   <name>-20i@6%*1</name>\n".
        "   <description></description>\n".
        "   <amount>-2000</amount>\n".      
        "   <vat>-113</vat>\n";            
        "   <quantity>1</quantity>\n".
        "  </row>\n";            
        $this->assertEquals(1, substr_count($request->xmlMessage, $expectedDiscountRow2));        
             
        // lagt ordern med den dumpade xml:en från utskriften i tools/payment, ger detta response:        

        //<response>
        //  <transaction id="600089">
        //    <paymentmethod>KORTCERT</paymentmethod>
        //    <merchantid>1130</merchantid>
        //    <customerrefno>2015-05-20T17:10:39 02:00</customerrefno>
        //    <amount>54000</amount>
        //    <currency>SEK</currency>
        //    <cardtype>VISA</cardtype>
        //    <maskedcardno>444433xxxxxx1100</maskedcardno>
        //    <expirymonth>01</expirymonth>
        //    <expiryyear>16</expiryyear>
        //    <authcode>304397</authcode>
        //    <customer>
        //      <firstname/>
        //      <lastname/>
        //      <initials/>
        //      <email/>
        //      <ssn>194605092222</ssn>
        //      <address/>
        //      <address2/>
        //      <city/>
        //      <country>SE</country>
        //      <zip/>
        //      <phone/>
        //      <vatnumber/>
        //      <housenumber/>
        //      <companyname/>
        //      <fullname/>
        //    </customer>
        //  </transaction>
        //  <statuscode>0</statuscode>
        //</response>        
    }
    
    public function test_bv_order_sent_incvat_two_decimals_with_both_discounts_with_amount_only() {        
//    print_r("\n\n-----test_bv_order_sent_incvat_two_decimals_with_both_discounts_with_amount_only()\n");
    
        $order = WebPay::createOrder(\Svea\WebPay\Config\ConfigurationService::getDefaultConfig())
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("1337")
            ->setOrderDate("2015-05-20")
            ->setCurrency("SEK")
            ->addOrderRow(
                WebPayItem::orderRow()
                ->setAmountIncVat(1.00)
                ->setVatPercent(6)
                ->setQuantity(800)
                ->setName("3.00i@6%*800")
            )
            ->setClientOrderNumber(date('c'))
        ;
        $order->
            addDiscount(
                WebPayItem::fixedDiscount()
                ->setAmountIncVat(240)
                ->setDiscountId("fixedDiscount")
                ->setName("-240i*1")
            )
        ;
        $order->
            addDiscount(
                WebPayItem::fixedDiscount()
                ->setAmountIncVat(20)
                ->setDiscountId("fixedDiscount2")
                ->setName("-20i*1")
            )
        ;        
        $request = $order->usePaymentMethod(PaymentMethod::KORTCERT)->setReturnUrl("https://test.sveaekonomi.se/webpay-admin/admin/merchantresponsetest.xhtml");
        $request = $request->getPaymentForm();
        
//    print_r( $request->xmlMessage );
   
        // 240i@6% => 240 (13,58491) => 24000 (1358)
        $expectedDiscountRow =
        "  <row>\n".
        "   <sku>fixedDiscount</sku>\n".
        "   <name>-240i*1</name>\n".
        "   <description></description>\n".
        "   <amount>-24000</amount>\n".      
        "   <vat>-1358</vat>\n";            
        "   <quantity>1</quantity>\n".
        "  </row>\n";   
        $this->assertEquals(1, substr_count($request->xmlMessage, $expectedDiscountRow));

        // 20i@6% => 2000 (1,132076) => 2000 (113)
        $expectedDiscountRow2 =
        "  <row>\n".
        "   <sku>fixedDiscount2</sku>\n".
        "   <name>-20i*1</name>\n".
        "   <description></description>\n".
        "   <amount>-2000</amount>\n".      
        "   <vat>-113</vat>\n";            
        "   <quantity>1</quantity>\n".
        "  </row>\n";            
        $this->assertEquals(1, substr_count($request->xmlMessage, $expectedDiscountRow2));                     

        // lagt ordern med den dumpade xml:en från utskriften i tools/payment, ger detta response:                
        
        //<response>
        //  <transaction id="600123">
        //    <paymentmethod>KORTCERT</paymentmethod>
        //    <merchantid>1130</merchantid>
        //    <customerrefno>2015-05-22T13:00:54 02:00</customerrefno>
        //    <amount>54000</amount>
        //    <currency>SEK</currency>
        //    <cardtype>VISA</cardtype>
        //    <maskedcardno>444433xxxxxx1100</maskedcardno>
        //    <expirymonth>02</expirymonth>
        //    <expiryyear>17</expiryyear>
        //    <authcode>594378</authcode>
        //    <customer>
        //      <firstname/>
        //      <lastname/>
        //      <initials/>
        //      <email/>
        //      <ssn>194605092222</ssn>
        //      <address/>
        //      <address2/>
        //      <city/>
        //      <country>SE</country>
        //      <zip/>
        //      <phone/>
        //      <vatnumber/>
        //      <housenumber/>
        //      <companyname/>
        //      <fullname/>
        //    </customer>
        //  </transaction>
        //  <statuscode>0</statuscode>
        //</response>    
    }    
}