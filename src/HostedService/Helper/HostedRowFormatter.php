<?php
namespace Svea\HostedService;

class HostedRowFormatter {

    private $totalAmount;       // order item rows, rounded to 2 decimals, multiplied by 100 to integer
    private $totalVat;          // order item rows, rounded to 2 decimals, multiplied by 100 to integer
    private $newRows;           // type HostedOrderRowBuilder -- all order rows, as above
    private $rawAmount;         // unrounded, multiplied by 100, avoids cumulative rounding error (when summing up over rows)
    private $rawVat;            // unrounded, multiplied by 100, avoids cumulative rounding error (when summing up over rows)

    private $shippingAmount;
    private $shippingVat;

    private $invoiceAmount;
    private $invoiceVat;

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
     * Format rows and calculate vat.
     * Includes order InvoiceFee rows, as InvoiceFee may be used for i.e. generic handling fee etc.
     *
     * @param type $rows
     * @return int
     */
    public function formatRows($order) {
         foreach ($order->rows as $row ) {
             switch (get_class($row)) {
                   case 'Svea\OrderRow':
                    $this->formatOrderRows($row);
                    break;
                case 'Svea\ShippingFee':
                     $this->formatShippingFeeRows($row);
                    break;
                case 'Svea\InvoiceFee':
                    $this->formatInvoiceFeeRows($row);    // invoice fee stands in for all kinds of handling fees
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
     */
    private function formatOrderRows($row) {
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

                $tempRow->setAmount( \Svea\Helper::bround($rawAmount,2) *100 );
                $tempRow->setVat( \Svea\Helper::bround($rawVat,2) *100 );

            } elseif (isset($row->amountIncVat) && isset($row->vatPercent)) {
                $rawAmount = $row->amountIncVat;
                $rawVat = $row->amountIncVat - ($row->amountIncVat/($row->vatPercent/100+1));
                $tempRow->setAmount( \Svea\Helper::bround($rawAmount,2) *100 );
                $tempRow->setVat( \Svea\Helper::bround($rawVat,2) *100 );

            } else {
                $rawAmount = $row->amountIncVat;
                $rawVat = ($row->amountIncVat - $row->amountExVat);
                $tempRow->setAmount( \Svea\Helper::bround($rawAmount,2) *100 );
                $tempRow->setVat( \Svea\Helper::bround($rawVat,2) *100);
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
            $this->rawAmount += \Svea\Helper::bround( ($rawAmount * $row->quantity) ,2) *100;
            $this->rawVat +=  \Svea\Helper::bround( ($rawVat * $row->quantity) ,2) *100;
    }

    private function formatShippingFeeRows($row) {
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
                $tempRow->setAmount( \Svea\Helper::bround($rawAmount,2) *100 );
                $tempRow->setVat(\Svea\Helper::bround($rawVat,2) *100 );

            } elseif (isset($row->amountIncVat) && isset($row->vatPercent)) {
                $rawAmount = $row->amountIncVat;
                $rawVat = $row->amountIncVat - ($row->amountIncVat/($row->vatPercent/100+1));
                $tempRow->setAmount( \Svea\Helper::bround($rawAmount,2) *100 );
                $tempRow->setVat( \Svea\Helper::bround($rawVat,2) *100 );

            } else {
                $rawAmount = $row->amountIncVat;
                $rawVat = ($row->amountIncVat - $row->amountExVat);
                $tempRow->setAmount( \Svea\Helper::bround($rawAmount,2) *100 );
                $tempRow->setVat( \Svea\Helper::bround($rawVat,2) *100);
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

    public function formatInvoiceFeeRows($row) {
            $tempRow = new HostedOrderRowBuilder();

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
                $tempRow->setAmount( \Svea\Helper::bround($rawAmount,2) *100 );
                $tempRow->setVat( \Svea\Helper::bround($rawVat,2) *100 );

            } elseif (isset($row->amountIncVat) && isset($row->vatPercent)) {
                $rawAmount = $row->amountIncVat;
                $rawVat = $row->amountIncVat - ($row->amountIncVat/($row->vatPercent/100+1));
                $tempRow->setAmount( \Svea\Helper::bround($rawAmount,2) *100 );
                $tempRow->setVat( \Svea\Helper::bround($rawVat,2) *100 );

            } else {
                $rawAmount = $row->amountIncVat;
                $rawVat = ($row->amountIncVat - $row->amountExVat);
                $tempRow->setAmount( \Svea\Helper::bround($rawAmount,2) *100 );
                $tempRow->setVat( \Svea\Helper::bround($rawVat,2) *100);
            }

            if (isset($row->unit)) {
                $tempRow->setUnit($row->unit);
            }

            $tempRow->setQuantity(1);
            $this->newRows[] = $tempRow;
            $this->invoiceAmount += ($tempRow->amount );
            $this->invoiceVat += ($tempRow->vat );
    }

    public function formatFixedDiscountRows($row) {
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
            // use old method of calculating discounts from single discount amount inc. vat, i.e. the amount specified includes vat.
            if (isset($row->amount) && !isset($row->amountExVat) && !isset($row->vatPercent)) {
                $discountInPercent = ($row->amount * 100) / $this->rawAmount;   // discount as fraction of raw total order sum (raw doesn't decrease if multiple discounts in the same order

                $rawAmount = $row->amount;
                $rawVat = $this->rawVat/100 * $discountInPercent;     // divide by 100 so that our "round and multiply" works in setVat below
                $tempRow->setAmount( - \Svea\Helper::bround($rawAmount,2) *100 );
                $tempRow->setVat( - \Svea\Helper::bround($rawVat,2) *100 );
            }

            // if specified amount ex vat, split the discount across vat rates according to relative amounts taken ex vat. as we apply the discount before tax,
            // the total discount sum must include the vat on the discounted amount.
            elseif (!isset($row->amount) && isset($row->amountExVat) && !isset($row->vatPercent)) {
                $discountInPercent = ($row->amountExVat * 100) / ($this->totalAmount - $this->totalVat);
                $rawAmount = $row->amountExVat;
                $rawVat = $this->rawVat/100 * $discountInPercent;     // divide by 100 so that our "round and multiply" works in setVat below
                $tempRow->setAmount( - \Svea\Helper::bround($rawAmount + $rawVat,2)*100);
                $tempRow->setVat( - \Svea\Helper::bround($rawVat,2)*100 );
            }

            // calculate amount, vat from two out of three given by customer, see unit tests in HostedPaymentTest
            elseif (isset($row->amountExVat) && isset($row->vatPercent)) {
                $rawAmount = $row->amountExVat *($row->vatPercent/100+1);
                $rawVat = $row->amountExVat *($row->vatPercent/100);
                $tempRow->setAmount( - \Svea\Helper::bround($rawAmount,2) *100 );
                $tempRow->setVat( - \Svea\Helper::bround($rawVat,2) *100 );

            } elseif (isset($row->amount) && isset($row->vatPercent)) {
                $rawAmount = $row->amount;
                $rawVat = $row->amount - ($row->amount/($row->vatPercent/100+1));
                $tempRow->setAmount( - \Svea\Helper::bround($rawAmount,2) *100 );
                $tempRow->setVat( - \Svea\Helper::bround($rawVat,2) *100 );

            } else {
                $rawAmount = $row->amount;
                $rawVat = ( $row->amount - $row->amountExVat);
                $tempRow->setAmount( - \Svea\Helper::bround($rawAmount,2) *100 );
                $tempRow->setVat( - \Svea\Helper::bround($rawVat,2) *100);
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

    public function formatRelativeDiscountRows($row) {
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

            $rawAmount = $this->rawAmount/100 * $row->discountPercent/100;
            $rawVat = $this->rawVat/100 * $row->discountPercent/100;

            $tempRow->setAmount( - \Svea\Helper::bround($rawAmount,2)*100 );
            $tempRow->setVat( - \Svea\Helper::bround($rawVat,2)*100 );

            $tempRow->setQuantity(1);

            $this->totalAmount += $tempRow->amount;
            $this->totalVat += $tempRow->vat;
            $this->newRows[] = $tempRow;

            $this->discountAmount += $tempRow->amount;
            $this->discountVat +=  $tempRow->vat;
    }

    /**
     * formatTotalAmount() is used by i.e. HostedPayment calculateRequestValues to
     * get the total vat sum of the order.
     *
     * @deprecated @param array $rows $rows is no longer used, instead we return
     * the object rawAmount value, modified by shippinga and discounts
     * @return integer total order amount, including vat
     */
    public function formatTotalAmount($rows) {
        return $this->rawAmount + $this->shippingAmount + $this->discountAmount + $this->invoiceAmount;
    }

    /**
     * formatTotalVat() is used by i.e. HostedPayment calculateRequestValues to
     * get the total vat sum of the order.
     *
     * @deprecated @param array $rows $rows is no longer used, instead we return
     * the object rawAmount value, modified by shippinga and discounts
     * @return integer total amount of vat due in order
     */
    public function formatTotalVat($rows) {
        return $this->rawVat + $this->shippingVat + $this->discountVat + $this->invoiceVat;
    }
}
