<?php
namespace Svea;

/**
 *
 */
class HostedRowFormatter {

    private $totalAmount;       // order item rows, rounded to 2 decimals, multiplied by 100 to integer
    private $totalVat;          // order item rows, rounded to 2 decimals, multiplied by 100 to integer
    private $newRows;           // all order rows, as above
    private $rawAmount;         // unrounded, multiplied by 100, avoids cumulative rounding error (when summing up over rows)
    private $rawVat;            // unrounded, multiplied by 100, avoids cumulative rounding error (when summing up over rows)
           
    private $shippingAmount;    
    private $shippingVat;            
  
    private $discountAmount;         
    private $discountVat;      
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

        return $this->newRows;  // TODO return self instead => chain functions instead of passing rows to formatTotalX() below
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
            
            //print_r($this->totalAmount); echo " "; print_r($this->rawAmount);
            
        }
    }

    private function formatShippingFeeRows($order) {
        if (!isset($order->shippingFeeRows)) {
            return;
        }

        foreach ($order->shippingFeeRows as $row) {
            $tempRow = new HostedOrderRowBuilder();

            if (isset($row->articleNumber)) {
                $tempRow->setSku($row->articleNumber);
            }

            if (isset($row->name)) {
                $tempRow->setName($row->name);
            }

            if (isset($row->description)) {
                $tempRow->setDescription($row->description);
            }

            $rawAmount = 0.0;
            $rawVat = 0.0;
            // calculate amount, vat from two out of three given by customer, see unit tests in HostedRowFormater
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

            if (isset($row->shippingId)) {
                $tempRow->setSku($row->shippingId);
            }

            $tempRow->setQuantity(1);            
            $this->newRows[] = $tempRow;           
            $this->shippingAmount += ($tempRow->amount );
            $this->shippingVat +=  ($tempRow->vat );            

        }
    }

    public function formatFixedDiscountRows($order) {
        if (!isset($order->fixedDiscountRows)) {
            return;
        }

        foreach ($order->fixedDiscountRows as $row) {
            $tempRow = new HostedOrderRowBuilder();

            if (isset($row->name)) {
                $tempRow->setName($row->name);
            }

            if (isset($row->description)) {
                $tempRow->setDescription($row->description);
            }

            // switch on which were used of setAmountIncVat ($this->amount), setAmountExVat (->amountExVat), setVatPercent (->vatPercent)
            $rawAmount = 0.0;
            $rawVat = 0.0;            
            // use old method of calculating discounts from single amount inc. vat
            if (isset($row->amount) && !isset($row->amountExVat) && !isset($row->vatPercent)) {
                $discountInPercent = ($row->amount * 100) / $this->totalAmount;   // discount as fraction of total order sum
                
                $rawAmount = $row->amount;
                $rawVat = $this->totalVat * $discountInPercent;
                $tempRow->setAmount( - round($rawAmount,2)*100 );
                $tempRow->setVat( - round($rawVat,2) );             // calculated from multiplied amount, so no *100

            }
            // calculate amount, vat from two out of three given by customer, see unit tests in HostedPaymentTest
            elseif (isset($row->amountExVat) && isset($row->vatPercent)) {
                $rawAmount = $row->amountExVat *($row->vatPercent/100+1);
                $rawVat = $row->amountExVat *($row->vatPercent/100);
                $tempRow->setAmount( - round($rawAmount,2) *100 );
                $tempRow->setVat( - round($rawVat,2) *100 );
                
            } elseif (isset($row->amount) && isset($row->vatPercent)) {
                $rawAmount = $row->amount;
                $rawVat = $row->amount - ($row->amount/($row->vatPercent/100+1));
                $tempRow->setAmount( - round($rawAmount,2) *100 );
                $tempRow->setVat( - round($rawVat,2) *100 );
             
            } else {
                $rawAmount = $row->amount;
                $rawVat = ( $row->amount - $row->amountExVat);
                $tempRow->setAmount( - round($rawAmount,2)*100 );
                $tempRow->setVat( - round($rawVat,2) *100);
            }

            if (isset($row->unit)) {
                $tempRow->setUnit($row->unit);
            }

            if (isset($row->discountId)) {
                $tempRow->setSku($row->discountId);
            }

            $tempRow->setQuantity(1);
            
            $this->totalAmount += $tempRow->amount;
            $this->totalVat += $tempRow->vat;
            $this->newRows[] = $tempRow;
            
            $this->discountAmount += $tempRow->amount;
            $this->discountVat +=  $tempRow->vat;                    
        }
    }

    public function formatRelativeDiscountRows($order) {
        if (!isset($order->relativeDiscountRows)) {
            return;
        }

        foreach ($order->relativeDiscountRows as $row) {
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

            $tempRow->setAmount( - round((($row->discountPercent *0.01) * $this->rawAmount) ,2) );    
            $tempRow->setVat( - round(($row->discountPercent *0.01) * $this->rawVat ,2 ) );

            $tempRow->setQuantity(1);
            
            $this->totalAmount -= $tempRow->amount;
            $this->totalVat -= abs($tempRow->vat);
            $this->newRows[] = $tempRow;
            
            $this->discountAmount += $tempRow->amount;
            $this->discountVat +=  $tempRow->vat;                       
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

        //return $result;
        return $this->rawAmount + $this->shippingAmount + $this->discountAmount;
    }

    /**
    * used by HostedPayment calculateRequestValues to get sum to charge card/directbank
    */
    public function formatTotalVat($rows) {
        $result = 0;

        foreach ($rows as $row) {
            $result += $row->vat * $row->quantity;
        }

        //return $result;
        return $this->rawVat + $this->shippingVat + $this->discountVat;

    }
}
