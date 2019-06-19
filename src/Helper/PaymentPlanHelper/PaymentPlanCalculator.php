<?php


namespace Svea\WebPay\Helper\PaymentPlanHelper;

use Svea\WebPay\Helper\PaymentPlanHelper\CampaignTypeCalculator\InterestAndAmortizationFreePaymentPlanCalculator;
use Svea\WebPay\Helper\PaymentPlanHelper\CampaignTypeCalculator\InterestFreePaymentPlanCalculator;
use Svea\WebPay\Helper\PaymentPlanHelper\CampaignTypeCalculator\StandardPaymentPlanCalculator;
use Svea\WebPay\BuildOrder\Validator\ValidationException;

class PaymentPlanCalculator
{
    /**
     * Calculates total amount that has to be payed for a single campaign
     *
     * @param int $totalPrice ; Price for the whole order
     * @param $campaign ; Either array of campaign or CampaignCode object
     * @param $decimals ; default = 0, returned values will round to this precision
     * @return int ; Returns total amount that should be paid for the provided campaign
     * @throws ValidationException
     */
    public static function getTotalAmountToPay($totalPrice, $campaign, $decimals = 0)
    {
        $campaign = (array)$campaign;

        // The Checkout API returns array keys with the first letter, we have to convert the first letter to lowercase
        if(array_key_exists('PaymentPlanType', $campaign) == true)
        {
            $campaign = self::convertFromCheckoutArray($campaign);
        }

        switch($campaign['paymentPlanType'])
        {
            case "InterestAndAmortizationFree":
                return InterestAndAmortizationFreePaymentPlanCalculator::calculateTotalAmountToPay($totalPrice, $campaign, $decimals);
                break;
            case "InterestFree":
                return InterestFreePaymentPlanCalculator::calculateTotalAmountToPay($totalPrice, $campaign, $decimals);
                break;
            case "Standard":
                return StandardPaymentPlanCalculator::calculateTotalAmountToPay($totalPrice, $campaign, $decimals);
                break;

        }
        throw new ValidationException("paymentPlanType not recognized");
    }

    /**
     * Calculates monthly amount that has to be payed for a single campaign
     *
     * @param int $totalPrice ; Price for the whole order
     * @param $campaign ; Either array of campaign or CampaignCode object
     * @param $decimals ; default = 0, returned values will round to this precision
     * @return int ; Returns monthly amount that should be paid for the provided campaign
     * @throws ValidationException
     */
    public static function getMonthlyAmountToPay($totalPrice, $campaign, $decimals = 0)
    {
        $campaign = (array)$campaign;

        // The Checkout API returns array keys with the first letter, we have to convert the first letter to lowercase
        if(array_key_exists('PaymentPlanType', $campaign) == true)
        {
            $campaign = self::convertFromCheckoutArray($campaign);
        }

        switch($campaign['paymentPlanType'])
        {
            case "InterestAndAmortizationFree":
                return InterestAndAmortizationFreePaymentPlanCalculator::calculateMonthlyAmountToPay($totalPrice, $campaign, $decimals);
                break;
            case "InterestFree":
                return InterestFreePaymentPlanCalculator::calculateMonthlyAmountToPay($totalPrice, $campaign, $decimals);
                break;
            case "Standard":
                return StandardPaymentPlanCalculator::calculateMonthlyAmountToPay($totalPrice, $campaign, $decimals);
                break;
        }
        throw new ValidationException("paymentPlanType not recognized");
    }

    /**
     * Calculates effective interest rate for a single campaign
     *
     * @param int $totalPrice ; Price for the whole order
     * @param $campaign ; Either array of campaign or CampaignCode object
     * @param $decimals ; default = 0, Must be set to 2 if provided campaign is to be paid in euros
     * @return int ; Returns effective interest rate for the provided campaign
     * @throws ValidationException
     */
    public static function getEffectiveInterestRate($totalPrice, $campaign, $decimals = 0)
    {
        $campaign = (array)$campaign;

        // The Checkout API returns array keys with the first letter, we have to convert the first letter to lowercase
        if(array_key_exists('PaymentPlanType', $campaign) == true)
        {
            $campaign = self::convertFromCheckoutArray($campaign);
        }

        switch($campaign['paymentPlanType'])
        {
            case "InterestAndAmortizationFree":
                return InterestAndAmortizationFreePaymentPlanCalculator::calculateEffectiveInterestRate($totalPrice, $campaign, $decimals);
                break;
            case "InterestFree":
                return InterestFreePaymentPlanCalculator::calculateEffectiveInterestRate($totalPrice, $campaign, $decimals);
                break;
            case "Standard":
                return StandardPaymentPlanCalculator::calculateEffectiveInterestRate($totalPrice, $campaign, $decimals);
                break;
        }
        throw new ValidationException("paymentPlanType not recognized");
    }

    /**
     * Calculates effective interest rate, monthly amount to pay and total amount to pay for a single campaign
     *
     * @param int $totalPrice ; Price for the whole order
     * @param $campaign ; Either array of campaign or CampaignCode object
     * @param $decimals ; default = 0, returned values will round to this precision. Value must be 2 if payment plan is to be paid in euros
     * @return array ; Returns array of campaign with effective interest rate, monthly amount to pay and total amount to pay
     * @throws ValidationException
     */
    public static function getAllCalculations($totalPrice, $campaign , $decimals = 0)
    {
        $campaign = (array)$campaign;

        // The Checkout API returns array keys with the first letter, we have to convert the first letter to lowercase
        if(array_key_exists('PaymentPlanType', $campaign) == true)
        {
            $campaign = self::convertFromCheckoutArray($campaign);
        }

        switch($campaign['paymentPlanType'])
        {
            case "InterestAndAmortizationFree":
                $campaign['effectiveInterestRate'] = InterestAndAmortizationFreePaymentPlanCalculator::calculateEffectiveInterestRate($totalPrice, $campaign, $decimals);
                $campaign['monthlyAmountToPay'] = InterestAndAmortizationFreePaymentPlanCalculator::calculateMonthlyAmountToPay($totalPrice, $campaign, $decimals);
                $campaign['totalAmountToPay'] = InterestAndAmortizationFreePaymentPlanCalculator::calculateTotalAmountToPay($totalPrice, $campaign, $decimals);
                break;
            case "InterestFree":
                $campaign['effectiveInterestRate'] = InterestFreePaymentPlanCalculator::calculateEffectiveInterestRate($totalPrice, $campaign, $decimals);
                $campaign['monthlyAmountToPay'] = InterestFreePaymentPlanCalculator::calculateMonthlyAmountToPay($totalPrice, $campaign, $decimals);
                $campaign['totalAmountToPay'] = InterestFreePaymentPlanCalculator::calculateTotalAmountToPay($totalPrice, $campaign, $decimals);
                break;
            case "Standard":
                $campaign['effectiveInterestRate'] = StandardPaymentPlanCalculator::calculateEffectiveInterestRate($totalPrice, $campaign, $decimals);
                $campaign['monthlyAmountToPay'] = StandardPaymentPlanCalculator::calculateMonthlyAmountToPay($totalPrice, $campaign, $decimals);
                $campaign['totalAmountToPay'] = StandardPaymentPlanCalculator::calculateTotalAmountToPay($totalPrice, $campaign, $decimals);
                break;
            default:
                throw new ValidationException("paymentPlanType not recognized");
        }
        if(array_key_exists('checkout', $campaign) == true)
        {
            $campaign = self::convertToCheckoutArray($campaign);
        }
        return $campaign;
    }


    /**
     * Calculates total amount to be paid every campaign provided
     *
     * @param int $totalPrice ; Price for the whole order
     * @param $campaigns ; Either array of campaign or CampaignCode object
     * @param $decimals ; default = 0, returned values will round to this precision
     * @param $ignoreMinMaxFlag ; default = false, if set to true then all campaigns will be returned regardless of if the order total is within the campaigns fromAmount and toAmount
     * @return array ; Returns array of campaigns with their params and total amount to be paid
     * @throws ValidationException
     */
    public static function getTotalAmountToPayFromCampaigns($totalPrice, $campaigns, $decimals = 0, $ignoreMinMaxFlag = false)
    {
        $result = array();

        foreach($campaigns as $key => $campaign)
        {
            $campaign = (array)$campaign;
            if(array_key_exists('PaymentPlanType', $campaign) == true)
            {
                $campaign = self::convertFromCheckoutArray($campaign);
            }
            if($ignoreMinMaxFlag || $campaign['fromAmount'] <= $totalPrice && $campaign['toAmount'] >= $totalPrice) {


                $campaign['totalAmountToPay'] = self::getTotalAmountToPay($totalPrice, $campaign, $decimals);

                if (array_key_exists('checkout', $campaign) == true) {
                    $campaign = self::convertToCheckoutArray($campaign);
                }
                array_push($result, $campaign);
            }
        }

        return $result;
    }

    /**
     * Calculates monthly amount to be paid every campaign provided
     *
     * @param int $totalPrice ; Price for the whole order
     * @param $campaigns ; Either array of campaign or CampaignCode object
     * @param $decimals ; default = 0, returned values will round to this precision
     * @param $ignoreMinMaxFlag ; default = false, if set to true then all campaigns will be returned regardless of if the order total is within the campaigns fromAmount and toAmount
     * @return array ; Returns array of campaigns with their params and monthly amount to be paid
     * @throws ValidationException
     */
    public static function getMonthlyAmountToPayFromCampaigns($totalPrice, $campaigns, $decimals = 0, $ignoreMinMaxFlag = false)
    {
        $result = array();

        foreach($campaigns as $key => $campaign)
        {
            $campaign = (array)$campaign;
            if(array_key_exists('PaymentPlanType', $campaign) == true)
            {
                $campaign = self::convertFromCheckoutArray($campaign);
            }
            if($ignoreMinMaxFlag || $campaign['fromAmount'] <= $totalPrice && $campaign['toAmount'] >= $totalPrice) {


                $campaign['monthlyAmountToPay'] = self::getMonthlyAmountToPay($totalPrice, $campaign, $decimals);

                if (array_key_exists('checkout', $campaign) == true) {
                    $campaign = self::convertToCheckoutArray($campaign);
                }
                array_push($result, $campaign);
            }
        }
        return $result;
    }

    /**
     * Calculates effective interest rate on every campaign provided
     *
     * @param int $totalPrice ; Price for the whole order
     * @param $campaigns ; Either array of campaign or CampaignCode object
     * @param $decimals ; default = 0, returned values will round to this precision
     * @param $ignoreMinMaxFlag ; default = false, if set to true then all campaigns will be returned regardless of if the order total is within the campaigns fromAmount and toAmount
     * @return array ; Returns array of campaigns with their params and their respective effective interest rate
     * @throws ValidationException
     */
    public static function getEffectiveInterestRateFromCampaigns($totalPrice, $campaigns, $decimals = 0, $ignoreMinMaxFlag = false)
    {
        $result = array();

        foreach($campaigns as $key => $campaign)
        {
            $campaign = (array)$campaign;
            if(array_key_exists('PaymentPlanType', $campaign) == true)
            {
                $campaign = self::convertFromCheckoutArray($campaign);
            }
            if($ignoreMinMaxFlag || $campaign['fromAmount'] <= $totalPrice && $campaign['toAmount'] >= $totalPrice) {


                $campaign['effectiveInterestRate'] = self::getEffectiveInterestRate($totalPrice, $campaign, $decimals);

                if (array_key_exists('checkout', $campaign) == true) {
                    $campaign = self::convertToCheckoutArray($campaign);
                }
                array_push($result, $campaign);
            }
        }
        return $result;
    }

    /**
     * Calculates effective interest rate, monthly amount to pay and total amount to pay on every campaign provided
     *
     * @param int $totalPrice ; Price for the whole order
     * @param $campaigns ; Either array of campaign or CampaignCode object
     * @param $decimals ; default = 0, returned values will round to this precision. Value must be 2 if payment plan is to be paid in euros
     * @param $ignoreMinMaxFlag ; default = false, if set to true then all campaigns will be returned regardless of if the order total is within the campaigns fromAmount and toAmount
     * @return array ; Returns array of campaigns with their params and the result of the calculations
     * @throws ValidationException
     */
    public static function getAllCalculationsFromCampaigns($totalPrice, $campaigns, $decimals = 0, $ignoreMinMaxFlag = false)
    {
        $result = array();

        foreach($campaigns as $key => $campaign)
        {
            $campaign = (array)$campaign;
            if(array_key_exists('PaymentPlanType', $campaign) == true)
            {
                $campaign = self::convertFromCheckoutArray($campaign);
            }
            if($ignoreMinMaxFlag || $campaign['fromAmount'] <= $totalPrice && $campaign['toAmount'] >= $totalPrice) {


                $campaign['totalAmountToPay'] = self::getTotalAmountToPay($totalPrice, $campaign, $decimals);
                $campaign['monthlyAmountToPay'] = self::getMonthlyAmountToPay($totalPrice, $campaign, $decimals);
                $campaign['effectiveInterestRate'] = self::getEffectiveInterestRate($totalPrice, $campaign, $decimals);

                if (array_key_exists('checkout', $campaign) == true) {
                    $campaign = self::convertToCheckoutArray($campaign);
                }
                array_push($result, $campaign);
            }
        }
        return $result;
    }

    /**
     * Converts an array of a campaign that's returned by GetAvailablePartPaymentCampaigns to the same format as a regular campaign
     * @param array ; Campaign returned from GetAvailablePartPaymentCampaigns
     * @return array ; converted campaign
     */
    private static function convertFromCheckoutArray($campaign)
    {
        $campaign = self::convertFirstArrayKeyToLowerCase($campaign);
        $campaign['checkout'] = true;
        switch($campaign['paymentPlanType'])
        {
            case 0:
                $campaign['paymentPlanType'] = "Standard";
                break;
            case 1:
                $campaign['paymentPlanType'] = "InterestFree";
                break;
            case 2:
                $campaign['paymentPlanType'] = "InterestAndAmortizationFree";
                break;
        }
        return $campaign;
    }

    /**
     * Converts an array back to the checkout campaign format
     * @param array
     * @return array
     */
    private static function convertToCheckoutArray($campaign)
    {
        $campaign = self::convertFirstArrayKeyToUpperCase($campaign);
        unset($campaign['Checkout']);
        switch($campaign['PaymentPlanType'])
        {
            case "Standard":
                $campaign['PaymentPlanType'] = 0;
                break;
            case "InterestFree":
                $campaign['PaymentPlanType'] = 1;
                break;
            case "InterestAndAmortizationFree":
                $campaign['PaymentPlanType'] = 2;
                break;
        }
        return $campaign;
    }

    private static function convertFirstArrayKeyToLowerCase($campaign)
    {
        $campaign = array_combine(
            array_map('lcfirst', array_keys($campaign)),
            array_values($campaign)
        );
        return $campaign;
    }

    private static function convertFirstArrayKeyToUpperCase($campaign)
    {
        $campaign = array_combine(
            array_map('ucfirst', array_keys($campaign)),
            array_values($campaign)
        );
        return $campaign;
    }
}