<?php
namespace Svea;
use \Svea\HostedService\DirectPayment as DirectPayment;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../test/UnitTest/BuildOrder/OrderBuilderTest.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../TestUtil.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class DirectPaymentTest extends \PHPUnit_Framework_TestCase {

     /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid or missing Country code
     */
    public function testFailOnWrongCountryCodeInConfig() {
        $config = SveaConfig::getDefaultConfig();
        $rowFactory = new \TestUtil();
        $form = \WebPay::createOrder($config)
                ->addOrderRow(\TestUtil::createOrderRow())
            ->run($rowFactory->buildShippingFee())
            ->addCustomerDetails(\WebPayItem::individualCustomer()
                    ->setNationalIdNumber(194605092222)
                    )
            ->setCountryCode("ZZ")
            ->setClientOrderNumber("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->usePayPageDirectBankOnly()
                ->setReturnUrl("http://myurl.se")
                ->getPaymentForm();
        /**
        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);

        $this->assertEquals('KORTCERT', $xmlMessage->excludepaymentmethods->exclude[0]);
        $this->assertEquals('KORTSKRILL', $xmlMessage->excludepaymentmethods->exclude[1]);
        $this->assertEquals('PAYPAL', $xmlMessage->excludepaymentmethods->exclude[2]);
       // $this->assertEquals('SKRILL', $xmlMessage->excludepaymentmethods->exclude[3]);
         *
         */
    }

    public function testConfigureExcludedPaymentMethods() {
        $config = SveaConfig::getDefaultConfig();
        $rowFactory = new \TestUtil();
        $form = \WebPay::createOrder($config)
                ->addOrderRow(\TestUtil::createOrderRow())
            ->run($rowFactory->buildShippingFee())
            ->addCustomerDetails(\WebPayItem::individualCustomer()
                    ->setNationalIdNumber(194605092222)
            )
            ->setCountryCode("SE")
            ->setClientOrderNumber("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->usePayPageDirectBankOnly()
                ->setReturnUrl("http://myurl.se")
                ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);
        
//        $excluded = (array)$xmlMessage->excludepaymentmethods->exclude; // only works with php 5.5
//        $this->assertTrue( in_array( SystemPaymentMethod::KORTCERT, $excluded ) );
//        $this->assertTrue( in_array( SystemPaymentMethod::SKRILL, $excluded ) );
//        $this->assertTrue( in_array( SystemPaymentMethod::PAYPAL, $excluded ) );

        $this->assertEquals( SystemPaymentMethod::KORTCERT, $xmlMessage->excludepaymentmethods->exclude[14] );
        $this->assertEquals( SystemPaymentMethod::SKRILL, $xmlMessage->excludepaymentmethods->exclude[15] );
        $this->assertEquals( SystemPaymentMethod::PAYPAL, $xmlMessage->excludepaymentmethods->exclude[16] );        
    }

    public function testBuildDirectBankPayment() {
        $config = SveaConfig::getDefaultConfig();
        $rowFactory = new \TestUtil();
        $form = \WebPay::createOrder($config)
                ->addOrderRow(\TestUtil::createOrderRow())
                ->addFee(\WebPayItem::shippingFee()
                    ->setShippingId('33')
                    ->setName('shipping')
                    ->setDescription("Specification")
                    ->setAmountExVat(50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                )
                ->addDiscount(\WebPayItem::relativeDiscount()
                    ->setDiscountId("1")
                    ->setDiscountPercent(50)
                    ->setUnit("st")
                    ->setName('Relative')
                    ->setDescription("RelativeDiscount")
                )
                ->addCustomerDetails(\WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setClientOrderNumber("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->usePayPageDirectBankOnly()
                ->setReturnUrl("http://myurl.se")
                ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);
        //test values are as expected avter transforming xml to php object
        $this->assertEquals('SEK', $xmlMessage->currency);
        $this->assertEquals('18750', $xmlMessage->amount);
        $this->assertEquals('3750', $xmlMessage->vat); //may change when we recaltulate in Cartpymentclass
        $this->assertEquals('12500', $xmlMessage->orderrows->row[0]->amount);
        $this->assertEquals('6250', $xmlMessage->orderrows->row[1]->amount);
        $this->assertEquals('-12500', $xmlMessage->orderrows->row[2]->amount);
    }
    public function testBuildDirectBankPaymentCallBackUrl() {
        $config = SveaConfig::getDefaultConfig();
        $rowFactory = new \TestUtil();
        $form = \WebPay::createOrder($config)
                ->addOrderRow(\TestUtil::createOrderRow())
                ->addCustomerDetails(\WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setClientOrderNumber("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->usePayPageDirectBankOnly()
                ->setReturnUrl("http://myurl.se")
                ->setCallbackUrl("http://myurl.se")
                ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);
       $this->assertEquals("http://myurl.se", $xmlMessage->callbackurl);
    }


}
