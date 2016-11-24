<?php

namespace Svea\WebPay\Test\UnitTest\Checkout\Model;

use Svea\WebPay\Checkout\Model\PresetValue;
use Svea\WebPay\Test\UnitTest\Checkout\TestCase;

/**
 * Class CheckoutPresetValueTest
 * @package Svea\Svea\WebPay\WebPay\Test\UnitTest\Checkout\Model
 */
class CheckoutPresetValueTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @test
     *
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     */
    public function setTypeMethodReceiveBadParam()
    {
        $pv = new PresetValue();

        $pv->setTypeName('NonExistingName');
    }

    /**
     * @test
     */
    public function setTypeMethodReceiveGoodParams()
    {
        $pv = new PresetValue();
        $typeList = $this->invokeMethod($pv, 'getConstantListValues');

        foreach ($typeList as $type) {
            $pv->setTypeName($type);
        }
    }

    /**
     * @test
     */
    public function printArray()
    {
        $postalCode = '11123';
        $readonly = true;

        $pv = new PresetValue();
        $pv->setTypeName($pv::POSTAL_CODE)
            ->setValue($postalCode)
            ->setIsReadonly($readonly);

        $array = $pv->returnPresetArray();
        
        $this->assertEquals($array['typeName'], $pv::POSTAL_CODE);
        $this->assertEquals($array['value'], $postalCode);
        $this->assertEquals($array['isReadonly'], $readonly);
    }
}
