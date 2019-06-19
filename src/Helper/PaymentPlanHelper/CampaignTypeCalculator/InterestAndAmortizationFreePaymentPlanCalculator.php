<?php


namespace Svea\WebPay\Helper\PaymentPlanHelper\CampaignTypeCalculator;

use Svea\WebPay\Helper\Helper;

class InterestAndAmortizationFreePaymentPlanCalculator implements IPaymentPlanCalculator
{
    public static function calculateTotalAmountToPay($totalPrice, $campaign, $decimals = 0)
    {
        return $totalPrice + $campaign['notificationFee'] + $campaign['initialFee'];
    }

    public static function calculateMonthlyAmountToPay($totalPrice, $campaign, $decimals = 0)
    {
        return $totalPrice + $campaign['notificationFee'] + $campaign['initialFee'];
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
        return 1;
    }

    public static function calculateMonthlyAnnuityAmount($totalPrice, $campaign)
    {
        return $totalPrice;
    }

}