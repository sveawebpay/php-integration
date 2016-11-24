<?php

namespace Svea\WebPay\Checkout\Model;

/**
 * This class is used to hold data for order rows which will be
 * prepared like array for constructing an order data
 * for Checkout Conn Lib
 *
 * Class CheckoutOrderRow
 * @package Svea\Svea\WebPay\WebPay\Checkout\Model
 */
class CheckoutOrderRow
{
    /**
     * @var string $articleNumber
     */
    private $articleNumber;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var integer $quantity
     */
    private $quantity;

    /**
     * @var integer $unitPrice
     */
    private $unitPrice;

    /**
     * @var integer $discountPercent
     */
    private $discountPercent;

    /**
     * @var integer $vatPercent
     */
    private $vatPercent;

    /**
     * @var string $unit
     */
    private $unit;

    /**
     * @var $temporaryReference
     */
    private $temporaryReference;

    /**
     * @return string
     */
    public function getArticleNumber()
    {
        return $this->articleNumber;
    }

    /**
     * @param string $articleNumber
     * @return CheckoutOrderRow
     */
    public function setArticleNumber($articleNumber)
    {
        $this->articleNumber = $articleNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return CheckoutOrderRow
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param integer $quantity
     * @return CheckoutOrderRow
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity * 100;
        return $this;
    }

    /**
     * @return integer
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @param integer $unitPrice
     * @return CheckoutOrderRow
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    /**
     * @return integer
     */
    public function getDiscountPercent()
    {
        return $this->discountPercent;
    }

    /**
     * @param integer $discountPercent
     * @return CheckoutOrderRow
     */
    public function setDiscountPercent($discountPercent)
    {
        $this->discountPercent = $discountPercent;
        return $this;
    }

    /**
     * @return integer
     */
    public function getVatPercent()
    {
        return $this->vatPercent;
    }

    /**
     * @param integer $vatPercent
     * @return CheckoutOrderRow
     */
    public function setVatPercent($vatPercent)
    {
        $this->vatPercent = $vatPercent;
        return $this;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     * @return CheckoutOrderRow
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemporaryReference()
    {
        return $this->temporaryReference;
    }

    /**
     * @param string $temporaryReference
     */
    public function setTemporaryReference($temporaryReference)
    {
        $this->temporaryReference = $temporaryReference;
    }

    /**
     * Return formatted array
     * Convert this object to array, only filled fields
     * @return array
     */
    public function toArray()
    {
        $result = array();
        $properties = get_object_vars($this);

        foreach ($properties as $property => $value) {
            if (isset($this->$property)) {
                $result[$property] = $value;
            }
        }

        return $result;
    }
}
