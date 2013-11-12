<?php
namespace Svea;

/**
 * Helper class for formatting orderrows in the right format for WebService soap-calls
 *
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea Webpay
 * @package WebServiceRequests/Helper
 */
class WebServiceRowFormatter {

    private $order;
    private $totalAmountExVat;      // summed in calculateTotals
    private $totalVatAsAmount;      // summed in calculateTotals
    private $totalAmountIncVat;     // derived from ExVat + IncVat
    private $totalVatAsPercent;     // derived from ExVat + IncVat

    private $totalAmountPerVatRateIncVat;   // summed in calculateTotals, used to calculate "mean vat" split into given vat rates
    private $totalAmountPerVatRateExVat;    // summed in calculateTotals, used to calculate "mean vat" split into given vat rates

    private $newRows;

    /**
     * @param type $order
     */
    public function __construct($order) {
        $this->order = $order;

        $this->totalAmountPerVatRateIncVat = array();        
        $this->totalAmountPerVatRateExVat = array();  
    }

    /**
     * Converts an amount including vat to amount excluding vat, given a vat rate in percent.
     * 
     * @param float $amountIncVat
     * @param isNumeric $vatPercent
     * @return float amountExVat
     */
    public static function convertIncVatToExVat( $amountIncVat, $vatPercent) {
        $reverseVatPercent = (1-(1/(1+$vatPercent/100))); // calculate "reverse vat", i.e. 25% => 20%
        return Helper::bround( ($amountIncVat - $amountIncVat * $reverseVatPercent) ,2);
    }
    
    /**
     * Converts an amount excluding vat to amount including vat, given a vat rate in percent.
     * 
     * @param float $amountIncVat
     * @param isNumeric $vatPercent
     * @return float amountExVat
     */
    public static function convertExVatToIncVat( $amountExVat, $vatPercent) {
        return Helper::bround( ($amountExVat * (1+$vatPercent/100)) ,2);
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

    // used to create/increase the totalAmountPerVatRateIncVat and totalAmountPerVatRateExVat arrays in calculateTotals();  
    private function increaseCumulativeVatRateAmounts( &$array, $key, $value ) {
        if( isset($array[$key]) ) {
            $array[$key] += $value;                 
        } else {
            $array[$key] = $value;   
        } 
    }
    
    private function calculateTotals() {
        $this->totalAmountExVat = 0;
        $this->totalVatAsAmount = 0;

        foreach ($this->order->orderRows as $product) {

            // amountExVat & vatPercent used to specify product price
            if (isset($product->vatPercent) && isset($product->amountExVat)) {
                $this->totalAmountExVat += $product->amountExVat * $product->quantity;
                $this->totalVatAsAmount += ($product->vatPercent/100 * $product->amountExVat) * $product->quantity;

                $this->increaseCumulativeVatRateAmounts($this->totalAmountPerVatRateIncVat, $product->vatPercent, 
                        WebServiceRowFormatter::convertExVatToIncVat($product->amountExVat,$product->vatPercent) * $product->quantity );
                $this->increaseCumulativeVatRateAmounts($this->totalAmountPerVatRateExVat, $product->vatPercent, 
                        $product->amountExVat * $product->quantity );               
            } 
            // amountIncVat & vatPercent used to specify product price
            elseif (isset($product->vatPercent) && isset($product->amountIncVat)) {
                $amountExVat = WebServiceRowFormatter::convertIncVatToExVat($product->amountIncVat,$product->vatPercent);
                $this->totalAmountExVat += $amountExVat * $product->quantity;
                $this->totalVatAsAmount += ($product->vatPercent/100 * $amountExVat) * $product->quantity;

                $this->increaseCumulativeVatRateAmounts($this->totalAmountPerVatRateIncVat, $product->vatPercent, 
                        $product->amountIncVat * $product->quantity );
                $this->increaseCumulativeVatRateAmounts($this->totalAmountPerVatRateExVat, $product->vatPercent, 
                        WebServiceRowFormatter::convertIncVatToExVat($product->amountIncVat,$product->vatPercent) * $product->quantity );                
            } 
            // no vatPercent given
            else {
                $this->totalAmountExVat += $product->amountExVat * $product->quantity;
                $this->totalVatAsAmount += ($product->amountIncVat - $product->amountExVat) * $product->quantity;

                $vatRate = $this->calculateVatPercentFromPriceExVatAndPriceIncVat($product->amountIncVat, $product->amountExVat);
                
                $this->increaseCumulativeVatRateAmounts($this->totalAmountPerVatRateIncVat, $vatRate, 
                        $product->amountIncVat * $product->quantity );
                $this->increaseCumulativeVatRateAmounts($this->totalAmountPerVatRateExVat, $vatRate, 
                        $product->amountExVat * $product->quantity );      
            }
        }
        $this->totalAmountIncVat = $this->totalAmountExVat + $this->totalVatAsAmount;
        $this->totalAmountExVat = $this->totalAmountIncVat - $this->totalVatAsAmount;
        if ($this->totalAmountExVat > 0) {
            $this->totalVatAsPercent = $this->totalVatAsAmount / $this->totalAmountIncVat; //e.g. 0,20 if percentage 20
        }
    }

    /**
     * Helper function, calculates vat percentage as int from prices with and without vat.
     */
    private function calculateVatPercentFromPriceExVatAndPriceIncVat( $incVat, $exVat ) {
        if( $exVat == 0.0 || $incVat == 0.0 ) // avoid -100% vat on i.e. free products or fees
            return 0;
        else
            return round( (($incVat/$exVat) -1) *100);
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
            
            // amountExVat & vatPercent used to specify product price
            if (isset($row->vatPercent) && isset($row->amountExVat)) {
                $orderRow->PricePerUnit = $row->amountExVat;
                $orderRow->VatPercent = round($row->vatPercent);
            } 
            // amountIncVat & vatPercent used to specify product price
            elseif (isset($row->vatPercent) && isset($row->amountIncVat)) {
                $orderRow->PricePerUnit = 
                         WebServiceRowFormatter::convertIncVatToExVat( $row->amountIncVat, $row->vatPercent );
                $orderRow->VatPercent = round($row->vatPercent);
            }
            // no vatPercent given
            else {
                $orderRow->PricePerUnit = Helper::bround($row->amountExVat,2);
                $orderRow->VatPercent = $this->calculateVatPercentFromPriceExVatAndPriceIncVat( $row->amountIncVat, $row->amountExVat );
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
            
            // amountExVat & vatPercent used to specify product price
            if (isset($row->vatPercent) && isset($row->amountExVat)) {
                $orderRow->PricePerUnit = $row->amountExVat;
                $orderRow->VatPercent = round($row->vatPercent);
            }
            // amountIncVat & vatPercent used to specify product price
            elseif (isset($row->vatPercent) && isset($row->amountIncVat)) {
                $orderRow->PricePerUnit = 
                        WebServiceRowFormatter::convertIncVatToExVat( $row->amountIncVat, $row->vatPercent );
                $orderRow->VatPercent = round($row->vatPercent);
            }
            // no vatPercent given
            else {
                $orderRow->PricePerUnit = Helper::bround($row->amountExVat,2);
                $orderRow->VatPercent = $this->calculateVatPercentFromPriceExVatAndPriceIncVat( $row->amountIncVat, $row->amountExVat );
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
            
            // amountExVat & vatPercent used to specify product price
            if (isset($row->vatPercent) && isset($row->amountExVat)) {
                $orderRow->PricePerUnit = $row->amountExVat;
                $orderRow->VatPercent = round($row->vatPercent);
            } 
            // amountIncVat & vatPercent used to specify product price
            elseif (isset($row->vatPercent) && isset($row->amountIncVat)) {
                $orderRow->PricePerUnit = 
                        WebServiceRowFormatter::convertIncVatToExVat( $row->amountIncVat, $row->vatPercent );
                $orderRow->VatPercent = round($row->vatPercent);
            }
            // no vatPercent given
            else {
                $orderRow->PricePerUnit = Helper::bround($row->amountExVat,2);
                $orderRow->VatPercent = $this->calculateVatPercentFromPriceExVatAndPriceIncVat( $row->amountIncVat, $row->amountExVat );

            }
            $this->newRows[] = $orderRow;
        }
    }

    /**
     * Formats FixedDiscount rows specified with setAmountIncVat() only.
     * Returns one or more discount rows, one for each vat rate present in the order.
     * 
     * @param FixedDiscount $discountRow
     * @return \Svea\SveaOrderRow
     */
    private function formatFixedDiscountSpecifiedAsAmountIncVatOnly( $discountRow ) {
        
        $splitRows = array(); // one (or more) formated discount rows, split across the vat rates in the order
        
        foreach( $this->totalAmountPerVatRateIncVat as $vatRate => $amountAtThisVatRateIncVat ) {

            $orderRow = new SveaOrderRow();

            if (isset($discountRow->discountId)) {
                $orderRow->ArticleNumber = $discountRow->discountId;
            }
            if (isset($discountRow->description)) {
                $orderRow->Description = (isset($discountRow->name) ? $discountRow->name . ': ' : "") . $discountRow->description;
            } elseif (isset($discountRow->name) && isset($discountRow->description) == false) {
                $orderRow->Description = $discountRow->name;
            }
            if( sizeof($this->totalAmountPerVatRateIncVat)>1 ) {  // add tax rate for split discount to description
                $orderRow->Description .= " (".$vatRate."%)";
            }
            if (isset($discountRow->unit)) {
                $orderRow->Unit = $discountRow->unit;
            }
            $orderRow->DiscountPercent = 0; //no discount on discount
            $orderRow->NumberOfUnits = 1; //only one discount per row

            //calculate discount
            $discountAtThisVatRateIncVat = $discountRow->amount * ($amountAtThisVatRateIncVat / $this->totalAmountIncVat );
            $discountAtThisVatRateExVat = 
                    WebServiceRowFormatter::convertIncVatToExVat( $discountAtThisVatRateIncVat, $vatRate );
            $orderRow->PricePerUnit = -1*Helper::bround($discountAtThisVatRateExVat,2);
            $orderRow->VatPercent = $vatRate;
            $splitRows[] = $orderRow;
        }
        
        return $splitRows;
    }
    
    /**
     * Formats FixedDiscount rows specified with setAmountExVat() only.
     * Returns one or more discount rows, one for each vat rate present in the order.
     * 
     * @param FixedDiscount $discountRow
     * @return \Svea\SveaOrderRow
     */
    private function formatFixedDiscountSpecifiedAsAmountExVatOnly( $discountRow ) {
        
        $splitRows = array(); // one (or more) formated discount rows, split across the vat rates in the order
        
        foreach( $this->totalAmountPerVatRateExVat as $vatRate => $amountAtThisVatRateExVat ) {

            $orderRow = new SveaOrderRow();

            if (isset($discountRow->discountId)) {
                $orderRow->ArticleNumber = $discountRow->discountId;
            }
            if (isset($discountRow->description)) {
                $orderRow->Description = (isset($discountRow->name) ? $discountRow->name . ': ' : "") . $discountRow->description;
            } elseif (isset($discountRow->name) && isset($discountRow->description) == false) {
                $orderRow->Description = $discountRow->name;
            }
            if( sizeof($this->totalAmountPerVatRateExVat)>1 ) {  // add tax rate for split discount to description
                $orderRow->Description .= " (".$vatRate."%)";
            }
            if (isset($discountRow->unit)) {
                $orderRow->Unit = $discountRow->unit;
            }
            $orderRow->DiscountPercent = 0; //no discount on discount
            $orderRow->NumberOfUnits = 1; //only one discount per row

            //calculate discount
            $discountAtThisVatRateExVat = $discountRow->amountExVat * ($amountAtThisVatRateExVat / $this->totalAmountExVat );

            $orderRow->PricePerUnit = -1*Helper::bround($discountAtThisVatRateExVat,2);
            $orderRow->VatPercent = $vatRate;
            $splitRows[] = $orderRow;
        }
        
        return $splitRows;
    }
    
    private function formatFixedDiscountRows() {
        if (!isset($this->order->fixedDiscountRows)) {
            return;
        }
        foreach ($this->order->fixedDiscountRows as $row) {

            // only amountIncVat (i.e. amount) was specified:
            if( isset($row->amount) && !isset($row->vatPercent) && !isset($row->amountExVat) ) {
                $this->newRows = array_merge( $this->newRows, $this->formatFixedDiscountSpecifiedAsAmountIncVatOnly( $row ) );
            }

            // only amountExVat was specified:
            if( !isset($row->amount) && !isset($row->vatPercent) && isset($row->amountExVat) ) {
                $this->newRows = array_merge( $this->newRows, $this->formatFixedDiscountSpecifiedAsAmountExVatOnly( $row ) );
            }
                       
            // amountIncVat (i.e. amount) and vatPercent is set, so we use that vatPercent:
            if( isset($row->amount) && isset($row->vatPercent) && !isset($row->amountExVat) ) {

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

                    //calculate discount
                    $vatRate = $row->vatPercent;
                    $discountAtThisVatRateIncVat = $row->amount;
                    $discountAtThisVatRateExVat = 
                            WebServiceRowFormatter::convertIncVatToExVat( $discountAtThisVatRateIncVat, $vatRate );
                    
                    $orderRow->PricePerUnit = -1*Helper::bround($discountAtThisVatRateExVat,2);
                    $orderRow->VatPercent = $vatRate;

                    $this->newRows[] = $orderRow;
            }
            
            // amountExVat (i.e. amount) and vatPercent is set, so we use that vatPercent:
            if( !isset($row->amount) && isset($row->vatPercent) && isset($row->amountExVat) ) {

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

                    //calculate discount
                    $vatRate = $row->vatPercent;
                    $discountAtThisVatRateExVat = $row->amountExVat;

                    $orderRow->PricePerUnit = -1*Helper::bround($discountAtThisVatRateExVat,2);
                    $orderRow->VatPercent = $vatRate;

                    $this->newRows[] = $orderRow;
            }
        }
    }

    private function formatRelativeDiscountRows() {
        if (!isset($this->order->relativeDiscountRows)) {
            return;
        }

        foreach ($this->order->relativeDiscountRows as $row) {

            foreach( $this->totalAmountPerVatRateIncVat as $vatRate => $amountAtThisVatRateIncVat ) {

                $orderRow = new SveaOrderRow();
                if (isset($row->discountId)) {
                    $orderRow->ArticleNumber = $row->discountId;
                }
                if (isset($row->description)) {
                    $orderRow->Description = (isset($row->name) ? $row->name . ': ' : "") . $row->description;
                } elseif (isset($row->name) && isset($row->description) == false) {
                    $orderRow->Description = $row->name;
                }
                if( sizeof($this->totalAmountPerVatRateIncVat)>1 ) {  // add tax rate for split discount to description
                    $orderRow->Description .= " (".$vatRate."%)";
                }
                if (isset($row->unit)) {
                    $orderRow->Unit = $row->unit;
                }

                $amountAtThisVatRateExVat = $amountAtThisVatRateIncVat - $amountAtThisVatRateIncVat * (1-(1/(1+$vatRate/100)));   // calculate "reverse vat", i.e. 25% => 20%

                $discountExVat = round($amountAtThisVatRateExVat * ($row->discountPercent * 0.01), 2);
                $orderRow->DiscountPercent = 0; //no discount on discount
                $orderRow->NumberOfUnits = 1; //only one discount per row
                $orderRow->PricePerUnit = - number_format($discountExVat,2,'.',''); //Discountpercent on total price ex vat.
                $orderRow->VatPercent = $vatRate;

                $this->newRows[] = $orderRow;
            }
        }
    }
}
