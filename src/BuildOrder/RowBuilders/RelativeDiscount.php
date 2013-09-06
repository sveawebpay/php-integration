<?php
namespace Svea;

/**
 * @author anne-hal
 */
class RelativeDiscount {
    
    /**
     * Optional
     * @param type $idAsString
     * @return \RelativeDiscount
     */
    public function setDiscountId($idAsString) {
        $this->discountId = $idAsString;
        return $this;
    }

    /**
     * Required
     * The percentage of the discount
     * @param type $discountPercentOnTotalAmountInInt
     * @return \RelativeDiscount
     */
    public function setDiscountPercent($discountPercentOnTotalAmountInInt) {
        $this->discountPercent = $discountPercentOnTotalAmountInInt;
        return $this;
    }

    /**
     * Optional
     * @param type $unitDescriptionAsString
     * @return \RelativeDiscount
     */
    public function setUnit($unitDescriptionAsString) {
        $this->unit = $unitDescriptionAsString;
        return $this;
    }

    /**
     * Optional
     * @param type $nameAsString
     * @return \RelativeDiscount
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }

    /**
     * Optional
     * @param type $descriptionAsString
     * @return \RelativeDiscount
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }
}
