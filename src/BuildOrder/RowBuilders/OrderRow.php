<?php
namespace Svea;

/**
 * Instances of OrderRow usually conform to a single order row, containing one
 * or more order items which have certain attributes. These are all optional
 * and are created with the vaious set<Attribute> methods below.
 * 
 * Argument type is enforced when populating the order row through these 
 * methods, which throw InvalidArgumentException if the argument has the wrong 
 * type.
 * 
 * @author anne-hal, Kristian Grossman-Madsen
 */
class OrderRow {

    /**
     * Contains empty string if not set
     * @var string
     */
    public $unit = "";

    /**
     * Contains int 0 if not set
     * @var int
     */
    public $vatDiscount = 0;

    /**
     * Optional
     * @param string $articleNumberAsString
     * @return Svea\OrderRow
     * @throws \InvalidArgumentException
     */
    public function setArticleNumber($articleNumberAsString) {
        if( !is_string( $articleNumberAsString ) )
            throw new \InvalidArgumentException( '$articleNumberAsString is not of type string.');

        $this->articleNumber = $articleNumberAsString;
        return $this;
    }

    /**
     * Required
     * @param int $quantityAsInt
     * @return Svea\OrderRow
     */
    public function setQuantity($quantityAsInt) {
        if( !is_int( $quantityAsInt ) )
            throw new \InvalidArgumentException( '$quantityAsInt is not of type int.');

        $this->quantity = $quantityAsInt;
        return $this;
    }

    /**
     * Optional
     * @param string $unitAsString
     * @return Svea\OrderRow
     */
    public function setUnit($unitAsString) {
        if( !is_string( $unitAsString ) )
            throw new \InvalidArgumentException( '$unitAsString is not of type string.');   
        
        $this->unit = $unitAsString;
        return $this;
    }

    /**
     * Optional
     * @param float $AmountAsFloat
     * @return Svea\OrderRow
     */
    public function setAmountExVat($amountAsFloat) {
        if( !is_float( $amountAsFloat ) )
            throw new \InvalidArgumentException( '$amountAsFloat is not of type float.');    
                
        $this->amountExVat = $amountAsFloat;
        return $this;
    }
    
    /**
     * Optional
     * @param float $AmountAsFloat
     * @return Svea\OrderRow
     */
    public function setAmountIncVat($amountAsFloat) {
        if( !is_float( $amountAsFloat ) )
            throw new \InvalidArgumentException( '$amountAsFloat is not of type float.');    
          
        $this->amountIncVat = $amountAsFloat;
        return $this;
    }

    /**
     * Optional
     * @param string $nameAsString
     * @return Svea\OrderRow
     */
    public function setName($nameAsString) {
        if( !is_string( $nameAsString ) )
            throw new \InvalidArgumentException( '$nameAsString is not of type string.');   

        $this->name = $nameAsString;
        return $this;
    }

    /**
     * Optional
     * @param string $descriptionAsString
     * @return Svea\OrderRow
     */
    public function setDescription($descriptionAsString) {
        if( !is_string( $descriptionAsString ) )
            throw new \InvalidArgumentException( '$descriptionAsString is not of type string.');   

        $this->description = $descriptionAsString;
        return $this;
    }

    /**
     * Optional
     * @param int $vatPercentAsInt
     * @return Svea\OrderRow
     */
    public function setVatPercent($vatPercentAsInt) {
        if( !is_int( $vatPercentAsInt ) )
            throw new \InvalidArgumentException( '$vatPercentAsInt is not of type int.');

        $this->vatPercent = $vatPercentAsInt;
        return $this;
    }

    /**
     * Optional
     * @param int $discountPercentAsInt
     * @return Svea\OrderRow
     */
    public function setDiscountPercent($discountPercentAsInt) {
        if( !is_int( $discountPercentAsInt ) )
            throw new \InvalidArgumentException( '$discountPercentAsInt is not of type int.');

        $this->discountPercent = $discountPercentAsInt;
        return $this;
    }
}
