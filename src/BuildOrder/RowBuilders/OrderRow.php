<?php
namespace Svea;

/**
 * @author anne-hal
 */
class OrderRow {

    /**
     * Contains empty string if not set
     * @var string
     */
    public $unit = "";

    /**
     * Contains int 0 if not set
     * @var int
     */
    public $vatDiscount = 0;

    /**
     * Optional
     * @param string $articleNumberAsString
     * @return Svea\OrderRow
     */
    public function setArticleNumber($articleNumberAsString) {
        $this->articleNumber = $articleNumberAsString;
        return $this;
    }

    /**
     * Required. Quantity expressed as numeric value. 
     * 
     * The integration package supports fractions input at any precision, but 
     * when sending the request Svea numbers are rounded to two decimal places.
     * 
     * @param numeric $quantityAsFloat
     * @return Svea\OrderRow
     */
    public function setQuantity($quantityAsNumeric) {
        $this->quantity = $quantityAsNumeric;
        return $this;
    }

    /**
     * Optional
     * 
     * The unit name, i.e. "pieces", "pcs.", "st.", "mb" et al. 
     * 
     * @param string $unitAsString
     * @return Svea\OrderRow
     */
    public function setUnit($unitAsString) {
        $this->unit = $unitAsString;
        return $this;
    }

    /**
     * Precisely two of these values must be set in the WebPayItem object:  AmountExVat, AmountIncVat or VatPercent for Orderrow. 
     * Use functions setAmountExVat(), setAmountIncVat() or setVatPercent().
     * 
     * Order row item price excluding taxes, expressed as a float value. 
     *  
     * The integration package supports fractions input at any precision, but 
     * when sending the request Svea numbers are rounded to two decimal places.
     * 
     * @param float $AmountAsFloat
     * @return Svea\OrderRow
     */
    public function setAmountExVat($AmountAsFloat) {
        $this->amountExVat = $AmountAsFloat;
        return $this;
    }
    
    /**
     * Precisely two of these values must be set in the WebPayItem object:  AmountExVat, AmountIncVat or VatPercent for Orderrow. 
     * Use functions setAmountExVat(), setAmountIncVat() or setVatPercent().
     * 
     * Order row item price including tax, expressed as a float value. 
     *  
     * The integration package supports fractions input at any precision, but 
     * when sending the request Svea numbers are rounded to two decimal places.
     * 
     * Note:
     * If you specify AmountIncVat, note that this may introduce a cumulative rounding error when ordering large
     * quantities of an item, as the package bases the total order sum on a calculated price ex. vat.
     * 
     * We recommend specifying AmountExVat and VatPercentage.
     * 
     * Also, Svea uses bankers rounding (half-to-even) when calculating the order total, so at times a rounding error of at most
     * one cent/öre may show up if the implementation/shop does not use the same rounding method.
     * 
     * See HostedPaymentTest for examples, including sums and calculations.
     * 
     * @param float $AmountAsFloat
     * @return Svea\OrderRow
     */
    public function setAmountIncVat($AmountAsFloat) {
        $this->amountIncVat = $AmountAsFloat;
        return $this;
    }

    /**
     * Precisely two of these values must be set in the WebPayItem object:  AmountExVat, AmountIncVat or VatPercent for Orderrow. 
     * Use functions setAmountExVat(), setAmountIncVat() or setVatPercent().
     * 
     * @param int $vatPercentAsInt
     * @return Svea\OrderRow
     */
    public function setVatPercent($vatPercentAsInt) {
        $this->vatPercent = $vatPercentAsInt;
        return $this;
    }

    
    /**
     * Optional
     * @param string $nameAsString
     * @return Svea\OrderRow
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }

    /**
     * Optional
     * @param string $descriptionAsString
     * @return Svea\OrderRow
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }

    /**
     * Optional
     * @param int $discountPercentAsInteger
     * @return Svea\OrderRow
     */
    public function setDiscountPercent($discountPercentAsInteger) {
        $this->discountPercent = $discountPercentAsInteger;
        return $this;
    }
}
