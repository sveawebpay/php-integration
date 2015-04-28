<?php
namespace Svea;
/**
 * Use WebPayItem::fixedDiscount() when the discount or coupon is expressed as a fixed discount amount.
 *  
 * @author anne-hal, Kristian Grossman-Madsen
 */
class FixedDiscount {
    
    /**
     * Optional
     * @param string $IdAsString
     * @return $this
     */
    public function setDiscountId($IdAsString) {
        $this->discountId = $IdAsString;
        return $this;
    }
    /**@var string */
    public $discountId;
    
    /**
     * Optional
     * @param string $unitDescriptionAsString
     * @return $this
     */
    public function setUnit($unitDescriptionAsString) {
        $this->unit = $unitDescriptionAsString;
        return $this;
    }
    /**@var string */
    public $unit;
    
    /**
     * Optional
     * @param string $nameAsString
     * @return $this
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }
    /** @var string $name */
    public $name;
    
    /**
     * Optional
     * @param string $descriptionAsString
     * @return $this
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }
    /** @var string $description */
    public $description;
    
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
     * @return $this
     */
    public function setAmountIncVat($amountIncVatAsFloat) {
        $this->amount = $amountIncVatAsFloat;           // should set amountIncVat!
        $this->amountIncVat = $amountIncVatAsFloat;     // fix for above
        return $this;
    }
    /** @var float $amountIncVat */
    public $amountIncVat;
    
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
     * @return $this
     */
    public function setAmountExVat($amountExVatAsFloat) {
        $this->amountExVat = $amountExVatAsFloat;
        return $this;
    }
    /** @var float $amountExVat */
    public $amountExVat;
    
    /**
     * Optional. If vatPercent is specified along with either one of setAmountExVat() or setAmountIncVat(), 
     * we respect the specified rate and amount and enter the discount using the specified tax rate.
     *
     * @param int $vatPercentAsInt
     * @return $this
     */
    public function setVatPercent($vatPercentAsInt) {
        $this->vatPercent = $vatPercentAsInt;
        return $this;
    }
    /** @var int $vatPercent */
    public $vatPercent;
}
