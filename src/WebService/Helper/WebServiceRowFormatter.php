<?php
namespace Svea\WebService;

/**
 * Helper class for formatting orderrows in the right format for WebService soap-calls
 *
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea WebPay
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
    private $priceIncludingVat = false;
    private $resendOrderVat;                // used from admin service functions, when original request got a 50036 error

    /**
     * @param type $order
     */
    public function __construct($order,$priceIncludingVat = NULL) {
        $this->order = $order;
        $this->resendOrderVat = $priceIncludingVat;

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
        return  ($amountIncVat - $amountIncVat * $reverseVatPercent);
    }

    /**
     * Converts an amount excluding vat to amount including vat, given a vat rate in percent.
     *
     * @param float $amountIncVat
     * @param isNumeric $vatPercent
     * @return float amountExVat
     */
    public static function convertExVatToIncVat( $amountExVat, $vatPercent) {
        return  ($amountExVat * (1+$vatPercent/100));
    }

    /**
     * Helper function, calculates vat percentage as int from prices with and without vat.
     * Note: this function will drop any vat rate fractions, i.e. it only handles vat rates that can be expressed as integers.
     */
    public static function calculateVatPercentFromPriceExVatAndPriceIncVat( $incVat, $exVat ) {
        if( $exVat == 0.0 || $incVat == 0.0 ) // avoid -100% vat on i.e. free products or fees
            return 0;
        else
            return \Svea\Helper::bround( (($incVat/$exVat) -1) *100);
    }

    public function formatRows() {
        $this->newRows = array();

        $this->calculateTotals();

        if($this->resendOrderVat === NULL){
             $this->determineVatFlag();
        }  else {
            $this->priceIncludingVat = $this->resendOrderVat ? FALSE : TRUE;
        }

        foreach ($this->order->rows as $row) {
            switch (get_class($row)) {
                case 'Svea\OrderRow':
                    $this->formatOrderRows($row);
                    break;
                case 'Svea\ShippingFee':
                    $this->formatShippingFeeRows($row);
                    break;
                case 'Svea\InvoiceFee':
                    $this->formatInvoiceFeeRows($row);
                    break;
                case 'Svea\FixedDiscount':
                    $this->formatFixedDiscountRows($row);
                    break;
                case 'Svea\RelativeDiscount':
                    $this->formatRelativeDiscountRows($row);
                    break;
                default:
                    break;
            }
        }


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

    private function formatOrderRows($row) {
//        foreach ($this->order->orderRows as $row) {

            $orderRow = new WebServiceSoap\SveaOrderRow();

            if (isset($row->articleNumber)) {
                $orderRow->ArticleNumber = $row->articleNumber;
            }

            $orderRow->Description = $this->formatRowNameAndDescription( $row );

            if (isset($row->unit)) {
                $orderRow->Unit = $row->unit;
            }
            $orderRow->DiscountPercent = (isset($row->discountPercent) ? $row->discountPercent : 0);
            $orderRow->NumberOfUnits = $row->quantity;

            // amountExVat & vatPercent used to specify product price
            if (isset($row->vatPercent) && isset($row->amountExVat)) {
                $orderRow->PricePerUnit = $this->priceIncludingVat ? WebServiceRowFormatter::convertExVatToIncVat($row->amountExVat, \Svea\Helper::bround($row->vatPercent)) : $row->amountExVat;
                $orderRow->VatPercent = \Svea\Helper::bround($row->vatPercent);
                $orderRow->PriceIncludingVat = $this->priceIncludingVat ? TRUE : FALSE;
            }
            // amountIncVat & vatPercent used to specify product price
            elseif (isset($row->vatPercent) && isset($row->amountIncVat)) {
                $orderRow->PricePerUnit = $this->priceIncludingVat ? $row->amountIncVat : WebServiceRowFormatter::convertIncVatToExVat($row->amountIncVat, \Svea\Helper::bround($row->vatPercent));
                $orderRow->VatPercent = \Svea\Helper::bround($row->vatPercent);
                $orderRow->PriceIncludingVat = $this->priceIncludingVat ? TRUE : FALSE;
            }
            // no vatPercent given
            else {
                $orderRow->PricePerUnit = $this->priceIncludingVat ? $row->amountIncVat : $row->amountExVat;
                $orderRow->VatPercent = $this->calculateVatPercentFromPriceExVatAndPriceIncVat( $row->amountIncVat, $row->amountExVat );
                $orderRow->PriceIncludingVat = $this->priceIncludingVat ? TRUE : FALSE;
            }

            $this->newRows[] = $orderRow;
//        }
    }

    /**
     * As the Europe Web Service API only has a description field, join name and description, if both are given.
     *
     * @param OrderRow|ShippingFee|et al. $webPayItemRow  an instance of the order row classes from WebPayItem
     * @return string  the combined description string that should be written to Description
     */
    public function formatRowNameAndDescription( $webPayItemRow ) {

        $description = ""; //fallback to empty string if we haven't got either of name or description

        // if both name and description are set in the package orderrow, add both to the request row description field
        if( isset($webPayItemRow->name) && isset($webPayItemRow->description) ) {
            $description = $webPayItemRow->name.': '.$webPayItemRow->description;
        }
        // else, use either description or name, if set
        else {
            if( isset($webPayItemRow->description) ) {
                $description = $webPayItemRow->description;
            }
            if( isset($webPayItemRow->name) ) {
                $description = $webPayItemRow->name;
            }
        }

        return $description;
    }

    private function formatShippingFeeRows($row) {

            $orderRow = new WebServiceSoap\SveaOrderRow();

            if (isset($row->shippingId)) {
                $orderRow->ArticleNumber = $row->shippingId;
            }

            $orderRow->Description = $this->formatRowNameAndDescription( $row );

            if (isset($row->unit)) {
                $orderRow->Unit = $row->unit;
            }
            $orderRow->DiscountPercent = (isset($row->discountPercent) ? $row->discountPercent : 0);
            $orderRow->NumberOfUnits = 1; //only one fee per row

            // amountExVat & vatPercent used to specify product price
            if (isset($row->vatPercent) && isset($row->amountExVat)) {
                $orderRow->PricePerUnit = $this->priceIncludingVat ? WebServiceRowFormatter::convertExVatToIncVat($row->amountExVat, \Svea\Helper::bround($row->vatPercent)) : $row->amountExVat;
                $orderRow->VatPercent = \Svea\Helper::bround($row->vatPercent);
                $orderRow->PriceIncludingVat = $this->priceIncludingVat ? TRUE : FALSE;
            }
            // amountIncVat & vatPercent used to specify product price
            elseif (isset($row->vatPercent) && isset($row->amountIncVat)) {
//                $orderRow->PricePerUnit =
//                        WebServiceRowFormatter::convertIncVatToExVat( $row->amountIncVat, $row->vatPercent );
                $orderRow->PricePerUnit =  $this->priceIncludingVat ? $row->amountIncVat : WebServiceRowFormatter::convertIncVatToExVat($row->amountIncVat, \Svea\Helper::bround($row->vatPercent));
                $orderRow->VatPercent = \Svea\Helper::bround($row->vatPercent);
                $orderRow->PriceIncludingVat = $this->priceIncludingVat ? TRUE : FALSE;
            }
            // no vatPercent given, booth ExVat and IncVat
            elseif(isset($row->amountExVat) && isset($row->amountIncVat)) {
                $orderRow->PricePerUnit =  $this->priceIncludingVat ? $row->amountIncVat : $row->amountExVat;
                $orderRow->VatPercent = $this->calculateVatPercentFromPriceExVatAndPriceIncVat( $row->amountIncVat, $row->amountExVat );
                $orderRow->PriceIncludingVat = $this->priceIncludingVat ? TRUE : FALSE;
            }
            // no vatPercent given
            else {
                $orderRow->PricePerUnit =  $this->priceIncludingVat ? $row->amountIncVat : WebServiceRowFormatter::convertIncVatToExVat($row->amountIncVat, \Svea\Helper::bround($row->vatPercent));
                $orderRow->VatPercent = $this->calculateVatPercentFromPriceExVatAndPriceIncVat( $row->amountIncVat, $row->amountExVat );
                $orderRow->PriceIncludingVat = $this->priceIncludingVat ? TRUE : FALSE;
            }
            $this->newRows[] = $orderRow;
    }

    private function formatInvoiceFeeRows($row) {
            $orderRow = new WebServiceSoap\SveaOrderRow();

            $orderRow->ArticleNumber = "";

            $orderRow->Description = $this->formatRowNameAndDescription( $row );

            if (isset($row->unit)) {
                $orderRow->Unit = $row->unit;
            }
            $orderRow->DiscountPercent = isset($row->discountPercent) ? $row->discountPercent : 0;
            $orderRow->NumberOfUnits = 1; //only one fee per row

            // amountExVat & vatPercent used to specify product price
            if (isset($row->vatPercent) && isset($row->amountExVat)) {
                $orderRow->PricePerUnit = $this->priceIncludingVat ? WebServiceRowFormatter::convertExVatToIncVat($row->amountExVat, \Svea\Helper::bround($row->vatPercent)) : $row->amountExVat;
                $orderRow->VatPercent = \Svea\Helper::bround($row->vatPercent);
                $orderRow->PriceIncludingVat = $this->priceIncludingVat ? TRUE : FALSE;
            }
            // amountIncVat & vatPercent used to specify product price
            elseif (isset($row->vatPercent) && isset($row->amountIncVat)) {
//                $orderRow->PricePerUnit =
//                        WebServiceRowFormatter::convertIncVatToExVat( $row->amountIncVat, $row->vatPercent );
                $orderRow->PricePerUnit =  $this->priceIncludingVat ? $row->amountIncVat : WebServiceRowFormatter::convertIncVatToExVat($row->amountIncVat, \Svea\Helper::bround($row->vatPercent));
                $orderRow->VatPercent = \Svea\Helper::bround($row->vatPercent);
                $orderRow->PriceIncludingVat = $this->priceIncludingVat ? TRUE : FALSE;
            }
            // no vatPercent given, booth ExVat and IncVat
            elseif (isset($row->amountExVat) && isset($row->amountIncVat)) {
                $orderRow->PricePerUnit =  $this->priceIncludingVat ? $row->amountIncVat : $row->amountExVat;
                $orderRow->VatPercent = $this->calculateVatPercentFromPriceExVatAndPriceIncVat( $row->amountIncVat, $row->amountExVat );
                $orderRow->PriceIncludingVat = $this->priceIncludingVat ? TRUE : FALSE;
            }
            else {
                $orderRow->PricePerUnit =  $this->priceIncludingVat ? $row->amountIncVat : WebServiceRowFormatter::convertIncVatToExVat($row->amountIncVat, \Svea\Helper::bround($row->vatPercent));
                $orderRow->VatPercent = $this->calculateVatPercentFromPriceExVatAndPriceIncVat( $row->amountIncVat, $row->amountExVat );
                $orderRow->PriceIncludingVat = $this->priceIncludingVat ? TRUE : FALSE;
            }
            $this->newRows[] = $orderRow;
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

            $orderRow = new WebServiceSoap\SveaOrderRow();

            if (isset($discountRow->discountId)) {
                $orderRow->ArticleNumber = $discountRow->discountId;
            }

            $orderRow->Description = $this->formatRowNameAndDescription( $discountRow );

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
            $orderRow->PricePerUnit = (-1) * ($this->priceIncludingVat ? $discountAtThisVatRateIncVat : WebServiceRowFormatter::convertIncVatToExVat($discountAtThisVatRateIncVat, $vatRate));
;
            $orderRow->VatPercent = $vatRate;
            $orderRow->PriceIncludingVat = $this->priceIncludingVat ? TRUE : FALSE;
            $splitRows[] = $orderRow;
        }

        return $splitRows;
    }

    /**
     * Formats FixedDiscount rows specified with setAmountExVat() only.
     * Returns one or more discount rows, one for each vat rate present in the order.
     * If the
     *
     * @param FixedDiscount $discountRow
     * @return \Svea\SveaOrderRow
     */
    private function formatFixedDiscountSpecifiedAsAmountExVatOnly( $discountRow ) {
        $splitRows = array(); // one (or more) formated discount rows, split across the vat rates in the order

        foreach( $this->totalAmountPerVatRateExVat as $vatRate => $amountAtThisVatRateExVat ) {

            $orderRow = new WebServiceSoap\SveaOrderRow();

            if (isset($discountRow->discountId)) {
                $orderRow->ArticleNumber = $discountRow->discountId;
            }

            $orderRow->Description = $this->formatRowNameAndDescription( $discountRow );

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

            // iff priceIncludingVat set to true, write discount row as incvat
            if( $this->priceIncludingVat ) {
                $orderRow->PricePerUnit = (-1) * WebServiceRowFormatter::convertExVatToIncVat( $discountAtThisVatRateExVat, $vatRate );
                $orderRow->VatPercent = $vatRate;
                $orderRow->PriceIncludingVat = TRUE;
            }
            else {
                $orderRow->PricePerUnit = (-1) * $discountAtThisVatRateExVat;
                $orderRow->VatPercent = $vatRate;
                $orderRow->PriceIncludingVat = FALSE;
            }

            $splitRows[] = $orderRow;
        }

        return $splitRows;
    }

    private function formatFixedDiscountRows($row) {
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

                    $orderRow = new WebServiceSoap\SveaOrderRow();

                    if (isset($row->discountId)) {
                        $orderRow->ArticleNumber = $row->discountId;
                    }

                    $orderRow->Description = $this->formatRowNameAndDescription( $row );

                    if (isset($row->unit)) {
                        $orderRow->Unit = $row->unit;
                    }
                    $orderRow->DiscountPercent = 0; //no discount on discount
                    $orderRow->NumberOfUnits = 1; //only one discount per row

                    //calculate discount
                    $vatRate = $row->vatPercent;
                    $discountAtThisVatRateIncVat = $row->amount;
//                    $discountAtThisVatRateExVat =
//                            WebServiceRowFormatter::convertIncVatToExVat( $discountAtThisVatRateIncVat, $vatRate );

                    $orderRow->PricePerUnit = (-1) * ($this->priceIncludingVat ? $discountAtThisVatRateIncVat : WebServiceRowFormatter::convertIncVatToExVat($row->amount, \Svea\Helper::bround($row->vatPercent)));
                    $orderRow->VatPercent = $vatRate;
                    $orderRow->PriceIncludingVat = $this->priceIncludingVat ? TRUE : FALSE;

                    $this->newRows[] = $orderRow;
            }

            // amountExVat (i.e. amount) and vatPercent is set, so we use that vatPercent:
            if( !isset($row->amount) && isset($row->vatPercent) && isset($row->amountExVat) ) {

                    $orderRow = new WebServiceSoap\SveaOrderRow();

                    if (isset($row->discountId)) {
                        $orderRow->ArticleNumber = $row->discountId;
                    }

                    $orderRow->Description = $this->formatRowNameAndDescription( $row );

                    if (isset($row->unit)) {
                        $orderRow->Unit = $row->unit;
                    }
                    $orderRow->DiscountPercent = 0; //no discount on discount
                    $orderRow->NumberOfUnits = 1; //only one discount per row

                    //calculate discount
                    $vatRate = $row->vatPercent;
                    $discountAtThisVatRate = $this->priceIncludingVat ? WebServiceRowFormatter::convertExVatToIncVat($row->amountExVat, \Svea\Helper::bround($row->vatPercent)) : $row->amountExVat;

                    $orderRow->PricePerUnit = (-1) * $discountAtThisVatRate;
                    $orderRow->VatPercent = $vatRate;
                     $orderRow->PriceIncludingVat = $this->priceIncludingVat ? TRUE : FALSE;

                    $this->newRows[] = $orderRow;
            }
    }

    private function formatRelativeDiscountRows($row) {
            foreach( $this->totalAmountPerVatRateIncVat as $vatRate => $amountAtThisVatRateIncVat ) {
                $orderRow = new WebServiceSoap\SveaOrderRow();

                if (isset($row->discountId)) {
                    $orderRow->ArticleNumber = $row->discountId;
                }

                $orderRow->Description = $this->formatRowNameAndDescription( $row );

                if( sizeof($this->totalAmountPerVatRateIncVat)>1 ) {  // add tax rate for split discount to description
                    $orderRow->Description .= " (".$vatRate."%)";
                }
                if (isset($row->unit)) {
                    $orderRow->Unit = $row->unit;
                }

                $amountAtThisVatRateExVat = $amountAtThisVatRateIncVat - $amountAtThisVatRateIncVat * (1-(1/(1+$vatRate/100)));   // calculate "reverse vat", i.e. 25% => 20%

                $discountIncVat = $amountAtThisVatRateIncVat * ($row->discountPercent * 0.01);
                $discountExVat = $amountAtThisVatRateExVat * ($row->discountPercent * 0.01);
                $orderRow->DiscountPercent = 0; //no discount on discount
                $orderRow->NumberOfUnits = 1; //only one discount per row
                $orderRow->PricePerUnit = $this->priceIncludingVat ? - number_format($discountIncVat,5,'.','') : - number_format($discountExVat,5,'.',''); //Discountpercent on total price inc vat.
                $orderRow->VatPercent = $vatRate;
                $orderRow->PriceIncludingVat = $this->priceIncludingVat ? TRUE : FALSE;

                $this->newRows[] = $orderRow;
            }
    }

    private function determineVatFlag() {
        $exVat = 0;
        $incVat = 0;
        foreach ($this->order->rows as $row) {
            switch (get_class($row)) {
                 //relative discount
        // ignored, as relative discount doesn't use setAmountExVat/-IncVat at all
                case 'Svea\RelativeDiscount':
                    break;
                case 'Svea\FixedDiscount':
                    if(isset($row->amountExVat) && isset($row->amountIncVat)){
                        $incVat++;
                    } if (isset($row->amountExVat) && !isset($row->amountIncVat)) {
                        $exVat++;
                    }else {
                        $incVat++;
                    }
                    break;
                default:
                    if(isset($row->amountExVat) && isset($row->amountIncVat)){
                        $incVat++;
                    }elseif (isset($row->amountExVat) && isset ($row->vatPercent)) {
                        $exVat++;
                    }else {
                        $incVat++;
                    }
                    break;
            }

        }
        //if at least one of the non-discount rows are defined exvat, need to use set priceIncludingVat to false
        if ($exVat >= 1) {
            $this->priceIncludingVat = FALSE;
        }  else {
            $this->priceIncludingVat = TRUE;
        }

    }

}
