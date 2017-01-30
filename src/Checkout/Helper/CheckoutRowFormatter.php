<?php

namespace Svea\WebPay\Checkout\Helper;

use Svea\WebPay\Helper\Helper;
use Svea\WebPay\WebService\Helper\WebServiceRowFormatter;
use Svea\WebPay\WebService\SveaSoap\SveaOrderRow;

/**
 * Checkout Row Formatter is used for
 * formatting passed order rows depending on their type
 *
 * Class CheckoutRowFormatter
 * @package Svea\Svea\WebPay\WebPay\Checkout\Helper
 */
class CheckoutRowFormatter extends WebServiceRowFormatter
{
    /**
     * Go through list of passed Order Rows and
     * call appropriate format methods.
     *
     * @return array
     */
    public function formatRows()
    {
        $this->newRows = array();

        $this->calculateTotals();

        if ($this->resendOrderVat === null) {
            $this->determineVatFlag();
        } else {
            $this->priceIncludingVat = $this->resendOrderVat ? false : true;
        }

        foreach ($this->order->rows as $row) {
            switch (get_class($row)) {
                case 'Svea\WebPay\BuildOrder\RowBuilders\OrderRow':
                    $this->formatOrderRows($row);
                    break;
                case 'Svea\WebPay\BuildOrder\RowBuilders\ShippingFee':
                    $this->formatShippingFeeRows($row);
                    break;
                case 'Svea\WebPay\BuildOrder\RowBuilders\InvoiceFee':
                    $this->formatInvoiceFeeRows($row);
                    break;
                case 'Svea\WebPay\BuildOrder\RowBuilders\FixedDiscount':
                    $this->formatFixedDiscountRows($row);
                    break;
                default:
                    $this->handleOtherOrderRow($row);
                    break;
            }
        }

        foreach ($this->newRows as $row) {
            $this->formatValues($row);
        }

        return $this->newRows;
    }

    /**
     * Format OrderRow
     * @param $row
     */
    protected function formatOrderRows($row)
    {
        $tempRow = new SveaOrderRow();     // new empty object

        if (isset($row->name)) {
            $tempRow->Name = $row->name;
        }

        if (isset($row->articleNumber)) {
            $tempRow->ArticleNumber = $row->articleNumber;
        }

        if (isset($row->quantity)) {
            $tempRow->NumberOfUnits = $row->quantity;
        }

        if (isset($row->amountIncVat)) {
            $tempRow->PricePerUnit = $row->amountIncVat;
        }

        if (isset($row->discountPercent)) {
            $tempRow->DiscountPercent = $row->discountPercent;
        }

        if (isset($row->vatPercent)) {
            $tempRow->VatPercent = $row->vatPercent;
        }

        if (isset($row->unit)) {
            $tempRow->Unit = $row->unit;
        }

        if (isset($row->temporaryReference)) {
            $tempRow->TemporaryReference = $row->temporaryReference;
        }

        $this->newRows[] = $tempRow;
    }

    private function formatValues($row)
    {
        if (isset($row->PricePerUnit)) {
            $row->PricePerUnit = Helper::bround($row->PricePerUnit, 2) * 100;
        }

        // @todo - check if this should be from 1-100 or like minor currency
        if (isset($row->DiscountPercent)) {
            $row->DiscountPercent = Helper::bround($row->DiscountPercent, 2) * 100;
        }

        if (isset($row->VatPercent)) {
            $row->VatPercent = Helper::bround($row->VatPercent, 2) * 100;
        }
    }

    private function handleOtherOrderRow($row)
    {
        $type = get_class($row);
        throw new \Exception("This functionality currently does not support this Order type ($type)");
    }
}
