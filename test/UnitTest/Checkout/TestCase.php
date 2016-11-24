<?php

namespace Svea\WebPay\Test\UnitTest\Checkout;

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use PHPUnit_Framework_TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Checkout\Model\PresetValue;
use Svea\WebPay\Checkout\Helper\CheckoutOrderBuilder;

/**
 * Class TestCase
 * @package Svea\Svea\WebPay\WebPay\Test\UnitTest\Checkout
 */
class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var CheckoutOrderBuilder
     */
    protected $order;

    public function setUp()
    {
        $this->order = $this->returnCreatedOrder();
    }

    /**
     * @param $object
     * @param $methodName
     * @param array $parameters
     * @return mixed
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * @param $object
     * @param $propertyName
     * @return \ReflectionProperty
     */
    public function getPrivateProperty(&$object, $propertyName)
    {
        $reflector = new \ReflectionClass(get_class($object));
        $property = $reflector->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }

    /**
     * Creates a populated order object for use in tests
     *
     * @return CheckoutOrderBuilder
     */
    protected function returnCreatedOrder()
    {
        $config = ConfigurationService::getTestConfig();

        $orderObject = WebPay::Checkout($config);

        return $this->getPrivateProperty($orderObject, 'checkoutOrderBuilder');
    }

    /**
     * @return \Svea\WebPay\BuildOrder\RowBuilders\OrderRow
     */
    protected function returnOrderRow()
    {
        // create and add items to order
        $orderRow = WebPayItem::orderRow()
            ->setAmountIncVat(rand(1, 1000) + rand(1, 99) / 100)// - required
            ->setVatPercent($this->getRandVatPercent())// - required
            ->setQuantity($this->getRandQuantity())
            ->setDiscountPercent($this->getRandDiscountPercent())
            ->setArticleNumber("123456")
            ->setName('Fork');

        return $orderRow;
    }

    /**
     * @return PresetValue
     */
    protected function returnPresetValue()
    {
        $presetValue = new PresetValue();
        $presetValue->setTypeName(PresetValue::EMAIL_ADDRESS)
            ->setValue("email@mail.com")
            ->setIsReadonly(true);

        return $presetValue;
    }

    /**
     * Return random Vat Percent
     * @return mixed
     */
    protected function getRandVatPercent()
    {
        return array_rand(array(6, 12, 25));
    }

    /**
     * Return random Discount Percent
     * @return mixed
     */
    protected function getRandDiscountPercent()
    {
        return array_rand(array(6, 12, 20, 25));
    }

    /**
     * Return random Quantity
     * @return int
     */
    protected function getRandQuantity()
    {
        return rand(1, 100);
    }
}
