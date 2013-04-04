<?php

/**
 * Description of InvoiceFee
 *
 * @author anne-hal
 */
class InvoiceFee {
    /**
     * Optional
     * @param type $nameAsString
     * @return \InvoiceFee
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }

    /**
     * Optional
     * @param type $descriptionAsString
     * @return \InvoiceFee
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }

    /**
     * Optional
     * Required to use at least two of the functions setAmountExVat(), setAmountIncVat(), setVatPercent()
     * @param type $amountAsFloat
     * @return \InvoiceFee
     */
    public function setAmountExVat($amountAsFloat) {
        $this->amountExVat = $amountAsFloat;
        return $this;
    }
    /**
     * Optional
     * Required to use at least two of the functions setAmountExVat(), setAmountIncVat(), setVatPercent()
     * @param type $amountAsFloat
     * @return \InvoiceFee
     */
    public function setAmountIncVat($amountAsFloat) {
        $this->amountIncVat = $amountAsFloat;
        return $this;
    }

    /**
     * Optional
     * @param type $unitDescriptionAsString
     * @return \InvoiceFee
     */
    public function setUnit($unitDescriptionAsString) {
        $this->unit = $unitDescriptionAsString;
        return $this;
    }

    /**
     * Optional
     * Required to use at least two of the functions setAmountExVat(), setAmountIncVat(), setVatPercent()
     * @param type $vatPercentAsInt
     * @return \InvoiceFee
     */
    public function setVatPercent($vatPercentAsInt) {
        $this->vatPercent = $vatPercentAsInt;
        return $this;
    }

    /**
     * Optional
     * @param type $discountPercentAsInt
     * @return \InvoiceFee
     */
    public function setDiscountPercent($discountPercentAsInt) {
        $this->discountPercent = $discountPercentAsInt;
        return $this;
    }

}