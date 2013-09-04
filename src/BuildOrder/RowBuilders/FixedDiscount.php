<?php
namespace Svea;
/**
 * @author anne-hal
 */
class FixedDiscount {
    
    /**
     * Optional
     * @param string $IdAsString
     * @return \FixedDiscount
     */
    public function setDiscountId($IdAsString) {
        $this->discountId = $IdAsString;
        return $this;
    }

    /**
     * Optional
     * @param string $unitDescriptionAsString
     * @return \FixedDiscount
     */
    public function setUnit($unitDescriptionAsString) {
        $this->unit = $unitDescriptionAsString;
        return $this;
    }

    /**
     * Optional
     * @param string $nameAsString
     * @return \FixedDiscount
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }

    /**
     * Optional
     * @param string $descriptionAsString
     * @return \FixedDiscount
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }

    /**
     * Required
     * @param float $discountAmountOnTotalPriceAsFloat
     * @return \FixedDiscount
     */
    public function setAmountIncVat($discountAmountOnTotalPriceAsFloat) {
        $this->amount = $discountAmountOnTotalPriceAsFloat;
        return $this;
    }
}
