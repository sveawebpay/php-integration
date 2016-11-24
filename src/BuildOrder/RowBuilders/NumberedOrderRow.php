<?php

namespace Svea\WebPay\BuildOrder\RowBuilders;

/**
 * This is an extension of the orderRow class, used in the Svea\WebPay\WebPayAdmin::queryOrder() response and methods that adminster individual order rows.
 *
 * NumberedOrderRow is returned with the Svea\WebPay\WebPayAdmin::QueryOrder() response, or
 * directly via the service requests GetOrders, or QueryTransaction, responses.
 *
 * $myNumberedOrderRow =
 *     Svea\WebPay\WebPayItem::numberedOrderRow()
 *
 *      //inherited from OrderRow
 *      ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
 *      ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
 *      ->setAmountIncVat(125.00)               // optional, need to use two out of three of the price specification methods
 *      ->setQuantity(2)                        // required
 *      ->setUnit("st")                         // optional
 *      ->setName('Prod')                       // optional
 *      ->setDescription("Specification")       // optional
 *      ->setArticleNumber("1")                 // optional
 *      ->setDiscountPercent(0)                 // optional
 *
 *      //numberedOrderRow
 *      ->setCreditInvoiceId($creditInvoiceIdAsNumeric)         //optional
 *      ->setInvoiceId($invoiceIdAsNumeric)                     //optional
 *      ->setRowNumber($rowNumberAsNumeric)                     //optional
 *      ->setStatus(NumberedOrderRow::ORDERROWSTATUS_DELIVERED) //optional, one of _DELIVERED, _NOTDELIVERED, _CANCELLED
 *
 */
class NumberedOrderRow extends OrderRow
{
    const ORDERROWSTATUS_NOTDELIVERED = 'NotDelivered';
    const ORDERROWSTATUS_DELIVERED = 'Delivered';
    const ORDERROWSTATUS_CANCELLED = 'Cancelled';

    /**
     * @var string $creditInvoiceId reference to invoice to credit
     */
    public $creditInvoiceId;

    /**
     * @var string $invoiceId if order has been delivered, reference to resulting invoice
     */
    public $invoiceId;

    /**
     * @var integer $rowNumber the order row number, starting with 1 for the first order row
     */
    public $rowNumber;

    /**
     * @var string $status one of: NotDelivered | Delivered | Cancelled
     */
    public $status;

    /**
     * Optional.
     * @param string $creditInvoiceIdAsNumeric
     * @return $this
     */
    public function setCreditInvoiceId($creditInvoiceIdAsNumeric)
    {
        $this->creditInvoiceId = $creditInvoiceIdAsNumeric;
        return $this;
    }

    /**
     * Optional.
     * @param string $invoiceIdAsNumeric
     * @return $this
     */
    public function setInvoiceId($invoiceIdAsNumeric)
    {
        $this->invoiceId = $invoiceIdAsNumeric;
        return $this;
    }

    /**
     * Optional.
     * @param string $rowNumberAsNumeric
     * @return $this
     */
    public function setRowNumber($rowNumberAsNumeric)
    {
        $this->rowNumber = $rowNumberAsNumeric;
        return $this;
    }

    /**
     * Deprecated. Status is returned by Svea sytem and should not be populated in UpdateOrderRowsRequest
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * This is alias for setRowNUmber
     * Optional.
     * @param int $rowId
     * @return $this
     */
    public function setRowId($rowId)
    {
        $this->rowNumber = $rowId;
        return $this;
    }
}
