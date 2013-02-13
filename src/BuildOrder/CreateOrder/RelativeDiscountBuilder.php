<?php

require_once SVEA_REQUEST_DIR . '/Includes.php';
require_once '/../RowBuilder.php';

/**
  If order contains a discount with relative value as percentage.
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package BuildOrder/CreateOrder
 */

class RelativeDiscountBuilder implements RowBuilder {
    
    /**
     * @param createOrderBuilder $order
     */
    public function __construct(createOrderBuilder $order) {
        $this->order = $order;
    }
    
    /**
     * Optional
     * @param type $idAsString
     * @return \RelativeDiscountBuilder
     */
    public function setDiscountId($idAsString) {
        $this->discountId = $idAsString;
        return $this;
    }

    /**
     * Required
     * The percentage of the discount
     * @param type $discountPercentOnTotalAmountInInt
     * @return \RelativeDiscountBuilder
     */
    public function setDiscountPercent($discountPercentOnTotalAmountInInt) {
        $this->discountPercent = $discountPercentOnTotalAmountInInt;
        return $this;
    }
    
    /**
     * Optional
     * @param type $unitDescriptionAsString
     * @return \RelativeDiscountBuilder
     */
    public function setUnit($unitDescriptionAsString) {
        $this->unit = $unitDescriptionAsString;
        return $this;
    }

    /**
     * Optional
     * @param type $nameAsString
     * @return \RelativeDiscountBuilder
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }
    
    /**
     * Optional
     * @param type $descriptionAsString
     * @return \RelativeDiscountBuilder
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }

    /**
     * Code completion comment
     * @return createOrderBuilder createOrder
     */
    public function endRelativeDiscount() {
        return $this->order;
    }
}

?>
