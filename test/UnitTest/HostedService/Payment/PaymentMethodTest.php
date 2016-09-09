<?php

namespace Svea\WebPay\Test\UnitTest\HostedService\Payment;

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\PaymentMethod;
use Svea\WebPay\Constant\SystemPaymentMethod;


/**
 * @author anne-hal, Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class PaymentMethodTest extends \PHPUnit_Framework_TestCase{

     public function testPayPagePaymentWithSetPaymentMethod() {
        $config = ConfigurationService::getDefaultConfig();
         $rowFactory = new TestUtil();
        $form = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->run($rowFactory->buildShippingFee())
            ->addDiscount(WebPayItem::relativeDiscount()
                    ->setDiscountId("1")
                    ->setDiscountPercent(50)
                    ->setUnit("st")
                    ->setName('Relative')
                    ->setDescription("RelativeDiscount")
            )
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setClientOrderNumber("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setReturnUrl("http://myurl.se")
            ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);
        $this->assertEquals(PaymentMethod::KORTCERT, $xmlMessage->paymentmethod[0]);
    }

    public function testPayPagePaymentWithSetPaymentMethodInvoice() {
        $config = ConfigurationService::getDefaultConfig();
        $rowFactory = new TestUtil();
        $form = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->run($rowFactory->buildShippingFee())
            ->addDiscount(WebPayItem::relativeDiscount()
                    ->setDiscountId("1")
                    ->setDiscountPercent(50)
                    ->setUnit("st")
                    ->setName('Relative')
                    ->setDescription("RelativeDiscount")
            )
            ->addCustomerDetails(WebPayItem::companyCustomer()->setNationalIdNumber(4608142222))
            ->setCountryCode("SE")
            ->setClientOrderNumber("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::INVOICE)
                ->setReturnUrl("http://myurl.se")
                ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage); 
        $this->assertEquals(SystemPaymentMethod::INVOICE_SE, $xmlMessage->paymentmethod[0]);
        $this->assertEquals("TRUE", $xmlMessage->iscompany);
        $this->assertEquals("4608142222", $xmlMessage->customer->ssn);
    }

    public function testPaymentMethodInvoiceNL() {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::individualCustomer()
                    ->setInitials("SB")
                    ->setBirthDate(1923, 12, 12)
                    ->setName("Sneider", "Boasman")
                    ->setEmail("test@svea.com")
                    ->setPhoneNumber(999999)
                    ->setIpAddress("123.123.123")
                    ->setStreetAddress("Gatan", 23)
                    ->setCoAddress("c/o Eriksson")
                    ->setZipCode(9999)
                    ->setLocality("Stan")
            )
            ->setCountryCode("NL")
            ->setClientOrderNumber("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::INVOICE)
                ->setReturnUrl("http://myurl.se")
                ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);
        $this->assertEquals("FALSE", $xmlMessage->iscompany);
        $this->assertEquals("Sneider", $xmlMessage->customer->firstname);
    }
    public function testPaymentMethodInvoiceNLCallbackUrl() {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::individualCustomer()
                    ->setInitials("SB")
                    ->setBirthDate(1923, 12, 12)
                    ->setName("Sneider", "Boasman")
                    ->setEmail("test@svea.com")
                    ->setPhoneNumber(999999)
                    ->setIpAddress("123.123.123")
                    ->setStreetAddress("Gatan", 23)
                    ->setCoAddress("c/o Eriksson")
                    ->setZipCode(9999)
                    ->setLocality("Stan")
            )
            ->setCountryCode("NL")
            ->setClientOrderNumber("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::INVOICE)
            ->setReturnUrl("http://myurl.se")
            ->setCallbackUrl("http://myurl.se")
            ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);
        $this->assertEquals("http://myurl.se", $xmlMessage->callbackurl);
    }
}
