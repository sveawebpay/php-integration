<?php

namespace Svea\WebPay\Checkout\Service\Admin;

use Svea\WebPay\BuildOrder\UpdateOrderRowsBuilder;
use Svea\WebPay\Checkout\Validation\Admin\UpdateOrderRowValidator;
use Svea\WebPay\Helper\Helper;

class UpdateOrderRowsService extends AdminImplementationService
{
    /**
     * @var UpdateOrderRowsBuilder $adminBuilder
     */
    public $adminBuilder;

    /**
     * Validate order data
     */
    public function validate()
    {
        $validator = new UpdateOrderRowValidator();
        $errors = $validator->validate($this->adminBuilder);

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
            'orderRowId' => $this->adminBuilder->numberedOrderRows[0]->rowNumber,
            'orderRow' => $this->formatOrderRowValues()
        );

        return $requestData;
    }

    /**
     * Send call Connection Library
     */
    public function doRequest()
    {
        $preparedData = $this->prepareRequest();
        $response = $this->checkoutAdminConnection->updateOrderRow($preparedData);

        return $response;
    }

    private function formatOrderRowValues()
    {
        $requestOrderRow = array();

        $rowData = $this->adminBuilder->numberedOrderRows[0];
        foreach ($rowData as $orderRowKey => $orderRowValue) {
            switch ($orderRowKey) {
                case 'amountIncVat':
                    $requestOrderRow['unitPrice'] = intval((string)Helper::bround($orderRowValue, 2) * 100);
                    break;
                case 'vatPercent':
                    $requestOrderRow['vatPercent'] = intval((string)Helper::bround($orderRowValue, 2) * 100);
                    break;
                case 'discountPercent':
                    $requestOrderRow['discountPercent'] = intval((string)Helper::bround($orderRowValue, 2) * 100);
                    break;
                case 'quantity':
                    $requestOrderRow['quantity'] = intval((string)Helper::bround($orderRowValue, 2) * 100);
                    break;
                default:
                    $requestOrderRow[$orderRowKey] = $orderRowValue;
            }
        }

        return $requestOrderRow;
    }
}
