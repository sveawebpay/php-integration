<?php

namespace Svea\WebPay\BuildOrder;

use Svea\WebPay\Checkout\Service\Admin\AdminImplementationService;
use Svea\WebPay\HostedService\HostedAdminRequest\CancelRecurSubscription;

class CancelRecurSubscriptionBuilder extends OrderBuilder
{

    /**
     * @var string $subscriptionId
     */
    public $subscriptionId;

    /**
     * Set subscriptionId
     *
     * @param string $subscriptionId
     * @return $this
     */
    public function setSubscriptionId($subscriptionId)
    {
        $this->subscriptionId = $subscriptionId;

        return $this;
    }

    public function cancelRecurSubscription()
    {
        $cancelRecurSubscription = new CancelRecurSubscription($this->conf);
        $cancelRecurSubscription->subscriptionId = $this->subscriptionId;
        $cancelRecurSubscription->countryCode = $this->countryCode;
        return $cancelRecurSubscription;
    }
}
