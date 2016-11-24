<?php

namespace Svea\WebPay\BuildOrder;

use Svea\WebPay\Checkout\Service\Admin\GetTaskInfoService;
use Svea\WebPay\Checkout\Service\Admin\AdminImplementationService;

class QueryTaskInfoBuilder extends CheckoutAdminOrderBuilder
{
    /**
     * @var string $taskUrl
     */
    public $taskUrl;

    /**
     * Set Url that should be called with Svea Authorization
     *
     * @param string $url
     * @return $this
     */
    public function setTaskUrl($url)
    {
        $this->taskUrl = $url;

        return $this;
    }

    /**
     * @return AdminImplementationService
     */
    public function getTaskInfo()
    {
        return new GetTaskInfoService($this);
    }
}
