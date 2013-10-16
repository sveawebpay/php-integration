<?php
namespace Svea;
/**
 * @author anne-hal, Kristian Grossman-Madsen
 */
class FixedDiscount {
    
    /**
     * Optional
     * @param string $IdAsString
     * @return \FixedDiscount
     */
    public function setDiscountId($IdAsString) {
        $this->discountId = $IdAsString;
        return $this;
    }

    /**
     * Optional
     * @param string $unitDescriptionAsString
     * @return \FixedDiscount
     */
    public function setUnit($unitDescriptionAsString) {
        $this->unit = $unitDescriptionAsString;
        return $this;
    }

    /**
     * Optional
     * @param string $nameAsString
     * @return \FixedDiscount
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }

    /**
     * Optional
     * @param string $descriptionAsString
     * @return \FixedDiscount
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }

    /**
     * If only AmountIncVat is given, for Invoice and Payment plan payment methods we calculate the discount split across the tax (vat) rates present 
     * in the order. This will ensure that the correct discount vat is applied to the order. This means that the discount will show up split across 
     * multiple rows on the invoice, one for each tax rate present in the order.
     * 
     * For Card and Direct bank payments we only subtract the appropriate amount from the request, but we still honour the specified percentage, if
     * given using two the functions below. 
     * 
     * Otherwise, it is required to use precisely two of the functions setAmountExVat(), setAmountIncVat() and setVatPercent().
     * If two of these three attributes are specified, we respect the amount indicated and include the discount with the specified tax rate.
     *
     * @param float $amountIncVatAsFloat
     * @return \FixedDiscount
     */
    public function setAmountIncVat($amountIncVatAsFloat) {
        $this->amount = $amountIncVatAsFloat;
        return $this;
    }
    
    /**
     * Optional
     * 
     * Required to use at least two of the functions setAmountExVat(), setAmountIncVat(), setVatPercent().
     * If two of these three attributes are specified, we respect the amount indicated and include a discount with the appropriate tax rate.
     * 
     * @param float $amountAsFloat
     * @return \FixedDiscount
     */
    public function setAmountExVat($amountExVatAsFloat) {
        $this->amountExVat = $amountExVatAsFloat;
        return $this;
    }
    
    /**
     * Optional
     * Required to use at least two of the functions setAmountExVat(), setAmountIncVat(), setVatPercent()
     * @param int $vatPercentAsInt
     * @return \FixedDiscount
     */
    public function setVatPercent($vatPercentAsInt) {
        $this->vatPercent = $vatPercentAsInt;
        return $this;
    }

}
