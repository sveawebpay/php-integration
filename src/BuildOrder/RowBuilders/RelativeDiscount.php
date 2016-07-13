<?php

namespace Svea\WebPay\BuildOrder\RowBuilders;

/**
 * Use RelativeDiscount() when the discount or coupon is expressed as a percentage of the total product amount.
 */
class RelativeDiscount
{
    /**
     * @var string
     */
    public $discountId;

    /**
     * @var float
     */
    public $discountPercent;

    /**
     * @var string
     */
    public $unit;

    /**
     * @var string $name
     */
    public $name;

    /**
     * @var string $description
     */
    public $description;


    /**
     * Optional
     * @param string $idAsString
     * @return $this
     */
    public function setDiscountId($idAsString)
    {
        $this->discountId = $idAsString;
        return $this;
    }

    /**
     * Required
     * The percentage of the discount, either a float or a real number
     *
     * @param number $discountPercentOnTotalAmountAsNumber
     * @return $this
     */
    public function setDiscountPercent($discountPercentOnTotalAmountAsNumber)
    {
        $this->discountPercent = $discountPercentOnTotalAmountAsNumber;
        return $this;
    }

    /**
     * Optional
     * @param string $unitDescriptionAsString
     * @return $this
     */
    public function setUnit($unitDescriptionAsString)
    {
        $this->unit = $unitDescriptionAsString;
        return $this;
    }

    /**
     * Optional
     * @param string $nameAsString
     * @return $this
     */
    public function setName($nameAsString)
    {
        $this->name = $nameAsString;
        return $this;
    }

    /**
     * Optional
     * @param string $descriptionAsString
     * @return $this
     */
    public function setDescription($descriptionAsString)
    {
        $this->description = $descriptionAsString;
        return $this;
    }
}
