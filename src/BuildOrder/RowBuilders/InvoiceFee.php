<?php
namespace Svea;

/**
 * Use this class to add fees associated with a payment method (i.e. invoice fee) to the order.
 * 
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
