<?php
namespace Svea;

/**
 *
 */
class HostedRowFormatter {

    private $totalAmount;       // multiplied by 100, rounded to integer
    private $totalVat;          // multiplied by 100, rounded to integer
    private $newRows;           // all order rows, as above
    private $rawAmount;       // multiplied by 100, rounded to integer
    private $rawVat;          // multiplied by 100, rounded to integer    
           
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

    /**
     * formatOrderRows goes through the orderBuilder object order-, shipping & discount rows
     * and translates them to a format suitable for use by the HostedXmlBuilder.
     * 
     * This includes translating all prices to integer, multiplying by 100 to remove fractions.
     * Svea employs Bankers rounding, also known as "half-to-even rounding". 
     * 
     * We also calculate a total amount including taxes, and the total tax amount, for the order.
     * When calculating the amounts, all rounding takes place last, in order to avoid cumulative
     * rounding errors. (See HostedPaymentTest for an example.)
     * 
     * TODO implement bankers rounding
     */
    private function formatOrderRows($order) {
        foreach ($order->orderRows as $row ) {
            $tempRow = new HostedOrderRowBuilder();     // new empty object
           
            if (isset($row->name)) {
                $tempRow->setName($row->name);
            }

            if (isset($row->description)) {
                $tempRow->setDescription($row->description);
            }

            $rawAmount = 0.0;
            $rawVat = 0.0;
            // calculate amount, vat from two out of three given by customer, see unit tests HostedRowFormater
            if (isset($row->amountExVat) && isset($row->vatPercent)) {
                $rawAmount = floatval($row->amountExVat) *($row->vatPercent/100+1);
                $rawVat = floatval($row->amountExVat) *($row->vatPercent/100);
                $tempRow->setAmount( round($rawAmount,2) *100 );
                $tempRow->setVat( round($rawVat,2) *100 );
                
            } elseif (isset($row->amountIncVat) && isset($row->vatPercent)) {
                $rawAmount = $row->amountIncVat;
                $rawVat = $row->amountIncVat - ($row->amountIncVat/($row->vatPercent/100+1));
                $tempRow->setAmount( round($rawAmount,2) *100 );
                $tempRow->setVat( round($rawVat,2) *100 );
             
            } else {
                $rawAmount = $row->amountIncVat;
                $rawVat = ($row->amountIncVat - $row->amountExVat);
                $tempRow->setAmount( round($rawAmount,2)*100 );
                $tempRow->setVat( round($rawVat,2) *100);
            }

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
            $this->totalAmount += ($tempRow->amount * $row->quantity);
            $this->totalVat +=  ($tempRow->vat * $row->quantity);            
            $this->rawAmount += round( ($rawAmount * $row->quantity) ,2) *100;
            $this->rawVat +=  round( ($rawVat * $row->quantity) ,2) *100;
            
            print_r($this->totalAmount); echo " "; print_r($this->rawAmount);
            
        }
    }

    private function formatShippingFeeRows($order) {
        if (!isset($order->shippingFeeRows)) {
            return;
        }

        foreach ($order->shippingFeeRows as $row) {
            $tempRow = new HostedOrderRowBuilder();
            $plusVatCounter = isset($row->vatPercent) ? ($row->vatPercent * 0.01) + 1 : 0.0;

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
                $tempRow->setAmount(round(($row->amountExVat * 100) * $plusVatCounter));
                $tempRow->setVat(round($tempRow->amount - ($row->amountExVat * 100)));
            } elseif (isset($row->amountIncVat) && isset($row->vatPercent)) {
                 $tempRow->setAmount(round($row->amountIncVat * 100));
                 $tempRow->setVat(round($tempRow->amount - ($tempRow->amount / $plusVatCounter)));
            } else {
                 $tempRow->setAmount(round($row->amountIncVat * 100));
                 $tempRow->setVat(($row->amountIncVat - $row->amountExVat) * 100);
            }

            if (isset($row->unit)) {
                $tempRow->setUnit($row->unit);
            }

            if (isset($row->shippingId)) {
                $tempRow->setSku($row->shippingId);
            }

            $tempRow->setQuantity(1);
            $this->newRows[] = $tempRow;
        }
    }

    //check!
    public function formatFixedDiscountRows($order) {
        if (!isset($order->fixedDiscountRows)) {
            return;
        }

        foreach ($order->fixedDiscountRows as $row) {
            $discountInPercent = ($row->amount * 100)/ $this->totalAmount;
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
                $vat = $this->totalVat * $discountInPercent;
                $tempRow->setVat(-round($vat));

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
            $discountCounter = $row->discountPercent * 0.01; //e.g. 0.20
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

            $tempRow->setAmount(- round(($discountCounter * $this->totalAmount)));

            // Vat could be 0
            // if ($this->totalVat > 0) {
                $tempRow->setVat(- round(($this->totalVat * $discountCounter)));
            // }

            $tempRow->setQuantity(1);
            $this->totalAmount -= $tempRow->amount;
            $this->totalVat -= abs($tempRow->vat);
            $this->newRows[] = $tempRow;
        }
    }

    
   /**
    * used by HostedPayment calculateRequestValues to get sum to charge card/directbank
    */
    public function formatTotalAmount($rows) {
        $result = 0;

        foreach ($rows as $row) {
            $result += $row->amount * $row->quantity;
        }

        return $result;
    }

    /**
    * used by HostedPayment calculateRequestValues to get sum to charge card/directbank
    */
    public function formatTotalVat($rows) {
        $result = 0;

        foreach ($rows as $row) {
            $result += $row->vat * $row->quantity;
        }

        return $result;
    }
}
