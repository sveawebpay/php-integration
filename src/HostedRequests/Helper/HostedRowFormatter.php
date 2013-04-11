<?php

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
            $tempRow = new HostedOrderRowBuilder();
            $plusVatCounter = isset($row->vatPercent) ? ($row->vatPercent * 0.01) + 1 : "";

            if(isset($row->name)) {
                $tempRow->setName($row->name);
            }
            if(isset($row->description)) {
                $tempRow->setDescription($row->description);
            }
            if(isset($row->amountExVat) && isset($row->vatPercent)){
                $tempRow->setAmount(round(($row->amountExVat * 100) * $plusVatCounter));
                $tempRow->setVat(round($tempRow->amount - ($row->amountExVat * 100)));
            }elseif (isset($row->amountIncVat) && isset($row->vatPercent)) {
                 $tempRow->setAmount(round($row->amountIncVat * 100));
                 $tempRow->setVat(round($tempRow->amount - ($tempRow->amount / $plusVatCounter)));
            }  else {
                 $tempRow->setAmount(round($row->amountIncVat * 100));
                 $tempRow->setVat(($row->amountIncVat - $row->amountExVat) * 100);
            }

            if(isset($row->unit)) {
                $tempRow->setUnit($row->unit);
            }
            if(isset($row->articleNumber)) {
                $tempRow->setSku($row->articleNumber);
            }
            if(isset($row->quantity)){
                $tempRow->setQuantity($row->quantity);
            }

            $this->newRows[] = $tempRow;
            $this->totalAmount += $tempRow->amount * $row->quantity;
            $this->totalVat +=  $tempRow->vat * $row->quantity;
        }
    }

    private function formatShippingFeeRows($order) {
        if(!isset($order->shippingFeeRows)) {
            return;
        }

        foreach ($order->shippingFeeRows as $row) {
            $tempRow = new HostedOrderRowBuilder();
            $plusVatCounter = isset($row->vatPercent) ? ($row->vatPercent * 0.01) + 1 : "";

            if(isset($row->name)) {
                $tempRow->setName($row->name);
            }
            if(isset($row->description)) {
                $tempRow->setDescription($row->description);
            }
            if(isset($row->amountExVat) && isset($row->vatPercent)){
                $tempRow->setAmount(round(($row->amountExVat * 100) * $plusVatCounter));
                $tempRow->setVat(round($tempRow->amount - ($row->amountExVat * 100)));
            }elseif (isset($row->amountIncVat) && isset($row->vatPercent)) {
                 $tempRow->setAmount(round($row->amountIncVat * 100));
                 $tempRow->setVat(round($tempRow->amount - ($tempRow->amount / $plusVatCounter)));
            }  else {
                 $tempRow->setAmount(round($row->amountIncVat * 100));
                 $tempRow->setVat(($row->amountIncVat - $row->amountExVat) * 100);
            }

            if(isset($row->unit)) {
                $tempRow->setUnit($row->unit);
            }

            if(isset($row->shippingId)) {
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
        if(!isset($order->fixedDiscountRows)) {
            return;
        }

        foreach ($order->fixedDiscountRows as $row) {
            $discountInPercent = ($row->amount * 100)/ $this->totalAmount;
            $tempRow = new HostedOrderRowBuilder();

            if(isset($row->name)) {
                $tempRow->setName($row->name);
            }

            if(isset($row->description)) {
                $tempRow->setDescription($row->description);
            }

            $tempRow->setAmount(- round($row->amount * 100));

            //Fix: vat could bu 0
           // if($this->totalVat > 0) {
                $vat = $this->totalVat * $discountInPercent;
                $tempRow->setVat(-round($vat));

           // }

            if(isset($row->unit)) {
                $tempRow->setUnit($row->unit);
            }

            if(isset($row->discountId)) {
                $tempRow->setSku($row->discountId);
            }
            $tempRow->setQuantity(1);
              $this->totalAmount -= $row->amount;
              $this->totalVat -= substr($tempRow->vat, 1);
            $this->newRows[] = $tempRow;

        }
    }

    public function formatRelativeDiscountRows($order) {
        if(!isset($order->relativeDiscountRows)) {
            return;
        }

        foreach ($order->relativeDiscountRows as $row) {
            $discountCounter = $row->discountPercent * 0.01; //e.g. 0.20
            $tempRow = new HostedOrderRowBuilder();

            if(isset($row->name)) {
                $tempRow->setName($row->name);
            }

            if(isset($row->description)) {
                $tempRow->setDescription($row->description);
            }

            if(isset($row->discountId)) {
                $tempRow->setSku($row->discountId);
            }

            if(isset($row->unit)) {
                $tempRow->setUnit($row->unit);
            }

            $tempRow->setAmount(- round(($discountCounter * $this->totalAmount)));


            // Vat could be 0
           // if($this->totalVat > 0) {
                $tempRow->setVat(- round(($this->totalVat * $discountCounter)));
           // }

            $tempRow->setQuantity(1);
            $this->totalAmount -= $tempRow->amount;
            $this->totalVat -= substr($tempRow->vat,1);
            $this->newRows[] = $tempRow;
        }
    }

    public function formatTotalAmount($rows) {
        $result = 0;

        foreach ($rows as $row) {
            if(substr($row->amount, 0,1) == "-") {
                $result -= (substr($row->amount, 1))*$row->quantity;
            } else {
                $result += $row->amount * $row->quantity;
            }
        }

        return $result;
    }

    public function formatTotalVat($rows) {
        $result = 0;

        foreach ($rows as $row) {
            if(substr($row->vat, 0,1) == "-") {
                $result -= substr($row->vat, 1) * $row->quantity;
            } else {
                $result += $row->vat * $row->quantity;
            }
        }
        return $result;
    }
}