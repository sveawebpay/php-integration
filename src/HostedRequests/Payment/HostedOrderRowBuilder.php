<?php

class HostedOrderRowBuilder {

    /**
     * @param type $skuAsString
     * @return \HostedOrderRowBuilder
     */
    public function setSku($skuAsString) {
        $this->sku = $skuAsString;
        return $this;
    }

    /**
     * @param type $nameAsString
     * @return \HostedOrderRowBuilder
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }

    /**
     * @param type $descriptionAsString
     * @return \OrderRowBuilder
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }

    /**
     * @param type $AmountAsInt
     * @return \HostedOrderRowBuilder
     */
    public function setAmount($AmountAsInt) {
        $this->amount = $AmountAsInt;
        return $this;
    }

    /**
     * @param type $vatAsInt
     * @return \OrderRowBuilder
     */
    public function setVat($vatAsInt) {
        $this->vat = $vatAsInt;
        return $this;
    }

    /**
     * @param type $quantityAsInt
     * @return \HostedOrderRowBuilder
     */
    public function setQuantity($quantityAsInt) {
        $this->quantity = $quantityAsInt;
        return $this;
    }

    /**
     * @param type $unitAsString
     * @return \HostedOrderRowBuilder
     */
    public function setUnit($unitAsString) {
        $this->unit = $unitAsString;
        return $this;
    }
}