<?php
namespace Svea;

/**
 *
 */
class HostedRowFormatter {

    private $totalAmount;
    private $totalVat;
    private $newRows;

    /**
     *
     */
    public function __construct() {
        $this->totalAmount = 0;
        $this->totalVat = 0;
        $this->newRows = array();
    }

    /**
     * Format rows and calculate vat
     * @param type $rows
     * @return int
     */
    public function formatRows($order) {
        $this->formatOrderRows($order);
        $this->formatShippingFeeRows($order);
        $this->formatFixedDiscountRows($order);
        $this->formatRelativeDiscountRows($order);

        return $this->newRows;
    }

    private function formatOrderRows($order) {
        foreach ($order->orderRows as $row ) {
            $tempRow = new HostedOrderRowBuilder();     // new empty object
            if (isset($row->vatPercent)) {
                $plusVatCounter = bcmul($row->vatPercent, 0.01) + 1;
            } else {
                $plusVatCounter = '';
            }

            if (isset($row->name)) {
                $tempRow->setName($row->name);
            }

            if (isset($row->description)) {
                $tempRow->setDescription($row->description);
            }

            // calculate amount, vat from two out of three given by customer, see unit tests HostedRowFormater
            if (isset($row->amountExVat) && isset($row->vatPercent)) {
                $amount = bcmul(bcmul($row->amountExVat, 100), $plusVatCounter);
                $vat = bcsub($amount, bcmul($row->amountExVat, 100));
            } elseif (isset($row->amountIncVat) && isset($row->vatPercent)) {
                $amount = bcmul($row->amountIncVat, 100);
                $vat = bcsub($amount, bcdiv($amount, $plusVatCounter));
            } else {
                $amount = bcmul($row->amountIncVat, 100);
                $vat = bcmul(bcsub($row->amountIncVat, $row->amountExVat), 100);
            }

            $tempRow->setAmount($amount);
            $tempRow->setVat($vat);

            if (isset($row->unit)) {
                $tempRow->setUnit($row->unit);
            }

            if (isset($row->articleNumber)) {
                $tempRow->setSku($row->articleNumber);
            }

            if (isset($row->quantity)) {
                $tempRow->setQuantity($row->quantity);
            }

            $this->newRows[] = $tempRow;
            $this->totalAmount += bcmul($tempRow->amount, $row->quantity);
            $this->totalVat += bcmul($tempRow->vat, $row->quantity);
        }
    }

    private function formatShippingFeeRows($order) {
        if (!isset($order->shippingFeeRows)) {
            return;
        }

        foreach ($order->shippingFeeRows as $row) {
            $tempRow = new HostedOrderRowBuilder();
            if (isset($row->vatPercent)) {
                $plusVatCounter = bcmul($row->vatPercent, 0.01) + 1;
            } else {
                $plusVatCounter = '';
            }

            if (isset($row->articleNumber)) {
                $tempRow->setSku($row->articleNumber);
            }

            if (isset($row->name)) {
                $tempRow->setName($row->name);
            }

            if (isset($row->description)) {
                $tempRow->setDescription($row->description);
            }

            if (isset($row->amountExVat) && isset($row->vatPercent)) {
                $amount = bcmul(bcmul($row->amountExVat, 100), $plusVatCounter);
                $vat = bcsub($amount, bcmul($row->amountExVat, 100));
            } elseif (isset($row->amountIncVat) && isset($row->vatPercent)) {
                $amount = bcmul($row->amountIncVat, 100);
                $vat = bcsub($amount, bcdiv($amount, $plusVatCounter));
            } else {
                $amount = bcmul($row->amountIncVat, 100);
                $vat = bcmul(bcsub($row->amountIncVat, $row->amountExVat), 100);
            }

            $tempRow->setAmount($amount);
            $tempRow->setVat($vat);

            if (isset($row->unit)) {
                $tempRow->setUnit($row->unit);
            }

            if (isset($row->shippingId)) {
                $tempRow->setSku($row->shippingId);
            }

            $tempRow->setQuantity(1);
            $this->newRows[] = $tempRow;
           // $this->totalAmount += $tempRow->amount;
            //$this->totalVat += $tempRow->vat;
        }
    }

    //check!
    public function formatFixedDiscountRows($order) {
        if (!isset($order->fixedDiscountRows)) {
            return;
        }

        foreach ($order->fixedDiscountRows as $row) {
            $discountInPercent = bcdiv(bcmul($row->amount, 100), $this->totalAmount);
            $tempRow = new HostedOrderRowBuilder();

            if (isset($row->name)) {
                $tempRow->setName($row->name);
            }

            if (isset($row->description)) {
                $tempRow->setDescription($row->description);
            }

            $tempRow->setAmount(- round($row->amount * 100));

            //Fix: vat could bu 0
           // if ($this->totalVat > 0) {
                $vat = bcmul($this->totalVat, $discountInPercent);
                $tempRow->setVat(-$vat);
           // }

            if (isset($row->unit)) {
                $tempRow->setUnit($row->unit);
            }

            if (isset($row->discountId)) {
                $tempRow->setSku($row->discountId);
            }

            $tempRow->setQuantity(1);
            $this->totalAmount -= $row->amount;
            $this->totalVat -= abs($tempRow->vat);
            $this->newRows[] = $tempRow;
        }
    }

    public function formatRelativeDiscountRows($order) {
        if (!isset($order->relativeDiscountRows)) {
            return;
        }

        foreach ($order->relativeDiscountRows as $row) {
            $discountCounter = bcmul($row->discountPercent, 0.01); //e.g. 0.20
            $tempRow = new HostedOrderRowBuilder();

            if (isset($row->name)) {
                $tempRow->setName($row->name);
            }

            if (isset($row->description)) {
                $tempRow->setDescription($row->description);
            }

            if (isset($row->discountId)) {
                $tempRow->setSku($row->discountId);
            }

            if (isset($row->unit)) {
                $tempRow->setUnit($row->unit);
            }

            $tempRow->setAmount(-bcmul($discountCounter, $this->totalAmount));

            // Vat could be 0
            // if ($this->totalVat > 0) {
                $tempRow->setVat(-bcmul($this->totalVat, $discountCounter));
            // }

            $tempRow->setQuantity(1);
            $this->totalAmount -= $tempRow->amount;
            $this->totalVat -= abs($tempRow->vat);
            $this->newRows[] = $tempRow;
        }
    }

    public function formatTotalAmount($rows) {
        $result = 0;

        foreach ($rows as $row) {
            if ($row->amount < 0) {
                $result -= bcmul(abs($row->amount), $row->quantity);
            } else {
                $result += bcmul($row->amount, $row->quantity);
            }
        }

        return $result;
    }

    public function formatTotalVat($rows) {
        $result = 0;

        foreach ($rows as $row) {
            if ($row->vat < 0) {
                $result -= bcmul(abs($row->vat), $row->quantity);
            } else {
                $result += bcmul($row->vat, $row->quantity);
            }
        }

        return $result;
    }
}
