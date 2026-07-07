<?php

namespace Svea\WebPay\HostedService\Payment;

class HostedOrderRowBuilder
{
    public $sku;
    public $name;
    public $description;
    public $amount;
    public $vat;
    public $quantity;
    public $unit;

    /**
     * @param string $skuAsString
     * @return $this
     */
    public function setSku($skuAsString)
    {
        $this->sku = $skuAsString;

        return $this;
    }

    /**
     * @param string $nameAsString
     * @return $this
     */
    public function setName($nameAsString)
    {
        $this->name = $nameAsString;

        return $this;
    }

    /**
     * @param string $descriptionAsString
     * @return $this
     */
    public function setDescription($descriptionAsString)
    {
        $this->description = $descriptionAsString;

        return $this;
    }

    /**
     * @param int $AmountAsInt
     * @return $this
     */
    public function setAmount($AmountAsInt)
    {
        $this->amount = $AmountAsInt;

        return $this;
    }

    /**
     * @param int $vatAsInt
     * @return $this
     */
    public function setVat($vatAsInt)
    {
        $this->vat = $vatAsInt;

        return $this;
    }

    /**
     * @param int $quantityAsInt
     * @return $this
     */
    public function setQuantity($quantityAsInt)
    {
        $this->quantity = $quantityAsInt;

        return $this;
    }

    /**
     * @param string $unitAsString
     * @return $this
     */
    public function setUnit($unitAsString)
    {
        $this->unit = $unitAsString;

        return $this;
    }
}
