<?php


namespace Svea\WebPay\Test\UnitTest\HostedService\Payment;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\PaymentMethod;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;

class SwishPaymentTest extends \PHPUnit\Framework\TestCase
{
    public function testSwishBuildPayment() {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->setCountryCode("SE")
            ->setClientOrderNumber("33")
            ->setCurrency("SEK")
            ->setPayerAlias("46701234567")
            ->usePaymentMethod(PaymentMethod::SWISH)
            ->setReturnUrl("http://myurl.se")
            ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);

        $this->assertEquals('46701234567', $xmlMessage->payeralias);
        $this->assertEquals('SWISH', $xmlMessage->paymentmethod);
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -incorrect type : payerAlias must be numeric and can not contain any non-numeric characters
     */
    public function testSwishValidationPayerAliasContainsNonNumericCharacters()
    {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->setCountryCode("SE")
            ->setClientOrderNumber("33")
            ->setCurrency("SEK")
            ->setPayerAlias("4670123456a")
            ->usePaymentMethod(PaymentMethod::SWISH)
            ->setReturnUrl("http://myurl.se")
            ->getPaymentForm();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -incorrect length : payerAlias must be 11 digits
     */
    public function testSwishValidationPayerAliasTooShort()
    {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->setCountryCode("SE")
            ->setClientOrderNumber("33")
            ->setCurrency("SEK")
            ->setPayerAlias("4670123456")
            ->usePaymentMethod(PaymentMethod::SWISH)
            ->setReturnUrl("http://myurl.se")
            ->getPaymentForm();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -incorrect length : payerAlias must be 11 digits
     */
    public function testSwishValidationPayerAliasTooLong()
    {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->setCountryCode("SE")
            ->setClientOrderNumber("33")
            ->setCurrency("SEK")
            ->setPayerAlias("4670123456789")
            ->usePaymentMethod(PaymentMethod::SWISH)
            ->setReturnUrl("http://myurl.se")
            ->getPaymentForm();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -incorrect value : countryCode must be set to "SE" if payment method is SWISH
     */
    public function testSwishValidationWrongCountryCode()
    {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->setCountryCode("NO")
            ->setClientOrderNumber("33")
            ->setCurrency("SEK")
            ->setPayerAlias("4670123456789")
            ->usePaymentMethod(PaymentMethod::SWISH)
            ->setReturnUrl("http://myurl.se")
            ->getPaymentForm();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : payerAlias must be set if using payment method SWISH. Use function setPayerAlias()
     */
    public function testSwishValidationPayerAliasMissing()
    {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->setCountryCode("SE")
            ->setClientOrderNumber("33")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::SWISH)
            ->setReturnUrl("http://myurl.se")
            ->getPaymentForm();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -incorrect length : ClientOrderNumber cannot be longer than 35 characters for Swish payments

    public function testSwishValidationClientOrderNumberTooLong()
    {
        $config = ConfigurationService::getDefaultConfig();
        $form = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->setCountryCode("SE")
            ->setClientOrderNumber("3311111111111111111111111")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::SWISH)
            ->setReturnUrl("http://myurl.se")
            ->getPaymentForm();
    }*/
}