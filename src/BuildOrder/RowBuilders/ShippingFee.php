<?php

/**
 * Description of ShippingFee
 *
 * @author anne-hal
 */
class ShippingFee {
  /**
     * Optional
     * @param type $idAsString
     * @return \ShippingFee
     */
    public function setShippingId($idAsString) {
        $this->shippingId = $idAsString;
        return $this;
    }

    /**
     * Optional
     * @param type $nameAsString
     * @return \ShippingFee
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }

    /**
     * Optional
     * @param type $descriptionAsString
     * @return \ShippingFee
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }

    /**
     * Optional
     * @param type $amountAsFloat
     * @return \ShippingFee
     */
    public function setAmountExVat($amountAsFloat) {
        $this->amountExVat = $amountAsFloat;
        return $this;
    }
    /**
     * Optional
     * @param type $amountAsFloat
     * @return \ShippingFee
     */
    public function setAmountIncVat($amountAsFloat) {
        $this->amountIncVat = $amountAsFloat;
        return $this;
    }
  /**
   *
   * @param type $unitDescriptionAsString
   * @return \ShippingFee
   */
    public function setUnit($unitDescriptionAsString) {
        $this->unit = $unitDescriptionAsString;
        return $this;
    }
/**
 *
 * @param type $percentAsInt
 * @return \ShippingFee
 */
    public function setVatPercent($percentAsInt) {
        $this->vatPercent = $percentAsInt;
        return $this;
    }
/**
 *
 * @param type $discountPercentAsInt
 * @return \ShippingFee
 */
    public function setDiscountPercent($discountPercentAsInt) {
        $this->discountPercent = $discountPercentAsInt;
        return $this;
    }
}