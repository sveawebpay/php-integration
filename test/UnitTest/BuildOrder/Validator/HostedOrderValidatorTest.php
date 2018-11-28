<?php

namespace Svea\WebPay\Test\UnitTest\BuildOrder\Validator;

use Svea\WebPay\WebPay;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\PaymentMethod;

/**
 * @author Anneli Halld'n, Daniel Brolund, Fredrik Sundell for Svea Webpay
 */
class HostedOrderValidatorTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : ClientOrderNumber is required. Use function setClientOrderNumber().
     */
    public function testFailOnNullCustomerRefNo()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(TestUtil::createHostedOrderRow())
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->usePayPageCardOnly()
            ->setReturnUrl("myurl.se");

        $order->getPaymentForm();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : ClientOrderNumber is required. Use function setClientOrderNumber().
     */
    public function testFailOnEmptyCustomerRefNo()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(TestUtil::createHostedOrderRow())
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setClientOrderNumber("")
            ->usePayPageCardOnly()
            ->setReturnUrl("myurl.se");

        $order->getPaymentForm();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage
     * -missing value : Initials is required for INVOICE and PAYMENTPLAN payments for individual customers when countrycode is NL. Use function setInitials().
     * -missing value : BirthDate is required for INVOICE and PAYMENTPLAN payments for individual customers when countrycode is NL. Use function setBirthDate().
     * -missing value : Name is required for INVOICE and PAYMENTPLAN payments for individual customers when countrycode is NL. Use function setName().
     * -missing value : StreetAddress is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setStreetAddress().
     * -missing value : Locality is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setLocality().
     * -missing value : ZipCode is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setZipCode().
     */
    public function testFailOnMissingCustomerForNL()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(TestUtil::createHostedOrderRow())
            ->setCountryCode("NL")
            ->setCurrency("SEK")
            ->setClientOrderNumber("55")
            ->usePaymentMethod(PaymentMethod::INVOICE)
            ->setReturnUrl("myurl.se");

        $order->getPaymentForm();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage
     * -missing value : StreetAddress is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setStreetAddress().
     * -missing value : Locality is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setLocality().
     * -missing value : ZipCode is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setZipCode().
     * -missing value : VatNumber is required for INVOICE and PAYMENTPLAN payments for company customers when countrycode is NL. Use function setVatNumber().
     * -missing value : CompanyName is required for INVOICE and PAYMENTPLAN payments for individual customers when countrycode is NL. Use function setCompanyName().
     */
    public function testFailOnMissingCompanyCustomerForNL()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(TestUtil::createHostedOrderRow())
            ->setCountryCode("NL")
            ->setCurrency("SEK")
            ->setClientOrderNumber("55")
            ->usePaymentMethod(PaymentMethod::INVOICE)
            ->setReturnUrl("myurl.se");

        $order->getPaymentForm();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : Currency is required. Use function setCurrency().
     */
    public function testFailOnMissingCurrency()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(TestUtil::createHostedOrderRow())
            ->setCountryCode("SE")
            ->setClientOrderNumber("34")
            ->usePayPageCardOnly()
            ->setReturnUrl("myurl.se");

        $order->getPaymentForm();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : ReturnUrl is required. Use function setReturnUrl().
     */
    public function testFailOnMissingReturnUrl()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(TestUtil::createHostedOrderRow())
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setClientOrderNumber("34")
            ->usePayPage();
        // ->setReturnUrl("myurl.se")

        $order->getPaymentForm();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -unsupported currency : Currency is not supported with this payment method.
     */
    public function testUnsupportedCardPayCurrency()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(TestUtil::createHostedOrderRow())
            ->setCurrency("XXX")
            ->setClientOrderNumber("34")
            ->usePaymentMethod("SVEACARDPAY");

        $order->getPaymentForm();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : CountryCode is required for SVEACARDPAY_PF. Use function setCountryCode().
     */
    public function testNullCountryCodeWithCardPayPF()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(TestUtil::createHostedOrderRow())
            ->setCurrency("SEK")
            ->setClientOrderNumber("34")
            ->usePaymentMethod("SVEACARDPAY_PF");

        $order->getPaymentForm();
    }
}
