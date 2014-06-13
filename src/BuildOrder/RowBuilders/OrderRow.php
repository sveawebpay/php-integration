<?php
namespace Svea;

/**
* Use the OrderRow class for all kinds of products and other items. It is required to have a minimum of one order row.
* 
* Specify the price using precisely two of these methods in order to specify the item price and tax rate: 
* setAmountExVat(), setAmountIncVat() and setVatPercent().
* 
* We recommend specifying price using setAmountExVat() and setVatPercentage(). If not, make sure not retain as much precision as
* possible, i.e. use no premature rounding (87.4875 is a "better" PriceIncVat than 87.49).
* 
* If you use setAmountIncVat(), note that this may introduce a cumulative rounding error when ordering large
* quantities of an item, as the package bases the total order sum on a calculated price ex. vat.
*  
$order->
    addOrderRow(
        WebPayItem::orderRow()
            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
            ->setAmountIncVat(125.00)               // optional, need to use two out of three of the price specification methods
            ->setQuantity(2)                        // required
            ->setUnit("st")                         // optional
            ->setName('Prod')                       // optional
            ->setDescription("Specification")       // optional
            ->setArticleNumber("1")                 // optional
            ->setDiscountPercent(0)                 // optional
    )
;
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
     * @param numeric $quantityAsFloat
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
    /**@var string */
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
    public function setAmountExVat($AmountAsFloat) {
        $this->amountExVat = $AmountAsFloat;
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
    public function setAmountIncVat($AmountAsFloat) {
        $this->amountIncVat = $AmountAsFloat;
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
     * Optional
     * @param int $discountPercentAsInteger
     * @return $this
     */
    public function setDiscountPercent($discountPercentAsInteger) {
        $this->discountPercent = $discountPercentAsInteger;
        return $this;
    }
    /** @var int $discountPercent */
    public $discountPercent;    

    /** @var int  contains int 0 if not set. */
    public $vatDiscount = 0;    
}
