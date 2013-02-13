<?php

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * If shippingfee is used
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package BuildOrder/Row
 */
class ShippingFeeBuilder {
 
    /**
     * @param createOrderBuilder $order
     */
    public function __construct(createOrderBuilder $order) {
        $this->order = $order;
    }
    
    /**
     * Optional
     * @param type $idAsString
     * @return \ShippingFeeBuilder
     */
    public function setShippingId($idAsString) {
        $this->shippingId = $idAsString;
        return $this;
    }
    
    /**
     * Optional
     * @param type $nameAsString
     * @return \ShippingFeeBuilder
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }
    
    /**
     * Optional
     * @param type $descriptionAsString
     * @return \ShippingFeeBuilder
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }
    
    /**
     * Optional
     * @param type $amountAsFloat
     * @return \ShippingFeeBuilder
     */
    public function setAmountExVat($amountAsFloat) {
        $this->amountExVat = $amountAsFloat;
        return $this;
    }
    /**
     * Optional
     * @param type $amountAsFloat
     * @return \ShippingFeeBuilder
     */
    public function setAmountIncVat($amountAsFloat) {
        $this->amountIncVat = $amountAsFloat;
        return $this;
    }
  
    public function setUnit($unitDescriptionAsString) {
        $this->unit = $unitDescriptionAsString;
        return $this;
    }

    public function setVatPercent($percentAsInt) {
        $this->vatPercent = $percentAsInt;
        return $this;
    }

    public function setDiscountPercent($discountPercentAsInt) {
        $this->discountPercent = $discountPercentAsInt;
        return $this;
    }

    /**
     * Code completion comment
     * @return createOrderBuilder orderBuilder
     */
    public function endShippingFee() {
        return $this->order;
    }
}

?>
