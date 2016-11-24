<?php

namespace Svea\WebPay\Checkout\Service\Admin;

use Svea\WebPay\BuildOrder\QueryTaskInfoBuilder;

class GetTaskInfoService extends AdminImplementationService
{
    /**
     * @var QueryTaskInfoBuilder $adminBuilder
     */
    public $adminBuilder;

    /**
     * Send call Connection Library
     */
    public function doRequest()
    {
        $preparedData = $this->prepareRequest();
        $response = $this->checkoutAdminConnection->getTask($preparedData);

        return $response;
    }

    /**
     * Format given date so that will match data structure required for Admin API
     * @return mixed
     */
    public function prepareRequest()
    {
        $this->validate();

        $requestData = array(
            'locationUrl' => $this->adminBuilder->taskUrl
        );

        return $requestData;
    }

    /**
     * Validate order data
     */
    public function validate()
    {
        $errors = array();

        $taskUrl = $this->adminBuilder->taskUrl;

        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $taskUrl)
        ) {
            $errors['incorrect Task URL'] = "Task Url must be valid Task Location URL string";
        }

        $this->processErrors($errors);
    }
}
