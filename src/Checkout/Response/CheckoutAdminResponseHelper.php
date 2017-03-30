<?php

namespace Svea\WebPay\Checkout\Response;

class CheckoutAdminResponseHelper
{
    const MINOR_CURRENCY = 100;

    public static function processResponse($response)
    {
        if (isset($response['OrderAmount']) && !empty($response['OrderAmount'])) {
            $response['OrderAmount'] = $response['OrderAmount'] / self::MINOR_CURRENCY;
        }

        if (isset($response['CancelledAmount']) && !empty($response['CancelledAmount'])) {
            $response['CancelledAmount'] = $response['CancelledAmount'] / self::MINOR_CURRENCY;
        }

        $orderRows = $response['OrderRows'];
        $deliveries = $response['Deliveries'];

        if (count($deliveries) > 0) {
            $response['Deliveries'] = self::convertDeliveries($deliveries);
        }

        if (count($orderRows) > 0) {
            $response['OrderRows'] = self::convertOrderRows($orderRows);
        }

        return $response;
    }

    private static function convertDeliveries($deliveries)
    {
        $newDeliveries = array();
        foreach ($deliveries as $delivery) {
            if (isset($delivery['DeliveryAmount']) && !empty($delivery['DeliveryAmount'])) {
                $delivery['DeliveryAmount'] = $delivery['DeliveryAmount'] / self::MINOR_CURRENCY;
            }

            if (isset($delivery['CreditedAmount']) && !empty($delivery['CreditedAmount'])) {
                $delivery['CreditedAmount'] = $delivery['CreditedAmount'] / self::MINOR_CURRENCY;
            }

            $credits = $delivery['Credits'];
            if (count($credits) > 0) {
                $delivery['Credits'] = self::convertCredits($credits);
            }

            $deliveryOrderRows = $delivery['OrderRows'];
            $newDeliveryOrderRows = array();

            if (is_array($deliveryOrderRows)) {
                foreach ($deliveryOrderRows as $deliveryOrderRow) {
                    $newDeliveryOrderRows[] = self::convertMinorCurrencyValues($deliveryOrderRow);
                }
            }

            $delivery['OrderRows'] = $newDeliveryOrderRows;
            $newDeliveries[] = $delivery;
        }

        return $newDeliveries;
    }

    private static function convertCredits($credits)
    {
        $newCredits = array();

        foreach ($credits as $credit) {
            if (isset($credit['Amount']) && !empty($credit['Amount'])) {
                $credit['Amount'] = $credit['Amount'] / self::MINOR_CURRENCY;
            }

            $creditOrderRows = $credit['OrderRows'];
            $newCreditOrderRows = array();

            if (is_array($creditOrderRows)) {
                foreach ($creditOrderRows as $creditOrderRow) {
                    $newCreditOrderRows[] = self::convertMinorCurrencyValues($creditOrderRow);
                }
            }

            $credit['OrderRows'] = $newCreditOrderRows;
            $newCredits[] = $credit;
        }

        return $newCredits;
    }

    private static function convertOrderRows($orderRows)
    {
        $newOrderRows = array();
        foreach ($orderRows as $row) {
            $newOrderRows[] = self::convertMinorCurrencyValues($row);
        }

        return $newOrderRows;
    }

    private static function convertMinorCurrencyValues($orderRowData)
    {
        if (isset($orderRowData['Quantity']) && !empty($orderRowData['Quantity'])) {
            $orderRowData['Quantity'] = $orderRowData['Quantity'] / self::MINOR_CURRENCY;
        }

        if (isset($orderRowData['UnitPrice']) && !empty($orderRowData['UnitPrice'])) {
            $orderRowData['UnitPrice'] = $orderRowData['UnitPrice'] / self::MINOR_CURRENCY;
        }

        if (isset($orderRowData['VatPercent']) && !empty($orderRowData['VatPercent'])) {
            $orderRowData['VatPercent'] = $orderRowData['VatPercent'] / self::MINOR_CURRENCY;
        }

        if (isset($orderRowData['DiscountPercent']) && !empty($orderRowData['DiscountPercent'])) {
            $orderRowData['DiscountPercent'] = $orderRowData['DiscountPercent'] / self::MINOR_CURRENCY;
        }

        return $orderRowData;
    }
}