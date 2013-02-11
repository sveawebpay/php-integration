<?php

require_once SVEA_REQUEST_DIR . '/Includes.php';
require_once '/../RowBuilder.php';

/**
 *  If order contains a discount with fixed value.
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package BuildOrder/HandleOrder
 */

class DeliverFixedDiscountBuilder implements RowBuilder {
    
    /**
     * @param deliverOrder $order
     */
    public function __construct(deliverOrder $order) {
        $this->order = $order;
    }

    /**
     * Optional
     * @param type $IdAsString
     * @return \DeliverFixedDiscountBuilder
     */
    public function setDiscountId($IdAsString) {
        $this->discountId = $IdAsString;
        return $this;
    }
    
    /**
     * Optional
     * @param type $unitDescriptionAsString
     * @return \DeliverFixedDiscountBuilder
     */
    public function setUnit($unitDescriptionAsString) {
        $this->unit = $unitDescriptionAsString;
        return $this;
    }
    
    /**
     * Optional
     * @param type $nameAsString
     * @return \DeliverFixedDiscountBuilder
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }
    
    /**
     * Optional
     * @param type $descriptionAsString
     * @return \DeliverFixedDiscountBuilder
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }
    
    /**
     * Required
     * @param type $amountDiscountOnTotalPriceAsFloat
     * @return \DeliverFixedDiscountBuilder
     */
    public function setAmountIncVat($amountDiscountOnTotalPriceAsFloat) {
        $this->amount = $amountDiscountOnTotalPriceAsFloat;
        return $this;
    }

    /**
     * Code completion comment
     * @return deliverOrder deliverOrder
     */
    public function endFixedDiscount() {
        return $this->order;
    }
}

?>
