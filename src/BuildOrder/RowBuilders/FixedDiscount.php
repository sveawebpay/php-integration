<?php
/**
 * Description of FixedDiscount
 *
 * @author anne-hal
 */
class FixedDiscount {
    /**
     * Optional
     * @param type $IdAsString
     * @return \FixedDiscount
     */
    public function setDiscountId($IdAsString) {
        $this->discountId = $IdAsString;
        return $this;
    }

    /**
     * Optional
     * @param type $unitDescriptionAsString
     * @return \FixedDiscount
     */
    public function setUnit($unitDescriptionAsString) {
        $this->unit = $unitDescriptionAsString;
        return $this;
    }

    /**
     * Optional
     * @param type $nameAsString
     * @return \FixedDiscount
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }

    /**
     * Optional
     * @param type $descriptionAsString
     * @return \FixedDiscount
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }

    /**
     * Required
     * @param type $discountAmountOnTotalPriceAsFloat
     * @return \FixedDiscount
     */
    public function setAmountIncVat($discountAmountOnTotalPriceAsFloat) {
        $this->amount = $discountAmountOnTotalPriceAsFloat;
        return $this;
    }

}