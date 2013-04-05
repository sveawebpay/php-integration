<?php
/**
 * Description of OrderRow
 *
 * @author anne-hal
 */
class OrderRow {

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
     * Optional
     * @param type $articleNumberAsString
     * @return \OrderRow
     */
    public function setArticleNumber($articleNumberAsString) {
        $this->articleNumber = $articleNumberAsString;
        return $this;
    }

    /**
     * Required
     * @param type $quantityAsInt
     * @return \OrderRow
     */
    public function setQuantity($quantityAsInt) {
        $this->quantity = $quantityAsInt;
        return $this;
    }

    /**
     * Optional
     * @param type $unitAsString
     * @return \OrderRow
     */
    public function setUnit($unitAsString) {
        $this->unit = $unitAsString;
        return $this;
    }

    /**
     * Optional
     * @param type $AmountAsFloat
     * @return \OrderRow
     */
    public function setAmountExVat($AmountAsFloat) {
        $this->amountExVat = $AmountAsFloat;
        return $this;
    }
    /**
     * Optional
     * @param type $AmountAsFloat
     * @return \OrderRow
     */
    public function setAmountIncVat($AmountAsFloat) {
        $this->amountIncVat = $AmountAsFloat;
        return $this;
    }

    /**
     * Optional
     * @param type $nameAsString
     * @return \OrderRow
     */
    public function setName($nameAsString) {
        $this->name = $nameAsString;
        return $this;
    }

    /**
     * Optional
     * @param type $descriptionAsString
     * @return \OrderRow
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }

    /**
     * Optional
     * @param type $percentAsInt
     * @return \OrderRow
     */
    public function setVatPercent($percentAsInt) {
        $this->vatPercent = $percentAsInt;
        return $this;
    }

    /**
     * Optional
     * @param type $discountPercentAsInteger
     * @return \OrderRow
     */
    public function setDiscountPercent($discountPercentAsInteger) {
        $this->discountPercent = $discountPercentAsInteger;
        return $this;
    }
}