<?php

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Description of CreateInvoiceFeeBuilder
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class InvoiceFeeBuilder {
  
    /**
     * @param createOrderBuilder $order
     */
    public function __construct(createOrderBuilder $order) {
        $this->order = $order;
    }
    
    /**
     * Optional
     * @param type $nameAsString
     * @return \InvoiceFeeBuilder
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }
    
    /**
     * Optional
     * @param type $descriptionAsString
     * @return \InvoiceFeeBuilder
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }
    
    /**
     * Optional
     * Required to use at least two of the functions setAmountExVat(), setAmountIncVat(), setVatPercent()
     * @param type $amountAsFloat
     * @return \InvoiceFeeBuilder
     */
    public function setAmountExVat($amountAsFloat) {
        $this->amountExVat = $amountAsFloat;
        return $this;
    }
    /**
     * Optional
     * Required to use at least two of the functions setAmountExVat(), setAmountIncVat(), setVatPercent()
     * @param type $amountAsFloat
     * @return \InvoiceFeeBuilder
     */
    public function setAmountIncVat($amountAsFloat) {
        $this->amountIncVat = $amountAsFloat;
        return $this;
    }
    
    /**
     * Optional
     * @param type $unitDescriptionAsString
     * @return \InvoiceFeeBuilder
     */
    public function setUnit($unitDescriptionAsString) {
        $this->unit = $unitDescriptionAsString;
        return $this;
    }
    
    /**
     * Optional
     * Required to use at least two of the functions setAmountExVat(), setAmountIncVat(), setVatPercent()
     * @param type $vatPercentAsInt
     * @return \InvoiceFeeBuilder
     */
    public function setVatPercent($vatPercentAsInt) {
        $this->vatPercent = $vatPercentAsInt;
        return $this;
    }
    
    /**
     * Optional
     * @param type $discountPercentAsInt
     * @return \InvoiceFeeBuilder
     */
    public function setDiscountPercent($discountPercentAsInt) {
        $this->discountPercent = $discountPercentAsInt;
        return $this;
    }

    /**
     * Code completion comment. 
     * @return createOrderBuilder orderBuilder
     */
    public function endInvoiceFee() {
        return $this->order;
    }
}

?>
