<?php
namespace Svea;

/**
 * Use this class to add fees associated with a payment method (i.e. invoice fee) to the order.
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
    addFee(
        WebPayItem::invoiceFee()
            ->setName('Svea fee')                   // optional
            ->setDescription("Fee for invoice")     // optional
            ->setAmountExVat(50)                    // recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
            ->setAmountIncVat(62.50)                // optional, need to use two out of three of the price specification methods
            ->setUnit("st")                         // optional
            ->setDiscountPercent(0)                 // optional
    )
;
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
    /** @var float $quantity  quantity is always 1 */
    public $quantity;
       
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
     * Optional
     * Required to use at least two of the functions setAmountExVat(), setAmountIncVat(), setVatPercent()
     * @param float $amountAsFloat
     * @return $this
     */
    public function setAmountExVat($amountAsFloat) {
        $this->amountExVat = $amountAsFloat;
        return $this;
    }
    /** @var float $amountExVat */
    public $amountExVat;
    
    /**
     * Optional
     * Required to use at least two of the functions setAmountExVat(), setAmountIncVat(), setVatPercent()
     * @param float $amountAsFloat
     * @return $this
     */
    public function setAmountIncVat($amountAsFloat) {
        $this->amountIncVat = $amountAsFloat;
        return $this;
    }
    /** @var float $amountIncVat */
    public $amountIncVat;
    
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
     * Required to use at least two of the functions setAmountExVat(), setAmountIncVat(), setVatPercent()
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
     * Optional
     * @param int $discountPercentAsInt
     * @return $this
     */
    public function setDiscountPercent($discountPercentAsInt) {
        $this->discountPercent = $discountPercentAsInt;
        return $this;
    }
    /** @var int $discountPercent */
    public $discountPercent;  

}
