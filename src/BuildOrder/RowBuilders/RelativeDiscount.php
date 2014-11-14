<?php
namespace Svea;

/**
 * Use RelativeDiscount() when the discount or coupon is expressed as a percentage of the total product amount.
 */
class RelativeDiscount {
    
    /**
     * Optional
     * @param string $idAsString
     * @return $this
     */
    public function setDiscountId($idAsString) {
        $this->discountId = $idAsString;
        return $this;
    }
    /**@var string */
    public $discountId;
    
    /**
     * Required
     * The percentage of the discount, either a float or a real number
     * 
     * @param number $discountPercentOnTotalAmountAsNumber
     * @return $this
     */
    public function setDiscountPercent($discountPercentOnTotalAmountAsNumber) {
        $this->discountPercent = $discountPercentOnTotalAmountAsNumber;
        return $this;
    }
    /**@var float */
    public $discountPercent;
    
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
}
