<?php


namespace Svea\WebPay\Helper\PaymentPlanHelper\CampaignTypeCalculator;

use Svea\WebPay\Helper\Helper;

class InterestFreePaymentPlanCalculator implements IPaymentPlanCalculator
{
    public static function calculateTotalAmountToPay($totalPrice, $campaign, $decimals = 0)
    {
        return $totalPrice + $campaign['initialFee'] + $campaign['notificationFee'] * max(1, $campaign['contractLengthInMonths'] - $campaign['numberOfPaymentFreeMonths']);
    }

    public static function calculateMonthlyAmountToPay($totalPrice, $campaign, $decimals = 0)
    {
        return Helper::bround(self::calculateTotalAmountToPay($totalPrice, $campaign) / max(1, $campaign['contractLengthInMonths'] - $campaign['numberOfPaymentFreeMonths']), $decimals);
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
        return 1 / $numberOfPayments;
    }

    public static function calculateMonthlyAnnuityAmount($totalPrice, $campaign)
    {
        $paymentFactor = self::calculatePaymentFactor($campaign['contractLengthInMonths'] - $campaign['numberOfPaymentFreeMonths'],
            $campaign['interestRatePercent']);
        return $totalPrice * $paymentFactor;
    }
}