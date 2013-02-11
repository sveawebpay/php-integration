<?php

require_once SVEA_REQUEST_DIR . '/Includes.php';
require_once '/../RowBuilder.php';

/**
  If order contains a discount with relative value as percentage.
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package BuildOrder/HandleOrder
 */
class DeliverRelativeDiscountBuilder implements RowBuilder {
    
    /**
     * @param deliverOrder $order
     */
    public function __construct(deliverOrder $order) {
        $this->order = $order;
    }
    
    /**
     * Optional
     * @param type $idAsString
     * @return \DeliverRelativeDiscountBuilder
     */
    public function setDiscountId($idAsString) {
        $this->discountId = $idAsString;
        return $this;
    }

    /**
     * Required
     * The percentage of the discount
     * @param type $discountPercentOnTotalAmountInInt
     * @return \DeliverRelativeDiscountBuilder
     */
    public function setDiscountPercent($discountPercentOnTotalAmountInInt) {
        $this->discountPercent = $discountPercentOnTotalAmountInInt;
        return $this;
    }
    
    /**
     * Optional
     * @param type $unitDescriptionAsString
     * @return \DeliverRelativeDiscountBuilder
     */
    public function setUnit($unitDescriptionAsString) {
        $this->unit = $unitDescriptionAsString;
        return $this;
    }

    /**
     * Optional
     * @param type $nameAsString
     * @return \DeliverRelativeDiscountBuilder
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }
    
    /**
     * Optional
     * @param type $descriptionAsString
     * @return \DeliverRelativeDiscountBuilder
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }

    /**
     * Code completion comment
     * @return deliverOrder deliverOrder
     */
    public function endRelativeDiscount() {
        return $this->order;
    }
}

?>
