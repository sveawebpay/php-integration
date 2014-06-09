<?php
namespace Svea\HostedService;

class HostedOrderRowBuilder {

    /**
     * @param string $skuAsString
     * @return \HostedOrderRowBuilder
     */
    public function setSku($skuAsString) {
        $this->sku = $skuAsString;
        return $this;
    }

    /**
     * @param string $nameAsString
     * @return \HostedOrderRowBuilder
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }

    /**
     * @param string $descriptionAsString
     * @return \OrderRowBuilder
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }

    /**
     * @param int $AmountAsInt
     * @return \HostedOrderRowBuilder
     */
    public function setAmount($AmountAsInt) {
        $this->amount = $AmountAsInt;
        return $this;
    }

    /**
     * @param int $vatAsInt
     * @return \OrderRowBuilder
     */
    public function setVat($vatAsInt) {
        $this->vat = $vatAsInt;
        return $this;
    }

    /**
     * @param int $quantityAsInt
     * @return \HostedOrderRowBuilder
     */
    public function setQuantity($quantityAsInt) {
        $this->quantity = $quantityAsInt;
        return $this;
    }

    /**
     * @param string $unitAsString
     * @return \HostedOrderRowBuilder
     */
    public function setUnit($unitAsString) {
        $this->unit = $unitAsString;
        return $this;
    }
}
