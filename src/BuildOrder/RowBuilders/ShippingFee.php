<?php

namespace Svea\WebPay\BuildOrder\RowBuilders;

/**
 * Use this class to add shipping to the order.
 *
 * @author anne-hal, Kristian Grossman-Madsen
 */
class ShippingFee
{
	/**
	 * @var float $quantity quantity is always 1
	 */
	public $quantity;

	/**
	 * @var string $shippingId
	 */
	public $shippingId;

	/**
	 * @var string $name
	 */
	public $name;

	/**
	 * @var string $description
	 */
	public $description;


	/**
	 * @var float $amountExVat
	 */
	public $amountExVat;


	/**
	 * @var float $amountIncVat
	 */
	public $amountIncVat;

	/**
	 * @var int $vatPercent
	 */
	public $vatPercent;

	/**
	 * @var string $unit
	 */
	public $unit;

	/**
	 * @var int $vatDiscount -- defaults to zero (0)
	 */
	public $discountPercent;

	/**
	 * @var string $temporaryReference - Option parameter.
	 */
	public $temporaryReference;

	/**
	 * ShippingFee constructor.
	 */
	function __construct()
	{
		// set to 1, as this attribute is used by WebServiceRowFormatter() and all shipping rows are for one (1) unit
		$this->quantity = 1;
	}

	/**
	 * Optional
	 * @param string $idAsString
	 * @return $this
	 */
	public function setShippingId($idAsString)
	{
		$this->shippingId = $idAsString;
		return $this;
	}

	/**
	 * Optional - short item name
	 *
	 * Note that this will be merged with the item description when the request is sent to Svea
	 *
	 * @param string $nameAsString
	 * @return $this
	 */
	public function setName($nameAsString)
	{
		$this->name = $nameAsString;
		return $this;
	}

	/**
	 * Optional - long item description
	 *
	 * Note that this will be merged with the item name when the request is sent to Svea
	 *
	 * @param string $descriptionAsString
	 * @return $this
	 */
	public function setDescription($descriptionAsString)
	{
		$this->description = $descriptionAsString;
		return $this;
	}

	/**
	 * Recommended - precisely two of these values must be set in the Svea\WebPay\WebPayItem object:  AmountExVat, AmountIncVat or VatPercent for Orderrow.
	 * Use functions setAmountExVat(), setAmountIncVat() or setVatPercent(). The recommended is to use setAmountExVat() and setVatPercent().
	 *
	 * Order row item price excluding taxes, expressed as a float value.
	 *
	 * This action is not allowed for Checkout payment
	 *
	 * @param float $amountAsFloat
	 * @return $this
	 */
	public function setAmountExVat($amountAsFloat)
	{
		$this->amountExVat = $amountAsFloat;
		return $this;
	}

	/**
	 * Optional - precisely two of these values must be set in the Svea\WebPay\WebPayItem object:  AmountExVat, AmountIncVat or VatPercent for Orderrow.
	 * Use functions setAmountExVat(), setAmountIncVat() or setVatPercent(). The recommended is to use setAmountExVat() and setVatPercent().
	 *
	 * Order row item price including tax, expressed as a float value.
	 *
	 * If you specify AmountIncVat, note that this may introduce a cumulative rounding error when ordering large
	 * quantities of an item, as the package bases the total order sum on a calculated price ex. vat.
	 *
	 * Also, Svea uses bankers rounding (half-to-even) when calculating the order total, so at times a rounding error of at most
	 * one cent/öre may show up if the implementation/shop does not use the same rounding method.
	 *
	 * See HostedPaymentTest for examples, including sums and calculations.
	 *
	 * @param float $amountAsFloat
	 * @return $this
	 */
	public function setAmountIncVat($amountAsFloat)
	{
		$this->amountIncVat = $amountAsFloat;
		return $this;
	}

	/**
	 * Recommended - precisely two of these values must be set in the Svea\WebPay\WebPayItem object:  AmountExVat, AmountIncVat or VatPercent for Orderrow.
	 * Use functions setAmountExVat(), setAmountIncVat() or setVatPercent(). The recommended is to use setAmountExVat() and setVatPercent().
	 *
	 * Order row item price vat rate in percent, expressed as an integer.
	 *
	 * @param int $vatPercentAsInt
	 * @return $this
	 */
	public function setVatPercent($vatPercentAsInt)
	{
		$this->vatPercent = $vatPercentAsInt;
		return $this;
	}

	/**
	 * Optional - the name of the unit used for the shipping fee.
	 * @param string $unitDescriptionAsString
	 * @return $this
	 */
	public function setUnit($unitDescriptionAsString)
	{
		$this->unit = $unitDescriptionAsString;
		return $this;
	}

	/**
	 * Optional - discount in percent, applies to this order row only
	 *
	 * @param int $discountPercentAsInt
	 * @return $this
	 */
	public function setDiscountPercent($discountPercentAsInt)
	{
		$this->discountPercent = $discountPercentAsInt;
		return $this;
	}

	/**
	 * Optional - Can be used when creating or updating an order.
	 *			  The returned rows will have their corresponding temporary reference as they were given in the in-data.
	 *			  It will not be stored and will not be returned in GetOrder.
	 * Checkout orders only. Will not be applicable for other order types.
	 * @param $temporaryReference
	 * @return $this
	 */
	public function setTemporaryReference($temporaryReference)
	{
		$this->temporaryReference = $temporaryReference;
		return $this;
	}
}
