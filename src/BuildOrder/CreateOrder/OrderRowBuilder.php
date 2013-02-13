<?php

//require_once SVEA_REQUEST_DIR . '/Includes.php';
require_once '/../RowBuilder.php';

/**
 * OrderRowBuilder.
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class OrderRowBuilder {
  
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
     * @param createOrder $order
     */
    public function __construct(createOrder $order) {
        $this->order = $order;
    }
    
    /**
     * Optional
     * @param type $articleNumberAsString
     * @return \OrderRowBuilder
     */
    public function setArticleNumber($articleNumberAsString) {
        $this->articleNumber = $articleNumberAsString;
        return $this;
    }
    
    /**
     * Required
     * @param type $quantityAsInt
     * @return \OrderRowBuilder
     */
    public function setQuantity($quantityAsInt) {
        $this->quantity = $quantityAsInt;
        return $this;
    }
    
    /**
     * Optional
     * @param type $unitAsString
     * @return \OrderRowBuilder
     */
    public function setUnit($unitAsString) {
        $this->unit = $unitAsString;
        return $this;
    }
    
    /**
     * Optional
     * @param type $AmountAsFloat
     * @return \OrderRowBuilder
     */
    public function setAmountExVat($AmountAsFloat) {
        $this->amountExVat = $AmountAsFloat;
        return $this;
    }
    /**
     * Optional
     * @param type $AmountAsFloat
     * @return \OrderRowBuilder
     */
    public function setAmountIncVat($AmountAsFloat) {
        $this->amountIncVat = $AmountAsFloat;
        return $this;
    }
    
    /**
     * Optional
     * @param type $nameAsString
     * @return \OrderRowBuilder
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }
    
    /**
     * Optional
     * @param type $descriptionAsString
     * @return \OrderRowBuilder
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }
    
    /**
     * Optional
     * @param type $percentAsInt
     * @return \OrderRowBuilder
     */
    public function setVatPercent($percentAsInt) {
        $this->vatPercent = $percentAsInt;
        return $this;
    }
    
    /**
     * Optional
     * @param type $discountPercentAsInteger
     * @return \OrderRowBuilder
     */
    public function setDiscountPercent($discountPercentAsInteger) {
        $this->discountPercent = $discountPercentAsInteger;
        return $this;
    }
    
    /**
     * Code completion comment
     * @return createOrder orderBuilder
     */
    public function endOrderRow() {
        return $this->order;
    }
}

?>
