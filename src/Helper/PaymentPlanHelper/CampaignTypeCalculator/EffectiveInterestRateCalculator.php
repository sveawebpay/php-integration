<?php


namespace Svea\WebPay\Helper\PaymentPlanHelper\CampaignTypeCalculator;

use Svea\WebPay\BuildOrder\Validator\ValidationException;
use Svea\WebPay\Helper\Helper;

class EffectiveInterestRateCalculator
{
    private $oneMonth = 0.08327627652; // Factor of month average length divided by average year length (leap years etc)
    private $tolerance = 0.00000001;
    private $initialLowerBound = 0;
    private $initialUpperBound = 10000000;
    private $payments = array();
    private $sizeOfLoan;

    public function __construct($sizeOfLoan)
    {
        $this->sizeOfLoan = $sizeOfLoan;
    }

    /**
     * @param $sizeOfLoan
     * @param $firstPayment
     * @param $monthlyPayment
     * @param $contactLengthInMonths
     * @param $deferralPeriodInMonths
     * @return float|int
     * @throws ValidationException
     */
    public function calculate($sizeOfLoan, $firstPayment, $monthlyPayment, $contactLengthInMonths, $deferralPeriodInMonths)
    {
        if($monthlyPayment < 0)
        {
            throw new ValidationException("Monthly payment can not be below 0");
        }
        if($contactLengthInMonths < 1)
        {
            throw new ValidationException("Contract length must be at least 1 month");
        }

        $firstPaymentMonth = min($contactLengthInMonths, $deferralPeriodInMonths + 1);
        $this->addPayment($this->oneMonth * $firstPaymentMonth, $firstPayment);

        for($month = $firstPaymentMonth + 1; $month <= $contactLengthInMonths; $month++)
        {
            $this->addPayment($this->oneMonth * $month, $monthlyPayment);
        }

        if(array_sum(array_column($this->payments, 'amount')) - $sizeOfLoan < 0.1)
        {
            return 0;
        }

        return Helper::bround($this->solveUsingBisection() * 100, 2);
    }

    /**
     * @return float|int
     * @throws ValidationException
     */
    public function solveUsingBisection()
    {
        $lowerBound = $this->initialLowerBound;
        $upperBound = $this->initialUpperBound;

        while($upperBound - $lowerBound > $this->tolerance)
        {
            $newPoint = $lowerBound + ($upperBound - $lowerBound) / 2;
            if($this->sign($this->evaluate($lowerBound)) == $this->sign($this->evaluate($newPoint)))
            {
                $lowerBound = $newPoint;
            }
            else
            {
                $upperBound = $newPoint;
            }
        }

        if(abs($lowerBound - $this->initialLowerBound) < 2 * $this->tolerance || abs($upperBound - $this->initialUpperBound) < 2 * $this->tolerance)
        {
            throw new ValidationException("No solution found");
        }
        return $lowerBound;
    }

    private function sign( $number ) {
        return ( $number > 0 ) ? 1 : ( ( $number < 0 ) ? -1 : 0 );
    }

    private function evaluate($val)
    {
        $sum = 0;
        foreach($this->payments as $value)
        {
            $sum += $value['amount'] / pow(1 + $val, $value['timeToPaymentInYears']);
        }
        return $sum - $this->sizeOfLoan;
    }

    public function addPayment($timeToPaymentInYears, $amount)
    {
        array_push($this->payments, array('timeToPaymentInYears' => $timeToPaymentInYears, 'amount' => $amount));
    }
}