<?php

namespace Svea\WebPay\Test\UnitTest\Checkout\Validation;

use Svea\WebPay\Checkout\Helper\CheckoutOrderBuilder;
use Svea\WebPay\Checkout\Validation\GetAvailablePartPaymentCampaignsValidator;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Test\UnitTest\Checkout\TestCase;

/**
 * Class GetAvailablePartPaymentCampaignsValidationTest
 * @package Svea\Svea\WebPay\WebPay\Test\UnitTest\Checkout\Validation
 */
class GetAvailablePartPaymentCampaignsValidationTest extends TestCase
{
    /**
     * @test
     */
    public function validatePresetValueIsCompanySuccess()
    {
        $presetValueIsCompany = \Svea\WebPay\WebPayItem::presetValue()
            ->setTypeName(\Svea\WebPay\Checkout\Model\PresetValue::IS_COMPANY)
            ->setValue(false)
            ->setIsReadonly(true);

        $presetRequest = new CheckoutOrderBuilder(ConfigurationService::getTestConfig());
        $presetRequest->addPresetValue($presetValueIsCompany);

        $errors = new GetAvailablePartPaymentCampaignsValidator();

        $this->assertEquals(0, count($errors->validate($presetRequest)));
    }

    /**
     * @test
     */
    public function validatePresetValueIsCompanyValueWrongType()
    {
        $presetValueIsCompany = \Svea\WebPay\WebPayItem::presetValue()
            ->setTypeName(\Svea\WebPay\Checkout\Model\PresetValue::IS_COMPANY)
            ->setValue("false")
            ->setIsReadonly(true);

        $presetRequest = new CheckoutOrderBuilder(ConfigurationService::getTestConfig());
        $presetRequest->addPresetValue($presetValueIsCompany);

        $errors = new GetAvailablePartPaymentCampaignsValidator();

        $this->assertGreaterThan(0, count($errors->validate($presetRequest)));
    }

    public function validatePresetValueIsCompanyValueNotSet()
    {
        $presetValueIsCompany = \Svea\WebPay\WebPayItem::presetValue()
            ->setTypeName(\Svea\WebPay\Checkout\Model\PresetValue::IS_COMPANY)
            ->setIsReadonly(true);

        $presetRequest = new CheckoutOrderBuilder(ConfigurationService::getTestConfig());
        $presetRequest->addPresetValue($presetValueIsCompany);

        $errors = new GetAvailablePartPaymentCampaignsValidator();

        $this->assertGreaterThan(0, count($errors->validate($presetRequest)));
    }
}
