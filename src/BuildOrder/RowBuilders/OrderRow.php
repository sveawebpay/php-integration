<?php
namespace Svea;

/**
* Use the OrderRow class for all kinds of products and other items. It is required to have a minimum of one order row.
* 
* @author anne-hal, Kristian Grossman-Madsen
*/
class OrderRow {

    /**
     * Optional
     * @param string $articleNumberAsString
     * @return $this
     */
    public function setArticleNumber($articleNumberAsString) {
        $this->articleNumber = $articleNumberAsString;
        return $this;
    }
    /** @var string $articlenumber  Optional. */
    public $articleNumber;
    
    /**
     * Required -- item quantity, i.e. how many were ordered of this item
     * 
     * The integration package supports fractions input at any precision, but 
     * when sending the request to Svea numbers are rounded to two decimal places.
     * 
     * @param string $quantityAsFloat
     * @return $this
     */
    public function setQuantity($quantityAsNumeric) {
        $this->quantity = $quantityAsNumeric;
        return $this;
    }
    /** @var float $quantity  */
    public $quantity;
    
    /**
     * Optional - the name of the unit used for the order quantity, i.e. "pieces", "pcs.", "st.", "mb" et al. 
     * 
     * @param string $unitAsString
     * @return $this
     */
    public function setUnit($unitAsString) {
        $this->unit = $unitAsString;
        return $this;
    }
    /**@var string $unit */
    public $unit;

    /**
     * Recommended - precisely two of these values must be set in the WebPayItem object:  AmountExVat, AmountIncVat or VatPercent for Orderrow. 
     * Use functions setAmountExVat(), setAmountIncVat() or setVatPercent(). The recommended is to use setAmountExVat() and setVatPercent().
     * 
     * Order row item price excluding taxes, expressed as a float value. 
     *  
     * @param float $AmountAsFloat
     * @return $this
     */
    public function setAmountExVat($amountAsFloat) {
        $this->amountExVat = $amountAsFloat;
        return $this;
    }
    /** @var float $amountExVat */
    public $amountExVat;
    
    /**
     * Recommended - precisely two of these values must be set in the WebPayItem object:  AmountExVat, AmountIncVat or VatPercent for Orderrow. 
     * Use functions setAmountExVat(), setAmountIncVat() or setVatPercent(). The recommended is to use setAmountExVat() and setVatPercent().
     * 
     * Order row item price vat rate in percent, expressed as an integer. 
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
    
    /**
     * Optional - precisely two of these values must be set in the WebPayItem object:  AmountExVat, AmountIncVat or VatPercent for Orderrow. 
     * Use functions setAmountExVat(), setAmountIncVat() or setVatPercent(). The recommended is to use setAmountExVat() and setVatPercent().
     * 
     * Order row item price including tax, expressed as a float value. 
     * 
     * We recommend specifying AmountExVat and VatPercentage. If not, make sure not retain as much precision as possible, i.e. use no 
     * premature rounding (87.4875 is a "better" PriceIncVat than 87.49) as the package processes the price before sending the request to Svea. 
     * 
     * If you specify AmountIncVat, note that this may introduce a cumulative rounding error when ordering large
     * quantities of an item, as the package bases the total order sum on a calculated price ex. vat. 
     * 
     * Also, Svea uses bankers rounding (half-to-even) when calculating the order total, so at times a rounding error of at most
     * one cent/öre may show up if the implementation/shop does not use the same rounding method.
     * 
     * See HostedPaymentTest for examples, including sums and calculations.
     * 
     * @param float $AmountAsFloat
     * @return $this
     */
    public function setAmountIncVat($amountAsFloat) {
        $this->amountIncVat = $amountAsFloat;
        return $this;
    }    
    /** @var float $amountIncVat */
    public $amountIncVat;
    
    /**
     * Optional - short item name
     *
     * Note that this will be merged with the item description when the request is sent to Svea
     *
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
     * Optional - long item description
     * 
     * Note that this will be merged with the item name when the request is sent to Svea
     * 
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
     * Optional - discount in percent, applies to this order row only
     * 
     * @param int $discountPercentAsInteger
     * @return $this
     */
    public function setDiscountPercent($discountPercentAsInteger) {
        $this->discountPercent = $discountPercentAsInteger;
        return $this;
    }
    /** @var int $discountPercent */
    public $discountPercent;    

    /** @var int $vatDiscount -- defaults to zero (0) */
    public $vatDiscount = 0;    
}
