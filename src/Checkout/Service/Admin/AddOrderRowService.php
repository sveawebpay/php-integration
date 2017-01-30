<?php

namespace Svea\WebPay\Checkout\Service\Admin;

use Svea\WebPay\Helper\Helper;
use Svea\WebPay\BuildOrder\AddOrderRowsBuilder;
use Svea\WebPay\Checkout\Validation\Admin\AddOrderRowValidator;

class AddOrderRowService extends AdminImplementationService
{
    /**
     * @var AddOrderRowsBuilder $adminBuilder
     */
    public $adminBuilder;

    /**
     * Validate order data
     */
    public function validate()
    {
        $validator = new AddOrderRowValidator();
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
        $response = $this->checkoutAdminConnection->addOrderRow($preparedData);

        return $response;
    }

    private function formatOrderRowValues()
    {
        $requestOrderRow = array();

        $rowData = $this->adminBuilder->orderRows[0];
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
