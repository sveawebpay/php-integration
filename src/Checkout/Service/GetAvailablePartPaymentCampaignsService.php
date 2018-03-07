<?php

namespace Svea\WebPay\Checkout\Service;

use Svea\WebPay\BuildOrder\Validator\ValidationException;
use Svea\WebPay\Checkout\Helper\CheckoutOrderBuilder;
use Svea\WebPay\Checkout\Validation\GetAvailablePartPaymentCampaignsValidator;

/**
 * Class GetAvailablePartPaymentCampaignsService
 * @package Svea\Svea\WebPay\WebPay\Checkout\Service
 */
class GetAvailablePartPaymentCampaignsService extends CheckoutService
{
    /*
     * Send call Connection Library
     * @return mixed
     */
    public function doRequest()
    {
        $requestData = $this->prepareRequest();

        foreach($requestData['presetValues'] as $presetValue)
        {
            if(strtolower($presetValue['typeName']) == 'iscompany')
            {
                $requestData = array(
                    'isCompany' => $presetValue['value']
                );
            }
        }

        if(!isset($requestData))
        {
            $requestData = null;
        }

        $response = $this->serviceConnection->getAvailablePartPaymentCampaigns($requestData);

        return $response;
    }

    /**
     * Validate order data
     * @return array|mixed
     */
    protected function validateOrder()
    {
        $validator = new GetAvailablePartPaymentCampaignsValidator();
        $errors = $validator->validate($this->order);

        return $errors;
    }

    /**
     * @throws \Svea\WebPay\BuildOrder\Validator\ValidationException
     */
    protected function prepareRequest()
    {
        $errors = $this->validateOrder();
        $this->processErrors($errors);
        $data = $this->mapPresetValue($this->order);

        return $data;
    }

    protected function mapPresetValue(CheckoutOrderBuilder $request)
    {
        $data = array();
        if (count($request->getPresetValues()) > 0) {
            foreach ($request->getPresetValues() as $presetValue) {
                $data['presetValues'] [] = $presetValue->returnPresetArray();
            }
        }
        return $data;
    }
}
