<?php

/**
 * Helpclass for formatting orderrows for the right format for WebServeice soap-calls
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package WebServiceRequests/Helper
 */
class WebServiceRowFormatter {

    private $order;
    private $totalAmountExVat;
    private $totalAmountInclVat;
    private $totalVatAsAmount;
    private $totalVatAsPercent;
    private $newRows;

    /**
     * @param type $order
     */
    public function __construct($order) {
        $this->order = $order;
    }

    public function formatRows() {
        $this->newRows = array();

        $this->calculateTotals();

        $this->formatOrderRows();
        $this->formatShippingFeeRows();
        $this->formatInvoiceFeeRows();
        $this->formatFixedDiscountRows();
        $this->formatRelativeDiscountRows();

        return $this->newRows;
    }

    private function calculateTotals() {
        $this->totalAmountExVat = 0;
        $this->totalVatAsAmount = 0;

        foreach ($this->order->orderRows as $product) {
            $vatPercentAsCeroDecimal = isset($product->vatPercent) ? $product->vatPercent * 0.01 : "";
            if(isset($product->vatPercent) && isset($product->amountExVat)){
                $this->totalAmountExVat += $product->amountExVat * $product->quantity;
                $this->totalVatAsAmount += ($vatPercentAsCeroDecimal * $product->amountExVat) * $product->quantity;
            }elseif (isset($product->vatPercent) && isset($product->amountIncVat)) {
                $this->totalAmountInclVat += $product->amountIncVat * $product->quantity;
                $this->totalVatAsAmount += (($vatPercentAsCeroDecimal /(1 + $vatPercentAsCeroDecimal)) * $product->amountIncVat) * $product->quantity;
            }else {
                $this->totalAmountInclVat += $product->amountIncVat * $product->quantity;
                $this->totalAmountExVat += $product->amountExVat * $product->quantity;
                $this->totalVatAsAmount += ($product->amountIncVat - $product->amountExVat)* $product->quantity;
            }

        }
        $this->totalAmountInclVat = $this->totalAmountExVat + $this->totalVatAsAmount;
        $this->totalAmountExVat = $this->totalAmountInclVat - $this->totalVatAsAmount;
        if ($this->totalAmountExVat > 0) {
            $this->totalVatAsPercent = $this->totalVatAsAmount / $this->totalAmountInclVat; //e.g. 0,20 if percentage 20
        }
    }

    private function formatOrderRows() {
        foreach ($this->order->orderRows as $row) {
            $orderRow = new SveaOrderRow();
            if (isset($row->articleNumber)) {
                $orderRow->ArticleNumber = $row->articleNumber;
            }
            if (isset($row->description)) {
                $orderRow->Description = (isset($row->name) ? $row->name . ': ' : "") . $row->description;
            } elseif (isset($row->name) && isset($row->description) == false) {
                $orderRow->Description = $row->name;
            }
            if (isset($row->unit)) {
                $orderRow->Unit = $row->unit;
            }
            $orderRow->DiscountPercent = (isset($row->discountPercent) ? $row->discountPercent : 0);
            $orderRow->NumberOfUnits = $row->quantity;
            if(isset($row->vatPercent) && isset($row->amountExVat)){
                $orderRow->PricePerUnit = $row->amountExVat;
                $orderRow->VatPercent = round($row->vatPercent);
            }elseif (isset($row->vatPercent) && isset($row->amountIncVat)) {
                $orderRow->PricePerUnit = $row->amountIncVat / ((0.01 * $row->vatPercent) + 1);
                $orderRow->VatPercent = round($row->vatPercent);
            }  else {
                $orderRow->PricePerUnit = number_format($row->amountExVat, 2, '.', '');
                $orderRow->VatPercent = round((($row->amountIncVat / $row->amountExVat)-1) * 100);
            }

            $this->newRows[] = $orderRow;
        }
    }

    private function formatShippingFeeRows() {
        if (!isset($this->order->shippingFeeRows)) {
            return;
        }

        foreach ($this->order->shippingFeeRows as $row) {
            $orderRow = new SveaOrderRow();
            if (isset($row->shippingId)) {
                $orderRow->ArticleNumber = $row->shippingId;
            }
            if (isset($row->description)) {
                $orderRow->Description = (isset($row->name) ? $row->name . ': ' : "") . $row->description;
            } elseif (isset($row->name) && isset($row->description) == false) {
                $orderRow->Description = $row->name;
            }
            if (isset($row->unit)) {
                $orderRow->Unit = $row->unit;
            }
            $orderRow->DiscountPercent = (isset($row->discountPercent) ? $row->discountPercent : 0);
            $orderRow->NumberOfUnits = 1; //only one fee per row
           if(isset($row->vatPercent) && isset($row->amountExVat)){
                $orderRow->PricePerUnit = $row->amountExVat;
                $orderRow->VatPercent = round($row->vatPercent);
            }elseif (isset($row->vatPercent) && isset($row->amountIncVat)) {
                $orderRow->PricePerUnit = $row->amountIncVat / ((0.01 * $row->vatPercent) + 1);
                $orderRow->VatPercent = round($row->vatPercent);
            }  else {
                $orderRow->PricePerUnit = number_format($row->amountExVat, 2, '.', '');
                $orderRow->VatPercent = round((($row->amountIncVat / $row->amountExVat)-1) * 100);
            }
            $this->newRows[] = $orderRow;
        }
    }

    private function formatInvoiceFeeRows() {
        if (!isset($this->order->invoiceFeeRows)) {
            return;
        }

        foreach ($this->order->invoiceFeeRows as $row) {
            $orderRow = new SveaOrderRow();
            $orderRow->ArticleNumber = "";
            if (isset($row->description))
                if (isset($row->description)) {
                    $orderRow->Description = (isset($row->name) ? $row->name . ': ' : "") . $row->description;
                } elseif (isset($row->name) && isset($row->description) == false) {
                    $orderRow->Description = $row->name;
                }
            if (isset($row->unit)) {
                $orderRow->Unit = $row->unit;
            }
            $orderRow->DiscountPercent = isset($row->discountPercent) ? $row->discountPercent : 0;
            $orderRow->NumberOfUnits = 1; //only one fee per row
            if(isset($row->vatPercent) && isset($row->amountExVat)){
                $orderRow->PricePerUnit = $row->amountExVat;
                $orderRow->VatPercent = round($row->vatPercent);
            }elseif (isset($row->vatPercent) && isset($row->amountIncVat)) {
                $orderRow->PricePerUnit = $row->amountIncVat / ((0.01 * $row->vatPercent) + 1);
                $orderRow->VatPercent = round($row->vatPercent);
            }  else {
                $orderRow->PricePerUnit = number_format($row->amountExVat, 2, '.', '');
                $orderRow->VatPercent = round((($row->amountIncVat / $row->amountExVat)-1) * 100);
            }
            $this->newRows[] = $orderRow;
        }
    }

    private function formatFixedDiscountRows() {
        if (!isset($this->order->fixedDiscountRows)) {
            return;
        }

        foreach ($this->order->fixedDiscountRows as $row) {
            $productTotalAfterDiscount = $this->totalAmountInclVat - $row->amount; //e.g. 400 if total 500 and discount 100
            $totalProductVatAsAmountAfterDiscount = $this->totalVatAsPercent * $productTotalAfterDiscount;
            $discountVatAsAmount = $this->totalVatAsAmount - $totalProductVatAsAmountAfterDiscount;
            $orderRow = new SveaOrderRow();
            if (isset($row->discountId)) {
                $orderRow->ArticleNumber = $row->discountId;
            }
            if (isset($row->description)) {
                $orderRow->Description = (isset($row->name) ? $row->name . ': ' : "") . $row->description;
            } elseif (isset($row->name) && isset($row->description) == false) {
                $orderRow->Description = $row->name;
            }
            if (isset($row->unit)) {
                $orderRow->Unit = $row->unit;
            }
            $orderRow->DiscountPercent = 0; //no discount on discount
            $orderRow->NumberOfUnits = 1; //only one discount per row
            $orderRow->PricePerUnit = - number_format($row->amount - $discountVatAsAmount, 2,'.','');
            $orderRow->VatPercent = round(($discountVatAsAmount / ($row->amount - $discountVatAsAmount))*100);//Discountpercent
            $this->newRows[] = $orderRow;
        }
    }

    private function formatRelativeDiscountRows() {
        if (!isset($this->order->relativeDiscountRows)) {
            return;
        }

        foreach ($this->order->relativeDiscountRows as $row) {
            $orderRow = new SveaOrderRow();
            if (isset($row->discountId)) {
                $orderRow->ArticleNumber = $row->discountId;
            }
            if (isset($row->description)) {
                $orderRow->Description = (isset($row->name) ? $row->name . ': ' : "") . $row->description;
            } elseif (isset($row->name) && isset($row->description) == false) {
                $orderRow->Description = $row->name;
            }
            if (isset($row->unit)) {
                $orderRow->Unit = $row->unit;
            }
            $pricePerUnitExMoms = round($this->totalAmountExVat * ($row->discountPercent * 0.01), 2);
            $orderRow->DiscountPercent = 0; //no discount on discount
            $orderRow->NumberOfUnits = 1; //only one discount per row
            $orderRow->PricePerUnit = - number_format($pricePerUnitExMoms,2,'.',''); //Discountpercent on toatal price ex vat.
            $orderRow->VatPercent = round((($this->totalVatAsAmount * ($row->discountPercent * 0.01))/$pricePerUnitExMoms)*100,2);//round((($this->totalVatAsAmount * ($row->discountPercent * 0.01))/$orderRow->PricePerUnit) * 100,2); //Discountpercent on total vatamount
            $this->newRows[] = $orderRow;
        }
    }
}