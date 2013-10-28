<?php
namespace Svea;

/**
 * @author anne-hal, Kristian Grossman-Madsen
 */
class InvoiceFee {
    
    /**
     * in constructor, we set quantity to 1, as this attribute is used by 
     * WebServiceRowFormatter() and all shipping rows are for one (1) unit
     */
    function __construct() {
        $this->quantity = 1;
    }
    
    /**
     * Optional
     * @param string $nameAsString
     * @return \InvoiceFee
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }

    /**
     * Optional
     * @param string $descriptionAsString
     * @return \InvoiceFee
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }

    /**
     * Optional
     * Required to use at least two of the functions setAmountExVat(), setAmountIncVat(), setVatPercent()
     * @param float $amountAsFloat
     * @return \InvoiceFee
     */
    public function setAmountExVat($amountAsFloat) {
        $this->amountExVat = $amountAsFloat;
        return $this;
    }
    
    /**
     * Optional
     * Required to use at least two of the functions setAmountExVat(), setAmountIncVat(), setVatPercent()
     * @param float $amountAsFloat
     * @return \InvoiceFee
     */
    public function setAmountIncVat($amountAsFloat) {
        $this->amountIncVat = $amountAsFloat;
        return $this;
    }

    /**
     * Optional
     * @param string $unitDescriptionAsString
     * @return \InvoiceFee
     */
    public function setUnit($unitDescriptionAsString) {
        $this->unit = $unitDescriptionAsString;
        return $this;
    }

    /**
     * Optional
     * Required to use at least two of the functions setAmountExVat(), setAmountIncVat(), setVatPercent()
     * @param int $vatPercentAsInt
     * @return \InvoiceFee
     */
    public function setVatPercent($vatPercentAsInt) {
        $this->vatPercent = $vatPercentAsInt;
        return $this;
    }

    /**
     * Optional
     * @param int $discountPercentAsInt
     * @return \InvoiceFee
     */
    public function setDiscountPercent($discountPercentAsInt) {
        $this->discountPercent = $discountPercentAsInt;
        return $this;
    }
}
