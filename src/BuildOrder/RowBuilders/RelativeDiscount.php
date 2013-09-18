<?php
namespace Svea;

/**
 * @author anne-hal
 */
class RelativeDiscount {
    
    /**
     * Optional
     * @param string $idAsString
     * @return \RelativeDiscount
     */
    public function setDiscountId($idAsString) {
        $this->discountId = $idAsString;
        return $this;
    }

    /**
     * Required
     * The percentage of the discount
     * @param int $discountPercentOnTotalAmountAsInt
     * @return \RelativeDiscount
     */
    public function setDiscountPercent($discountPercentOnTotalAmountAsInt) {
        $this->discountPercent = $discountPercentOnTotalAmountAsInt;
        return $this;
    }

    /**
     * Optional
     * @param string $unitDescriptionAsString
     * @return \RelativeDiscount
     */
    public function setUnit($unitDescriptionAsString) {
        $this->unit = $unitDescriptionAsString;
        return $this;
    }

    /**
     * Optional
     * @param string $nameAsString
     * @return \RelativeDiscount
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }

    /**
     * Optional
     * @param string $descriptionAsString
     * @return \RelativeDiscount
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }
}
