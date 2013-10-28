<?php
namespace Svea;

/**
 * @author anne-hal
 */
class ShippingFee {
    
    /**
     * in constructor, we set quantity to 1, as this attribute is used by 
     * WebServiceRowFormatter() and all shipping rows are for one (1) unit
     */
    function __construct() {
        $this->quantity = 1;
    }
    
    /**
     * Optional
     * @param string $idAsString
     * @return \ShippingFee
     */
    public function setShippingId($idAsString) {
        $this->shippingId = $idAsString;
        return $this;
    }

    /**
     * Optional
     * @param string $nameAsString
     * @return \ShippingFee
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }

    /**
     * Optional
     * @param string $descriptionAsString
     * @return \ShippingFee
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }

    /**
     * Optional
     * @param float $amountAsFloat
     * @return \ShippingFee
     */
    public function setAmountExVat($amountAsFloat) {
        $this->amountExVat = $amountAsFloat;
        return $this;
    }
    
    /**
     * Optional
     * @param float $amountAsFloat
     * @return \ShippingFee
     */
    public function setAmountIncVat($amountAsFloat) {
        $this->amountIncVat = $amountAsFloat;
        return $this;
    }
    
    /**
    *
    * @param string $unitDescriptionAsString
    * @return \ShippingFee
    */
    public function setUnit($unitDescriptionAsString) {
        $this->unit = $unitDescriptionAsString;
        return $this;
    }
    
    /**
     *
     * @param int $percentAsInt
     * @return \ShippingFee
     */
    public function setVatPercent($percentAsInt) {
        $this->vatPercent = $percentAsInt;
        return $this;
    }
    
    /**
     *
     * @param int $discountPercentAsInt
     * @return \ShippingFee
     */
    public function setDiscountPercent($discountPercentAsInt) {
        $this->discountPercent = $discountPercentAsInt;
        return $this;
    }
}
