<?php

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 *  If invoice fee is used for Invoice order
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package BuildOrder/Row
 */
class DeliverInvoiceFeeBuilder {

    /**
     * @param deliverOrder $order
     */
    public function __construct(deliverOrder $order) {
        $this->order = $order;
    }
    
    /**
     * Optional
     * @param type $nameAsString
     * @return \DeliverInvoiceFeeBuilder
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }
    
    /**
     * Optional
     * @param type $descriptionAsString
     * @return \DeliverInvoiceFeeBuilder
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }
    
    /**
     * Required
     * @param type $amountAsFloat
     * @return \DeliverInvoiceFeeBuilder
     */
    public function setAmountExVat($amountAsFloat) {
        $this->amount = $amountAsFloat;
        return $this;
    }
    /**
     * Required
     * @param type $amountAsFloat
     * @return \DeliverInvoiceFeeBuilder
     */
    public function setAmountIncVat($amountAsFloat) {
        $this->amount = $amountAsFloat;
        return $this;
    }
    
    /**
     * Optional
     * @param type $unitDescriptionAsString
     * @return \DeliverInvoiceFeeBuilder
     */
    public function setUnit($unitDescriptionAsString) {
        $this->unit = $unitDescriptionAsString;
        return $this;
    }
    
    /**
     * Required
     * @param type $vatPercentAsInt
     * @return \DeliverInvoiceFeeBuilder
     */
    public function setVatPercent($vatPercentAsInt) {
        $this->vatPercent = $vatPercentAsInt;
        return $this;
    }
    
    /**
     * Optional
     * @param type $discountPercentAsInt
     * @return \DeliverInvoiceFeeBuilder
     */
    public function setDiscountPercent($discountPercentAsInt) {
        $this->discountPercent = $discountPercentAsInt;
        return $this;
    }

    /**
     * Code completion comment. 
     * @return deliverOrder orderBuilder
     */
    public function endInvoiceFee() {
        return $this->order;
    }
}

?>
