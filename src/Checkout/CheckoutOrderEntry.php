<?php

namespace Svea\WebPay\Checkout;

use Svea\WebPay\Checkout\Helper\CheckoutOrderBuilder;
use Svea\WebPay\Checkout\Model\PresetValue;

/**
 * Class CheckoutOrderEntry
 * @package Svea\Svea\WebPay\WebPay\Checkout
 */
class CheckoutOrderEntry
{
    /**
     * @var CheckoutOrderBuilder
     */
    private $checkoutOrderBuilder;

    /**
     * CheckoutOrderEntry constructor.
     * @param $checkoutOrderBuilder
     */
    public function __construct($checkoutOrderBuilder)
    {
        $this->checkoutOrderBuilder = $checkoutOrderBuilder;
    }

    /**
     * Calls logic that initialize creating a Checkout Order
     * and returns response from server
     *
     * @return array
     */
    public function createOrder()
    {
        $createService = $this->checkoutOrderBuilder->createOrder();

        return $createService;
    }

    /**
     * Calls logic that initialize getting a Checkout Order
     * and returns response from server
     *
     * @return array
     */
    public function getOrder()
    {
        $getOrderService = $this->checkoutOrderBuilder->getOrder();

        return $getOrderService;
    }

    /**
     * Calls logic that initialize updating a Checkout Order
     * and returns response from server
     *
     * @return array
     */
    public function updateOrder()
    {
        $updateOrderService = $this->checkoutOrderBuilder->updateOrder();

        return $updateOrderService;
    }

    /**
     * @param string $checkoutUri
     * @return $this
     */
    public function setCheckoutUri($checkoutUri)
    {
        $this->checkoutOrderBuilder->setCheckoutUri($checkoutUri);

        return $this;
    }

    /**
     * @param string $confirmationUri
     * @return $this
     */
    public function setConfirmationUri($confirmationUri)
    {
        $this->checkoutOrderBuilder->setConfirmationUri($confirmationUri);

        return $this;
    }

    /**
     * @param string $pushUri
     * @return $this
     */
    public function setPushUri($pushUri)
    {
        $this->checkoutOrderBuilder->setPushUri($pushUri);

        return $this;
    }

    /**
     * @param string $termsUri
     * @return $this
     */
    public function setTermsUri($termsUri)
    {
        $this->checkoutOrderBuilder->setTermsUri($termsUri);

        return $this;
    }

    /**
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->checkoutOrderBuilder->setLocale($locale);

        return $this;
    }

    /**
     * Required for get and update methods
     * @param $id
     * @return $this
     */
    public function setCheckoutOrderId($id)
    {
        $this->checkoutOrderBuilder->setId($id);

        return $this;
    }

    /**
     * @param string $clientOrderNumber
     * @return $this
     */
    public function setClientOrderNumber($clientOrderNumber)
    {
        $this->checkoutOrderBuilder->setClientOrderNumber($clientOrderNumber);

        return $this;
    }

    /**
     * Add Preset Value to the list of Preset Values
     *
     * @param PresetValue $presetValues
     * @return $this
     */
    public function addPresetValue($presetValues)
    {
        $this->checkoutOrderBuilder->addPresetValue($presetValues);

        return $this;
    }

    /**
     * Required - set order country code, we recommend basing this on the customer billing address
     *
     * For orders using the invoice or payment plan payment methods, you need to supply a country code that corresponds
     * to the account credentials used for the address lookup. (Note that this means that these methods don't support
     * orders from foreign countries, this is a consequence of the fact that the invoice and payment plan payment
     * methods don't support foreign orders.)
     *
     * @param string $countryCode
     * @return $this
     */
    public function setCountryCode($countryCode)
    {
        $this->checkoutOrderBuilder->setCountryCode($countryCode);

        return $this;
    }

    /**
     * Required - you need to add at least one order row to the order
     *
     * @param \Svea\WebPay\BuildOrder\RowBuilders\OrderRow $orderRowItem
     * @return $this
     */
    public function addOrderRow($orderRowItem)
    {
        $this->checkoutOrderBuilder->addOrderRow($orderRowItem);

        return $this;
    }

    /**
     * Optional - adds a shipping fee or invoice fee to the order
     *
     * @param \Svea\WebPay\BuildOrder\RowBuilders\InvoiceFee|\Svea\WebPay\BuildOrder\RowBuilders\ShippingFee $itemFeeObject
     * @return $this
     */
    public function addFee($itemFeeObject)
    {
        $this->checkoutOrderBuilder->addFee($itemFeeObject);

        return $this;
    }

    /**
     * Optional - adds a fixed amount discount or an order total percent discount to the order
     *
     * See the discount objects for information on how the discount is calculated et al.
     *
     * @see \Svea\FixedDiscount \Svea\WebPay\BuildOrder\RowBuilders\FixedDiscount
     * @see \Svea\RelativeDiscount \Svea\WebPay\BuildOrder\RowBuilders\RelativeDiscount
     *
     * @param \Svea\WebPay\BuildOrder\RowBuilders\FixedDiscount|\Svea\WebPay\BuildOrder\RowBuilders\RelativeDiscount $itemDiscountObject
     * @return $this
     */
    public function addDiscount($itemDiscountObject)
    {
        $this->checkoutOrderBuilder->addDiscount($itemDiscountObject);

        return $this;
    }

    /**
     * Required for card payment, direct bank & PayPage payments. Ignored for invoice and payment plan.
     *
     * Ignored for invoice and payment plan orders, which use the selected client id currency, as determined by Svea\WebPay\Config\ConfigurationProvider and setCountryCode.
     *
     * @param string $currencyString in ISO 4217 three-letter format, ex. "SEK", "EUR"
     * @return $this
     */
    public function setCurrency($currencyString)
    {
        $this->checkoutOrderBuilder->setCurrency($currencyString);

        return $this;
    }

    /**
     * @return CheckoutOrderBuilder
     */
    public function getCheckoutOrderBuilder()
    {
        return $this->checkoutOrderBuilder;
    }
}
