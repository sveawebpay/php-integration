<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../TestUtil.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class HostedOrderValidatorTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage -missing value : ClientOrderNumber is required. Use function setClientOrderNumber().
     */
    public function testFailOnNullCustomerRefNo() {
        $config = SveaConfig::getDefaultConfig();
        $builder = \WebPay::createOrder($config);
        $order = $builder
                ->addOrderRow(\TestUtil::createHostedOrderRow())
                ->setCountryCode("SE")
                ->setCurrency("SEK")
                ->usePayPageCardOnly()
                ->setReturnUrl("myurl.se");

        $order->getPaymentForm();
    }

    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage -missing value : ClientOrderNumber is required. Use function setClientOrderNumber().
     */
    public function testFailOnEmptyCustomerRefNo() {
        $config = SveaConfig::getDefaultConfig();
        $builder = \WebPay::createOrder($config);
        $order = $builder
                ->addOrderRow(\TestUtil::createHostedOrderRow())
                ->setCountryCode("SE")
                ->setCurrency("SEK")
                ->setClientOrderNumber("")
                ->usePayPageCardOnly()
                ->setReturnUrl("myurl.se");

        $order->getPaymentForm();
    }

    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage
     * -missing value : Initials is required for INVOICE and PAYMENTPLAN payments for individual customers when countrycode is NL. Use function setInitials().
     * -missing value : BirthDate is required for INVOICE and PAYMENTPLAN payments for individual customers when countrycode is NL. Use function setBirthDate().
     * -missing value : Name is required for INVOICE and PAYMENTPLAN payments for individual customers when countrycode is NL. Use function setName().
     * -missing value : StreetAddress is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setStreetAddress().
     * -missing value : Locality is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setLocality().
     * -missing value : ZipCode is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setZipCode().
     */
    public function testFailOnMissingCustomerForNL() {
        $config = SveaConfig::getDefaultConfig();
        $builder = \WebPay::createOrder($config);
        $order = $builder
                ->addOrderRow(\TestUtil::createHostedOrderRow())
                ->setCountryCode("NL")
                ->setCurrency("SEK")
                ->setClientOrderNumber("55")
                ->usePaymentMethod(\PaymentMethod::INVOICE)
                ->setReturnUrl("myurl.se");

        $order->getPaymentForm();
    }

    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage
     * -missing value : StreetAddress is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setStreetAddress().
     * -missing value : Locality is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setLocality().
     * -missing value : ZipCode is required for INVOICE and PAYMENTPLAN payments for all customers when countrycode is NL. Use function setZipCode().
     * -missing value : VatNumber is required for INVOICE and PAYMENTPLAN payments for company customers when countrycode is NL. Use function setVatNumber().
     * -missing value : CompanyName is required for INVOICE and PAYMENTPLAN payments for individual customers when countrycode is NL. Use function setCompanyName().
     */
    public function testFailOnMissingCompanyCustomerForNL() {
        $config = SveaConfig::getDefaultConfig();
        $builder = \WebPay::createOrder($config);
        $order = $builder
                ->addOrderRow(\TestUtil::createHostedOrderRow())
                ->setCountryCode("NL")
                ->setCurrency("SEK")
                ->setClientOrderNumber("55")
                ->usePaymentMethod(\PaymentMethod::INVOICE)
                ->setReturnUrl("myurl.se");

        $order->getPaymentForm();
    }

    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage -missing value : Currency is required. Use function setCurrency().
     */
    public function testFailOnMissingCurrency() {
        $config = SveaConfig::getDefaultConfig();
        $builder = \WebPay::createOrder($config);
        $order = $builder
                ->addOrderRow(\TestUtil::createHostedOrderRow())
                ->setCountryCode("SE")
                ->setClientOrderNumber("34")
                ->usePayPageCardOnly()
                ->setReturnUrl("myurl.se");

        $order->getPaymentForm();
    }

    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage -missing value : CountryCode is required. Use function setCountryCode().
     */
    public function testFailOnMissingCountryCode() {
        $config = SveaConfig::getDefaultConfig();
        $builder = \WebPay::createOrder($config);
        $order = $builder
                ->addOrderRow(\TestUtil::createHostedOrderRow())
                //->setCountryCode("SE")
                ->setCurrency("SEK")
                ->setClientOrderNumber("34")
                ->usePayPageCardOnly()
                ->setReturnUrl("myurl.se");

        $order->getPaymentForm();
    }

    /**
     * @expectedException Svea\ValidationException
     * @expectedExceptionMessage -missing value : ReturnUrl is required. Use function setReturnUrl().
     */
    public function testFailOnMissingReturnUrl() {
        $config = SveaConfig::getDefaultConfig();
        $builder = \WebPay::createOrder($config);
        $order = $builder
                ->addOrderRow(\TestUtil::createHostedOrderRow())
                ->setCountryCode("SE")
                ->setCurrency("SEK")
                ->setClientOrderNumber("34")
                ->usePayPage();
                // ->setReturnUrl("myurl.se")

        $order->getPaymentForm();
    }
}
