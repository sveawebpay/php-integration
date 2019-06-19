<?php


namespace Svea\WebPay\Helper\PaymentPlanHelper\CampaignTypeCalculator;


interface IPaymentPlanCalculator
{
    static public function calculateTotalAmountToPay($totalPrice, $campaign, $decimals = 0);
    static public function calculateMonthlyAmountToPay($totalPrice, $campaign, $decimals = 0);
    static public function calculateEffectiveInterestRate($totalPrice, $campaign, $decimals = 0);
    static public function calculatePaymentFactor($numberOfPayments, $yearlyInterestRate, $paymentFrequencyPerYear = 12);
    static public function calculateMonthlyAnnuityAmount($totalPrice, $campaign);
}