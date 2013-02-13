<?php
require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * For products or other rows
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package BuildOrder/Row
 */
class DeliverOrderRowBuilder {
  
    /**
     * Contains empty string if not set
     * @var type 
     */
    public $unit = "";

    /**
     * Contains int 0 if not set
     * @var type 
     */
    public $vatDiscount = 0;
    
    /**
     * @param deliverOrderBuilder $order
     */
    public function __construct(deliverOrderBuilder $order) {
        $this->order = $order;
    }
    
    /**
     * Optional
     * @param type $articleNumberAsString
     * @return \DeliverOrderRowBuilder
     */
    public function setArticleNumber($articleNumberAsString) {
        $this->articleNumber = $articleNumberAsString;
        return $this;
    }
    
    /**
     * Required
     * @param type $quantityAsInt
     * @return \DeliverOrderRowBuilder
     */
    public function setQuantity($quantityAsInt) {
        $this->quantity = $quantityAsInt;
        return $this;
    }
    
    /**
     * Optional
     * @param type $unitAsString
     * @return \DeliverOrderRowBuilder
     */
    public function setUnit($unitAsString) {
        $this->unit = $unitAsString;
        return $this;
    }
    
    /**
     * Required
     * @param type $AmountAsFloat
     * @return \DeliverOrderRowBuilder
     */
    public function setAmountExVat($AmountAsFloat) {
        $this->amountExVat = $AmountAsFloat;
        return $this;
    }
    /**
     * Required
     * @param type $AmountAsFloat
     * @return \DeliverOrderRowBuilder
     */
    public function setAmountIncVat($AmountAsFloat) {
        $this->amountIncVat = $AmountAsFloat;
        return $this;
    }
    
    /**
     * Optional
     * @param type $nameAsString
     * @return \DeliverOrderRowBuilder
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }
    
    /**
     * Optional
     * @param type $descriptionAsString
     * @return \DeliverOrderRowBuilder
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }
    
    /**
     * Required
     * @param type $percentAsInt
     * @return \DeliverOrderRowBuilder
     */
    public function setVatPercent($percentAsInt) {
        $this->vatPercent = $percentAsInt;
        return $this;
    }
    
    /**
     * Optional
     * @param type $discountPercentAsInteger
     * @return \DeliverOrderRowBuilder
     */
    public function setDiscountPercent($discountPercentAsInteger) {
        $this->discountPercent = $discountPercentAsInteger;
        return $this;
    }
    
    /**
     * Code completion comment
     * @return deliverOrderBuilder orderBuilder
     */
    public function endOrderRow() {
        return $this->order;
    }
}

?>
