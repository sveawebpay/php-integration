<?php

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 *  If order contains a discount with fixed value.
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package BuildOrder/CreateOrder
 */

class FixedDiscountBuilder implements RowBuilder {
    
    /**
     * @param createOrderBuilder $order
     */
    public function __construct(createOrderBuilder $order) {
        $this->order = $order;
    }

    /**
     * Optional
     * @param type $IdAsString
     * @return \FixedDiscountBuilder
     */
    public function setDiscountId($IdAsString) {
        $this->discountId = $IdAsString;
        return $this;
    }
    
    /**
     * Optional
     * @param type $unitDescriptionAsString
     * @return \FixedDiscountBuilder
     */
    public function setUnit($unitDescriptionAsString) {
        $this->unit = $unitDescriptionAsString;
        return $this;
    }
    
    /**
     * Optional
     * @param type $nameAsString
     * @return \FixedDiscountBuilder
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }
    
    /**
     * Optional
     * @param type $descriptionAsString
     * @return \FixedDiscountBuilder
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }
    
    /**
     * Required
     * @param type $discountAmountOnTotalPriceAsFloat
     * @return \FixedDiscountBuilder
     */
    public function setAmountIncVat($discountAmountOnTotalPriceAsFloat) {
        $this->amount = $discountAmountOnTotalPriceAsFloat;
        return $this;
    }

    /**
     * Code completion comment
     * @return createOrderBuilder createOrder
     */
    public function endFixedDiscount() {
        return $this->order;
    }
}

?>
