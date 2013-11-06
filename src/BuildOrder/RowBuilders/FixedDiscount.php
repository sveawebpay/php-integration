<?php
namespace Svea;
/**
 * @author anne-hal, Kristian Grossman-Madsen
 */
class FixedDiscount {
    
    /**
     * Optional
     * @param string $IdAsString
     * @return Svea\FixedDiscount
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
     * Required to use at least one of the functions setAmountExVat() or setAmountIncVat(), and optionally also setVatPercent().
     * 
     * If only AmountIncVat is given, for Invoice and Payment plan payment methods we calculate the discount split across the tax (vat) rates present 
     * in the order. The discount will be split based upon the order total for each tax rate, the totals being calculated including taxes. 
     * 
     * This means that the discount will show up split across multiple rows on the invoice, one for each tax rate present in the order.
     * 
     * For Card and Direct bank payments we only subtract the appropriate amount from the request, but we still honour the specified percentage, if
     * given using two the functions below. 
     * 
     * Otherwise, it is required to use precisely two of the functions setAmountExVat(), setAmountIncVat() and setVatPercent().
     * If two of these three attributes are specified, we respect the amount indicated and include the discount with the specified tax rate.
     *
     * See WebServiceRowFormaterTest, test_FixedDiscount_specified_using_amountIncVat_in_order_with_multiple_vat_rates() for an example.
     * 
     * @param float $amountIncVatAsFloat
     * @return \FixedDiscount
     */
    public function setAmountIncVat($amountIncVatAsFloat) {
        $this->amount = $amountIncVatAsFloat;
        return $this;
    }
    
    /**
     * Required to use at least one of the functions setAmountExVat() or setAmountIncVat(), and optionally also setVatPercent().
     * 
     * If only AmountExVat is given, for Invoice and Payment plan payment methods we calculate the discount split across the tax (vat) rates present 
     * in the order. The discount will be split based upon the total amount for each tax rate, the totals being calculated before taxes. 
     * 
     * This means that the discount will show up split across multiple rows on the invoice, one for each tax rate present in the order.
     * 
     * For Card and Direct bank payments we only subtract the appropriate amount from the request, but we still honour the specified percentage, if
     * given using two the functions below. 
     * 
     * Otherwise, it is required to use precisely two of the functions setAmountExVat(), setAmountIncVat() and setVatPercent().
     * If two of these three attributes are specified, we respect the amount indicated and include the discount with the specified tax rate.
     *
     * See WebServiceRowFormaterTest, test_FixedDiscount_specified_using_amountExVat_in_order_with_multiple_vat_rates() for an example.
     * 
     * @param float $amountAsFloat
     * @return \FixedDiscount
     */
    public function setAmountExVat($amountExVatAsFloat) {
        $this->amountExVat = $amountExVatAsFloat;
        return $this;
    }
    
    /**
     * Optional. If vatPercent is specified along with either one of setAmountExVat() or setAmountIncVat(), 
     * we respect the specified rate and amount and enter the discount using the specified tax rate.
     *
     * @param int $vatPercentAsInt
     * @return \FixedDiscount
     */
    public function setVatPercent($vatPercentAsInt) {
        $this->vatPercent = $vatPercentAsInt;
        return $this;
    }

}
