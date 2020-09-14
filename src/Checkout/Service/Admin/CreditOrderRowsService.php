<?php

namespace Svea\WebPay\Checkout\Service\Admin;

use Svea\WebPay\BuildOrder\CheckoutAdminOrderBuilder;
use Svea\WebPay\BuildOrder\CreditOrderRowsBuilder;
use Svea\WebPay\BuildOrder\RowBuilders\OrderRow;
use Svea\WebPay\Helper\Helper;

class CreditOrderRowsService extends AdminImplementationService
{
    /**
     * @var CreditOrderRowsBuilder $adminBuilder
     */
    public $adminBuilder;

    /**
     * @var bool $isNewCreditRow
     */
    protected $isNewCreditRow;

    public function __construct(CheckoutAdminOrderBuilder $adminBuilder, $isNewCreditRow = false)
    {
        parent::__construct($adminBuilder);
        $this->isNewCreditRow = $isNewCreditRow;
    }

    /**
     * Validate order data
     */
    public function validate()
    {
        $errors = array();

        $orderId = $this->adminBuilder->orderId;
        if (empty($orderId) || !is_int($orderId)) {
            $errors['incorrect Order Id'] = "Order Id can't be empty and must be Integer";
        }

        $deliveryId = $this->adminBuilder->deliveryId;
        if (is_int($deliveryId) || (is_float($deliveryId) && $deliveryId > 2147483647))
        {

        }
        else
        {
            $errors['incorrect Delivery Id'] = "Delivery Id can't be empty and must be Integer";
        }

        if ($this->isNewCreditRow === true) {
            $creditOrderRows = $this->adminBuilder->creditOrderRows;

            if (count($creditOrderRows) > 1) {
                $errors['incorrect New Credit Row'] = "Only one Credit row can be set!";
            }

            $newCreditRow = $creditOrderRows[0];
            if (!($newCreditRow instanceof OrderRow)) {
                $errors['incorrect New Credit Row'] = "New Credit Row can't be empty and must be array";
            } else {
                foreach ($creditOrderRows as $orderRow) {
                    if (!isset($orderRow->vatPercent)) {
                        $errors['missing order row vat information'] = "cannot calculate orderRow vatPercent, need to set vatPercent.";
                    }

                    if (!is_float($orderRow->amountIncVat) && $orderRow->amountIncVat !== 0) {
                        $errors['missing order row amount information'] = "cannot calculate orderRow amount, need to set value for amountIncVat.";
                    }
                }
            }
        } else {
            $orderRowIds = $this->adminBuilder->rowsToCredit;
            if (is_array($orderRowIds) && count($orderRowIds) > 0) {
                foreach ($orderRowIds as $orderRowId) {
                    if (empty($orderRowId) || !is_int($orderRowId)) {
                        $errors['incorrect Order Row Id'] = "Order Row Id can't be empty and must be Integer";
                    }
                }
            } else {
                $errors['missing order rows'] = "must be at least one Order row set.";
            }
        }

        $this->processErrors($errors);
    }

    /**
     * Format given date so that will match data structure required for Admin API
     * @return mixed
     */
    public function prepareRequest()
    {
        $this->validate();

        $requestData = array(
            'orderId' => $this->adminBuilder->orderId,
            'deliveryId' => $this->adminBuilder->deliveryId
        );

        if (!empty($this->adminBuilder->rowsToCredit)) {
            $requestData['orderRowIds'] = $this->adminBuilder->rowsToCredit;
        } elseif (count($this->adminBuilder->creditOrderRows) > 0) {
            /**
             * @var OrderRow $orderRow
             */
            $orderRow = $this->adminBuilder->creditOrderRows[0];

            $requestData['newCreditRow'] = array(
                'name' => $orderRow->name,
                'quantity'   => intval((string)Helper::bround($orderRow->quantity, 2) * 100),
                'unitPrice'  => intval((string)Helper::bround($orderRow->amountIncVat, 2) * 100),
                'vatPercent' => intval((string)Helper::bround($orderRow->vatPercent, 2) * 100)
            );
        }

        return $requestData;
    }

    /**
     * Send call Connection Library
     */
    public function doRequest()
    {
        $preparedData = $this->prepareRequest();
        if ($this->isNewCreditRow === true) {
            $response = $this->checkoutAdminConnection->creditNewOrderRow($preparedData);
        } else {
            $response = $this->checkoutAdminConnection->creditOrderRows($preparedData);
        }

        return $response;
    }
}
