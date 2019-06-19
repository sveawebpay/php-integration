<?php

namespace Svea\WebPay\Helper\PaymentPlanHelper\CampaignTypeCalculator;

use Svea\WebPay\Helper\Helper;


class StandardPaymentPlanCalculator implements IPaymentPlanCalculator
{
    public static function calculateTotalAmountToPay($totalPrice, $campaign , $decimals = 0)
    {
        $numberOfPayments = max( 1, $campaign['contractLengthInMonths'] - $campaign['numberOfPaymentFreeMonths']);
        $paymentFactor = self::calculatePaymentFactor($numberOfPayments, $campaign['interestRatePercent']/ 100);
        return Helper::bround($campaign['initialFee'] + ($totalPrice * $paymentFactor + $campaign['notificationFee']) * $numberOfPayments, $decimals);
    }
    public static function calculateMonthlyAmountToPay($totalPrice, $campaign, $decimals = 0)
    {
        return Helper::bround(self::calculateTotalAmountToPay($totalPrice, $campaign, $decimals) / max(1, $campaign['contractLengthInMonths'] - $campaign['numberOfPaymentFreeMonths']), $decimals);
    }
    public static function calculateEffectiveInterestRate($totalPrice, $campaign, $decimals = 0)
    {
        $effectiveCalculator = new EffectiveInterestRateCalculator($totalPrice);
        $firstPayment = $campaign['initialFee'] + $campaign['notificationFee'] + Helper::bround(self::calculateMonthlyAnnuityAmount($totalPrice, $campaign), $decimals);
        $monthlyAmount = Helper::bround(self::calculateMonthlyAnnuityAmount($totalPrice, $campaign), $decimals) + $campaign['notificationFee'];
        return $effectiveCalculator->calculate($totalPrice, $firstPayment, $monthlyAmount, $campaign['contractLengthInMonths'], $campaign['numberOfPaymentFreeMonths']);
    }

    public static function calculatePaymentFactor($numberOfPayments, $yearlyInterestRate, $paymentFrequencyPerYear = 12)
    {
        $monthlyInterestRate = $yearlyInterestRate / $paymentFrequencyPerYear;
        return $monthlyInterestRate / (1-pow(1+$monthlyInterestRate, -$numberOfPayments));
    }

    public static function calculateMonthlyAnnuityAmount($totalPrice, $campaign)
    {
        $paymentFactor = self::calculatePaymentFactor($campaign['contractLengthInMonths'] - $campaign['numberOfPaymentFreeMonths'],
            $campaign['interestRatePercent']/ 100);
        return $totalPrice * $paymentFactor;
    }
}