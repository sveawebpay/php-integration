<?php

namespace Svea\WebPay\Checkout\Service;

use Svea\WebPay\BuildOrder\Validator\ValidationException;
use Svea\WebPay\Checkout\Helper\CheckoutRowFormatter;
use Svea\WebPay\Checkout\Model\CheckoutOrderRow;
use Svea\WebPay\Checkout\Service\Connection\CheckoutServiceConnection;
use Svea\WebPay\Checkout\Service\Connection\ServiceConnection;

/**
 * Class CheckoutService
 * @package Svea\Svea\WebPay\WebPay\Checkout\Service
 */
abstract class CheckoutService
{
    protected $order;

    /**
     * @var ServiceConnection
     */
    protected $serviceConnection;

    /**
     * CheckoutService constructor.
     *
     * @param $order
     */
    public function __construct($order)
    {
        $this->order = $order;
        $this->serviceConnection = new CheckoutServiceConnection($this->order->conf, $this->order->getCountryCode());
    }

    /**
     * Validate order data
     */
    abstract protected function validateOrder();

    /**
     * Format given order so that will match data structure required for API
     */
    abstract protected function prepareRequest();

    /**
     * Send call Connection Library
     */
    abstract public function doRequest();


    /**
     * @return mixed
     */
    protected function formatOrderInformationWithOrderRows()
    {
        $formatter = new CheckoutRowFormatter($this->order);
        $formattedOrderRows = $formatter->formatRows();

        return $formattedOrderRows;
    }

    /**
     * Translate SveaOrderRow to CheckoutOrderRow and return result as array
     *
     * @param \Svea\WebPay\WebService\SveaSoap\SveaOrderRow $item
     * @return array
     */
    protected function mapOrderItem($item)
    {
        $checkoutOrderItem = new CheckoutOrderRow();

        if (isset($item->Name)) {
            $checkoutOrderItem->setName($item->Name);
        }
        if (isset($item->ArticleNumber)) {
            $checkoutOrderItem->setArticleNumber($item->ArticleNumber);
        }
        if (isset($item->PricePerUnit)) {
            $checkoutOrderItem->setUnitPrice($item->PricePerUnit);
        }
        if (isset($item->VatPercent)) {
            $checkoutOrderItem->setVatPercent($item->VatPercent);
        }
        if (isset($item->DiscountPercent)) {
            $checkoutOrderItem->setDiscountPercent($item->DiscountPercent);
        }
        if (isset($item->Unit)) {
            $checkoutOrderItem->setUnit($item->Unit);
        }
        if (isset($item->NumberOfUnits)) {
            $checkoutOrderItem->setQuantity($item->NumberOfUnits);
        }
        if (isset($item->TemporaryReference)) {
            $checkoutOrderItem->setTemporaryReference($item->TemporaryReference);
        }
        if (isset($item->MerchantData)) {
            $checkoutOrderItem->setMerchantData($item->MerchantData);
        }

        return $checkoutOrderItem->toArray();
    }


    /**
     * @param array $errors
     * @throws ValidationException
     */
    protected function processErrors(array $errors)
    {
        if (count($errors) > 0) {
            $message = '';
            foreach ($errors as $key => $val) {
                $message .= " - $key : $val" . PHP_EOL;
            }
            throw new ValidationException($message);
        }
    }
}
