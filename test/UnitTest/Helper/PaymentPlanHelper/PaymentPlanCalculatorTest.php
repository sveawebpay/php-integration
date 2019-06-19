<?php

use \PHPUnit\Framework\TestCase;
use Svea\WebPay\BuildOrder\Validator\ValidationException;
use Svea\WebPay\WebService\WebServiceResponse\PaymentPlanParamsResponse;
use \Svea\WebPay\Helper\PaymentPlanHelper\PaymentPlanCalculator;
use \Svea\WebPay\Helper\PaymentPlanHelper\CampaignTypeCalculator\EffectiveInterestRateCalculator;

class PaymentPlanCalculatorTest extends TestCase
{
    protected $price;

    protected $paymentPlanParams;

    protected function setUp()
    {
        $this->price = 11200;
    }

    protected function getInterestAndAmortizationFreeCampaign()
    {
        $response = (object) array(
            "GetPaymentPlanParamsEuResult" => (object)array (
                "Accepted" => true,
                "ResultCode" => 0,
                "CampaignCodes" => (object)array(
                    "CampaignCodeInfo" => array(
                        0 =>
                            (object)array(
                                'CampaignCode' => 223060,
                                'Description' => 'Köp nu betala om 3 månader (räntefritt)',
                                'PaymentPlanType' => 'InterestAndAmortizationFree',
                                'ContractLengthInMonths' => 3,
                                'MonthlyAnnuityFactor' => '1',
                                'InitialFee' => '0',
                                'NotificationFee' => '29',
                                'InterestRatePercent' => '0',
                                'NumberOfInterestFreeMonths' => 3,
                                'NumberOfPaymentFreeMonths' => 3,
                                'FromAmount' => '1000',
                                'ToAmount' => '50000',
                            )
                    )
                )
            )
        );

        $params = new PaymentPlanParamsResponse($response, false);

        return $params->campaignCodes;
    }

    protected function getInterestAndAmortizationFreeCampaignAsArray()
    {
        return array (
            'CampaignCode' => 223060,
            'Description' => 'Köp nu betala om 3 månader (räntefritt)',
            'PaymentPlanType' => 2,
            'ContractLengthInMonths' => 3,
            'MonthlyAnnuityFactor' => 1.0,
            'InitialFee' => 0.0,
            'NotificationFee' => 29.0,
            'InterestRatePercent' => 0.0,
            'NumberOfInterestFreeMonths' => 3,
            'NumberOfPaymentFreeMonths' => 3,
            'FromAmount' => 1000.0,
            'ToAmount' => 50000.0,
        );
    }

    protected function getInterestFreeCampaign()
    {
        $response = (object) array(
            "GetPaymentPlanParamsEuResult" => (object)array (
                "Accepted" => true,
                "ResultCode" => 0,
                "CampaignCodes" => (object)array(
                    "CampaignCodeInfo" => array(
                        0 =>
                            (object)array(
                                'CampaignCode' => 310012,
                                'Description' => 'Dela upp betalningen på 12 månader (räntefritt)',
                                'PaymentPlanType' => 'InterestFree',
                                'ContractLengthInMonths' => 12,
                                'MonthlyAnnuityFactor' => '0.08333',
                                'InitialFee' => '295',
                                'NotificationFee' => '35',
                                'InterestRatePercent' => '0',
                                'NumberOfInterestFreeMonths' => 12,
                                'NumberOfPaymentFreeMonths' => 0,
                                'FromAmount' => '1000',
                                'ToAmount' => '30000',
                            )
                    )
                )
            )
        );

        $params = new PaymentPlanParamsResponse($response, false);

        return $params->campaignCodes;
    }

    protected function getFinnishInterestAndAmortizationFreeCampaignAsArray()
    {
        return array (
            'CampaignCode' => 220002,
            'Description' => 'OSTA NYT, MAKSA 3 KK PÄÄSTÄ',
            'PaymentPlanType' => 2,
            'ContractLengthInMonths' => 3,
            'MonthlyAnnuityFactor' => 1,
            'InitialFee' => 0,
            'NotificationFee' => 3.95,
            'InterestRatePercent' => 0,
            'NumberOfInterestFreeMonths' => 3,
            'NumberOfPaymentFreeMonths' => 3,
            'FromAmount' => 50.0,
            'ToAmount' => 5000.0,
        );
    }

    protected function getInterestFreeCampaignAsArray()
    {
        return array (
            'CampaignCode' => 310012,
            'Description' => 'Dela upp betalningen på 12 månader (räntefritt)',
            'PaymentPlanType' => 1,
            'ContractLengthInMonths' => 12,
            'MonthlyAnnuityFactor' => 0.08333,
            'InitialFee' => 295.0,
            'NotificationFee' => 35.0,
            'InterestRatePercent' => 0.0,
            'NumberOfInterestFreeMonths' => 12,
            'NumberOfPaymentFreeMonths' => 0,
            'FromAmount' => 1000.0,
            'ToAmount' => 30000.0,
        );
    }

    protected function getFinnishInterestFreeCampaignAsArray()
    {
        return array (
            'CampaignCode' => 220001,
            'Description' => 'ERÄMAKSU 3 KK KOROTON',
            'PaymentPlanType' => 1,
            'ContractLengthInMonths' => 3,
            'MonthlyAnnuityFactor' => 0.33333,
            'InitialFee' => 0.0,
            'NotificationFee' => 0.0,
            'InterestRatePercent' => 0.0,
            'NumberOfInterestFreeMonths' => 3,
            'NumberOfPaymentFreeMonths' => 0,
            'FromAmount' => 50.0,
            'ToAmount' => 1000.0,
        );
    }

    protected function getStandardCampaign()
    {
        $response = (object) array(
            "GetPaymentPlanParamsEuResult" => (object)array (
                "Accepted" => true,
                "ResultCode" => 0,
                "CampaignCodes" => (object)array(
                    "CampaignCodeInfo" => array(
                        0 =>
                            (object)array(
                                "CampaignCode" => 213060,
                                "Description" => "Dela upp betalningen på 60 månader",
                                "PaymentPlanType" => "Standard",
                                "ContractLengthInMonths" => 60,
                                "MonthlyAnnuityFactor" => '0.02555',
                                "InitialFee" => '100',
                                "NotificationFee" => '29',
                                "InterestRatePercent" => '16.75',
                                "NumberOfInterestFreeMonths" => 3,
                                "NumberOfPaymentFreeMonths" => 3,
                                "FromAmount" => '1000',
                                "ToAmount" => '50000',
                            )
                    )
                )
            )
        );

        $params = new PaymentPlanParamsResponse($response, false);

        return $params->campaignCodes;
    }

    protected function getStandardCampaignAsArray()
    {
        return array (
            'CampaignCode' => 213060,
            'Description' => 'Dela upp betalningen på 60 månader',
            'PaymentPlanType' => 0,
            'ContractLengthInMonths' => 60,
            'MonthlyAnnuityFactor' => 0.02555,
            'InitialFee' => 100.0,
            'NotificationFee' => 29.0,
            'InterestRatePercent' => 16.75,
            'NumberOfInterestFreeMonths' => 3,
            'NumberOfPaymentFreeMonths' => 3,
            'FromAmount' => 1000.0,
            'ToAmount' => 50000.0,
        );
    }

    protected function getFinnishStandardCampaignAsArray()
    {
        return array (
            'CampaignCode' => 220012,
            'Description' => 'ERÄMAKSU 12 KK 9.8%',
            'PaymentPlanType' => 0,
            'ContractLengthInMonths' => 12,
            'MonthlyAnnuityFactor' => 0.08782,
            'InitialFee' => 29.9,
            'NotificationFee' => 8.9,
            'InterestRatePercent' => 9.8,
            'NumberOfInterestFreeMonths' => 0,
            'NumberOfPaymentFreeMonths' => 0,
            'FromAmount' => 50,
            'ToAmount' => 1000,
        );
    }

    protected function getMixedCampaigns()
    {
        $response = (object) array(
            "GetPaymentPlanParamsEuResult" => (object)array (
                "Accepted" => true,
                "ResultCode" => 0,
                "CampaignCodes" => (object)array(
                    "CampaignCodeInfo" => array(
                        0 =>
                            (object)array(
                                "CampaignCode" => 213060,
                                "Description" => "Dela upp betalningen på 60 månader",
                                "PaymentPlanType" => "Standard",
                                "ContractLengthInMonths" => 60,
                                "MonthlyAnnuityFactor" => '0.02555',
                                "InitialFee" => '100',
                                "NotificationFee" => '29',
                                "InterestRatePercent" => '16.75',
                                "NumberOfInterestFreeMonths" => 3,
                                "NumberOfPaymentFreeMonths" => 3,
                                "FromAmount" => '1000',
                                "ToAmount" => '50000',
                            ),
                        1 =>
                            (object)array(
                                'CampaignCode' => 222065,
                                'Description' => 'Vårkampanj',
                                'PaymentPlanType' => 'InterestAndAmortizationFree',
                                'ContractLengthInMonths' => 3,
                                'MonthlyAnnuityFactor' => '1',
                                'InitialFee' => '0',
                                'NotificationFee' => '0',
                                'InterestRatePercent' => '0',
                                'NumberOfInterestFreeMonths' => 3,
                                'NumberOfPaymentFreeMonths' => 3,
                                'FromAmount' => '120',
                                'ToAmount' => '30000',
                            ),
                        2 =>
                            (object)array(
                                'CampaignCode' => 222066,
                                'Description' => 'Sommarkampanj',
                                'PaymentPlanType' => 'InterestAndAmortizationFree',
                                'ContractLengthInMonths' => 3,
                                'MonthlyAnnuityFactor' => '1',
                                'InitialFee' => '0',
                                'NotificationFee' => '0',
                                'InterestRatePercent' => '0',
                                'NumberOfInterestFreeMonths' => 3,
                                'NumberOfPaymentFreeMonths' => 3,
                                'FromAmount' => '120',
                                'ToAmount' => '30000',
                            ),
                        3 =>
                            (object)array(
                                'CampaignCode' => 223060,
                                'Description' => 'Köp nu betala om 3 månader (räntefritt)',
                                'PaymentPlanType' => 'InterestAndAmortizationFree',
                                'ContractLengthInMonths' => 3,
                                'MonthlyAnnuityFactor' => '1',
                                'InitialFee' => '0',
                                'NotificationFee' => '29',
                                'InterestRatePercent' => '0',
                                'NumberOfInterestFreeMonths' => 3,
                                'NumberOfPaymentFreeMonths' => 3,
                                'FromAmount' => '1000',
                                'ToAmount' => '50000',
                            ),
                        4 =>
                            (object)array(
                                'CampaignCode' => 223065,
                                'Description' => 'Black Friday - Cyber Monday',
                                'PaymentPlanType' => 'InterestAndAmortizationFree',
                                'ContractLengthInMonths' => 3,
                                'MonthlyAnnuityFactor' => '1',
                                'InitialFee' => '0',
                                'NotificationFee' => '0',
                                'InterestRatePercent' => '0',
                                'NumberOfInterestFreeMonths' => 3,
                                'NumberOfPaymentFreeMonths' => 3,
                                'FromAmount' => '120',
                                'ToAmount' => '30000',
                            ),
                        5 =>
                            (object)array(
                                'CampaignCode' => 223066,
                                'Description' => 'Julkampanj',
                                'PaymentPlanType' => 'InterestAndAmortizationFree',
                                'ContractLengthInMonths' => 3,
                                'MonthlyAnnuityFactor' => '1',
                                'InitialFee' => '0',
                                'NotificationFee' => '0',
                                'InterestRatePercent' => '0',
                                'NumberOfInterestFreeMonths' => 3,
                                'NumberOfPaymentFreeMonths' => 3,
                                'FromAmount' => '120',
                                'ToAmount' => '30000',
                            ),
                        6 =>
                            (object)array(
                                'CampaignCode' => 310012,
                                'Description' => 'Dela upp betalningen på 12 månader (räntefritt)',
                                'PaymentPlanType' => 'InterestFree',
                                'ContractLengthInMonths' => 12,
                                'MonthlyAnnuityFactor' => '0.08333',
                                'InitialFee' => '295',
                                'NotificationFee' => '35',
                                'InterestRatePercent' => '0',
                                'NumberOfInterestFreeMonths' => 12,
                                'NumberOfPaymentFreeMonths' => 0,
                                'FromAmount' => '1000',
                                'ToAmount' => '30000',
                            ),
                        7 =>
                            (object)array(
                                'CampaignCode' => 410012,
                                'Description' => 'Dela upp betalningen på 12 månader',
                                'PaymentPlanType' => 'Standard',
                                'ContractLengthInMonths' => 12,
                                'MonthlyAnnuityFactor' => '0.09259',
                                'InitialFee' => '0',
                                'NotificationFee' => '29',
                                'InterestRatePercent' => '19.9',
                                'NumberOfInterestFreeMonths' => 0,
                                'NumberOfPaymentFreeMonths' => 0,
                                'FromAmount' => '100',
                                'ToAmount' => '30000',
                            ),
                        8 =>
                            (object)array(
                                'CampaignCode' => 410024,
                                'Description' => 'Dela upp betalningen på 24 månader',
                                'PaymentPlanType' => 'Standard',
                                'ContractLengthInMonths' => 24,
                                'MonthlyAnnuityFactor' => '0.04684',
                                'InitialFee' => '350',
                                'NotificationFee' => '35',
                                'InterestRatePercent' => '11.5',
                                'NumberOfInterestFreeMonths' => 0,
                                'NumberOfPaymentFreeMonths' => 0,
                                'FromAmount' => '1000',
                                'ToAmount' => '150000',
                            )
                    )
                )
            )
        );

        $params = new PaymentPlanParamsResponse($response, false);

        return $params->campaignCodes;
    }

    protected function getMixedCampaignsAsArray()
    {
        return  array (
            0 =>
                array (
                    'CampaignCode' => 213060,
                    'Description' => 'Dela upp betalningen på 60 månader',
                    'PaymentPlanType' => 0,
                    'ContractLengthInMonths' => 60,
                    'MonthlyAnnuityFactor' => 0.02555,
                    'InitialFee' => 100.0,
                    'NotificationFee' => 29.0,
                    'InterestRatePercent' => 16.75,
                    'NumberOfInterestFreeMonths' => 3,
                    'NumberOfPaymentFreeMonths' => 3,
                    'FromAmount' => 1000.0,
                    'ToAmount' => 50000.0,
                ),
            1 =>
                array (
                    'CampaignCode' => 222065,
                    'Description' => 'Vårkampanj',
                    'PaymentPlanType' => 2,
                    'ContractLengthInMonths' => 3,
                    'MonthlyAnnuityFactor' => 1.0,
                    'InitialFee' => 0.0,
                    'NotificationFee' => 0.0,
                    'InterestRatePercent' => 0.0,
                    'NumberOfInterestFreeMonths' => 3,
                    'NumberOfPaymentFreeMonths' => 3,
                    'FromAmount' => 120.0,
                    'ToAmount' => 30000.0,
                ),
            2 =>
                array (
                    'CampaignCode' => 222066,
                    'Description' => 'Sommarkampanj',
                    'PaymentPlanType' => 2,
                    'ContractLengthInMonths' => 3,
                    'MonthlyAnnuityFactor' => 1.0,
                    'InitialFee' => 0.0,
                    'NotificationFee' => 0.0,
                    'InterestRatePercent' => 0.0,
                    'NumberOfInterestFreeMonths' => 3,
                    'NumberOfPaymentFreeMonths' => 3,
                    'FromAmount' => 120.0,
                    'ToAmount' => 30000.0,
                ),
            3 =>
                array (
                    'CampaignCode' => 223060,
                    'Description' => 'Köp nu betala om 3 månader (räntefritt)',
                    'PaymentPlanType' => 2,
                    'ContractLengthInMonths' => 3,
                    'MonthlyAnnuityFactor' => 1.0,
                    'InitialFee' => 0.0,
                    'NotificationFee' => 29.0,
                    'InterestRatePercent' => 0.0,
                    'NumberOfInterestFreeMonths' => 3,
                    'NumberOfPaymentFreeMonths' => 3,
                    'FromAmount' => 1000.0,
                    'ToAmount' => 50000.0,
                ),
            4 =>
                array (
                    'CampaignCode' => 223065,
                    'Description' => 'Black Friday - Cyber Monday',
                    'PaymentPlanType' => 2,
                    'ContractLengthInMonths' => 3,
                    'MonthlyAnnuityFactor' => 1.0,
                    'InitialFee' => 0.0,
                    'NotificationFee' => 0.0,
                    'InterestRatePercent' => 0.0,
                    'NumberOfInterestFreeMonths' => 3,
                    'NumberOfPaymentFreeMonths' => 3,
                    'FromAmount' => 120.0,
                    'ToAmount' => 30000.0,
                ),
            5 =>
                array (
                    'CampaignCode' => 223066,
                    'Description' => 'Julkampanj',
                    'PaymentPlanType' => 2,
                    'ContractLengthInMonths' => 3,
                    'MonthlyAnnuityFactor' => 1.0,
                    'InitialFee' => 0.0,
                    'NotificationFee' => 0.0,
                    'InterestRatePercent' => 0.0,
                    'NumberOfInterestFreeMonths' => 3,
                    'NumberOfPaymentFreeMonths' => 3,
                    'FromAmount' => 120.0,
                    'ToAmount' => 30000.0,
                ),
            6 =>
                array (
                    'CampaignCode' => 310012,
                    'Description' => 'Dela upp betalningen på 12 månader (räntefritt)',
                    'PaymentPlanType' => 1,
                    'ContractLengthInMonths' => 12,
                    'MonthlyAnnuityFactor' => 0.08333,
                    'InitialFee' => 295.0,
                    'NotificationFee' => 35.0,
                    'InterestRatePercent' => 0.0,
                    'NumberOfInterestFreeMonths' => 12,
                    'NumberOfPaymentFreeMonths' => 0,
                    'FromAmount' => 1000.0,
                    'ToAmount' => 30000.0,
                ),
            7 =>
                array (
                    'CampaignCode' => 410012,
                    'Description' => 'Dela upp betalningen på 12 månader',
                    'PaymentPlanType' => 0,
                    'ContractLengthInMonths' => 12,
                    'MonthlyAnnuityFactor' => 0.09259,
                    'InitialFee' => 0.0,
                    'NotificationFee' => 29.0,
                    'InterestRatePercent' => 19.9,
                    'NumberOfInterestFreeMonths' => 0,
                    'NumberOfPaymentFreeMonths' => 0,
                    'FromAmount' => 100.0,
                    'ToAmount' => 30000.0,
                ),
            8 =>
                array (
                    'CampaignCode' => 410024,
                    'Description' => 'Dela upp betalningen på 24 månader',
                    'PaymentPlanType' => 0,
                    'ContractLengthInMonths' => 24,
                    'MonthlyAnnuityFactor' => 0.04684,
                    'InitialFee' => 350.0,
                    'NotificationFee' => 35.0,
                    'InterestRatePercent' => 11.5,
                    'NumberOfInterestFreeMonths' => 0,
                    'NumberOfPaymentFreeMonths' => 0,
                    'FromAmount' => 1000.0,
                    'ToAmount' => 150000.0,
                ),
            9 =>
                array (
                    'CampaignCode' => 996699,
                    'Description' => 'Sommarkampanj',
                    'PaymentPlanType' => 1,
                    'ContractLengthInMonths' => 6,
                    'MonthlyAnnuityFactor' => 0.33333,
                    'InitialFee' => 95.0,
                    'NotificationFee' => 0.0,
                    'InterestRatePercent' => 0.0,
                    'NumberOfInterestFreeMonths' => 6,
                    'NumberOfPaymentFreeMonths' => 3,
                    'FromAmount' => 500.0,
                    'ToAmount' => 50000.0,
                ),
        );
    }

    protected function getFinnishMixedCampaignsAsArray()
    {
        return array (
            0 =>
                array (
                    'CampaignCode' => 220001,
                    'Description' => 'ERÄMAKSU 3 KK KOROTON',
                    'PaymentPlanType' => 1,
                    'ContractLengthInMonths' => 3,
                    'MonthlyAnnuityFactor' => 0.33333,
                    'InitialFee' => 0.0,
                    'NotificationFee' => 0.0,
                    'InterestRatePercent' => 0.0,
                    'NumberOfInterestFreeMonths' => 3,
                    'NumberOfPaymentFreeMonths' => 0,
                    'FromAmount' => 50.0,
                    'ToAmount' => 1000.0,
                ),
            1 =>
                array (
                    'CampaignCode' => 220003,
                    'Description' => 'ERÄMAKSU 3 KK, 9.8%',
                    'PaymentPlanType' => 0,
                    'ContractLengthInMonths' => 3,
                    'MonthlyAnnuityFactor' => 0.33879,
                    'InitialFee' => 19.9,
                    'NotificationFee' => 8.9,
                    'InterestRatePercent' => 9.8,
                    'NumberOfInterestFreeMonths' => 0,
                    'NumberOfPaymentFreeMonths' => 0,
                    'FromAmount' => 50.0,
                    'ToAmount' => 1000.0,
                ),
            2 =>
                array (
                    'CampaignCode' => 220006,
                    'Description' => 'ERÄMAKSU 6 KK, 9.8%',
                    'PaymentPlanType' => 0,
                    'ContractLengthInMonths' => 6,
                    'MonthlyAnnuityFactor' => 0.17146,
                    'InitialFee' => 19.9,
                    'NotificationFee' => 8.9,
                    'InterestRatePercent' => 9.8,
                    'NumberOfInterestFreeMonths' => 0,
                    'NumberOfPaymentFreeMonths' => 0,
                    'FromAmount' => 50.0,
                    'ToAmount' => 1000.0,
                ),
            3 =>
                array (
                    'CampaignCode' => 220012,
                    'Description' => 'ERÄMAKSU 12 KK 9.8%',
                    'PaymentPlanType' => 0,
                    'ContractLengthInMonths' => 12,
                    'MonthlyAnnuityFactor' => 0.08782,
                    'InitialFee' => 29.9,
                    'NotificationFee' => 8.9,
                    'InterestRatePercent' => 9.8,
                    'NumberOfInterestFreeMonths' => 0,
                    'NumberOfPaymentFreeMonths' => 0,
                    'FromAmount' => 50.0,
                    'ToAmount' => 1000.0,
                ),
            4 =>
                array (
                    'CampaignCode' => 220024,
                    'Description' => 'ERÄMAKSU 24 KK, 9.8',
                    'PaymentPlanType' => 0,
                    'ContractLengthInMonths' => 24,
                    'MonthlyAnnuityFactor' => 0.04605,
                    'InitialFee' => 29.9,
                    'NotificationFee' => 8.9,
                    'InterestRatePercent' => 9.8,
                    'NumberOfInterestFreeMonths' => 0,
                    'NumberOfPaymentFreeMonths' => 0,
                    'FromAmount' => 50.0,
                    'ToAmount' => 1000.0,
                ),
            5 =>
                array (
                    'CampaignCode' => 220002,
                    'Description' => 'OSTA NYT, MAKSA 3 KK PÄÄSTÄ',
                    'PaymentPlanType' => 2,
                    'ContractLengthInMonths' => 3,
                    'MonthlyAnnuityFactor' => 1,
                    'InitialFee' => 0,
                    'NotificationFee' => 3.95,
                    'InterestRatePercent' => 0,
                    'NumberOfInterestFreeMonths' => 3,
                    'NumberOfPaymentFreeMonths' => 3,
                    'FromAmount' => 50.0,
                    'ToAmount' => 5000.0,
                ),
        );
    }

    function test_InterestAndAmortizationFreePaymentPlanCalculator_getTotalAmountToPay()
    {
        $campaign = $this->getInterestAndAmortizationFreeCampaign();
        $totalAmount =  PaymentPlanCalculator::getTotalAmountToPay($this->price, $campaign[0]);
        $this->assertEquals(11229, $totalAmount);
    }

    function test_InterestAndAmortizationFreePaymentPlanCalculator_getTotalAmountToPayAsArray()
    {
        $campaign = $this->getInterestAndAmortizationFreeCampaignAsArray();
        $totalAmount = PaymentPlanCalculator::getTotalAmountToPay($this->price, $campaign);
        $this->assertEquals(11229, $totalAmount);
    }

    function test_InterestAndAmortizationFreePaymentPlanCalculator_getTotalAmountToPayWithFinnishCampaign()
    {
        $this->price = 100;
        $campaign = $this->getFinnishInterestAndAmortizationFreeCampaignAsArray();
        $totalAmount = PaymentPlanCalculator::getTotalAmountToPay($this->price, $campaign);
        $this->assertEquals(103.95, $totalAmount);
    }

    function test_InterestAndAmortizationFreePaymentPlanCalculator_getMonthlyAmountToPay()
    {
        $campaign = $this->getInterestAndAmortizationFreeCampaign();
        $totalAmount = PaymentPlanCalculator::getMonthlyAmountToPay($this->price, $campaign[0]);
        $this->assertEquals(11229, $totalAmount);
    }

    function test_InterestAndAmortizationFreePaymentPlanCalculator_getMonthlyAmountToPayAsArray()
    {
        $campaign = $this->getInterestAndAmortizationFreeCampaignAsArray();
        $totalAmount = PaymentPlanCalculator::getMonthlyAmountToPay($this->price, $campaign);
        $this->assertEquals(11229, $totalAmount);
    }

    function test_InterestAndAmortizationFreePaymentPlanCalculator_getMonthlyAmountToPayWithFinnishCampaign()
    {
        $this->price = 100;
        $campaign = $this->getFinnishInterestAndAmortizationFreeCampaignAsArray();
        $totalAmount = PaymentPlanCalculator::getMonthlyAmountToPay($this->price, $campaign);
        $this->assertEquals(103.95, $totalAmount);
    }

    function test_InterestAndAmortizationFreePaymentPlanCalculator_getEffectiveInterestRate()
    {
        $campaign = $this->getInterestAndAmortizationFreeCampaign();
        $totalAmount = PaymentPlanCalculator::getEffectiveInterestRate($this->price, $campaign[0]);
        $this->assertEquals(1.04, $totalAmount);
    }

    function test_InterestAndAmortizationFreePaymentPlanCalculator_getEffectiveInterestRateAsArray()
    {
        $campaign = $this->getInterestAndAmortizationFreeCampaignAsArray();
        $totalAmount = PaymentPlanCalculator::getEffectiveInterestRate($this->price, $campaign);
        $this->assertEquals(1.04, $totalAmount);
    }

    function test_InterestAndAmortizationFreePaymentPlanCalculator_getEffectiveInterestRateWithFinnishCampaign()
    {
        $this->price = 100;
        $campaign = $this->getFinnishInterestAndAmortizationFreeCampaignAsArray();
        $totalAmount = PaymentPlanCalculator::getEffectiveInterestRate($this->price, $campaign, 2);
        $this->assertEquals(16.77, $totalAmount);
    }

    function test_InterestFreePaymentPlanCalculator_getTotalAmountToPay()
    {
        $campaign = $this->getInterestFreeCampaign();
        $totalAmount =  PaymentPlanCalculator::getTotalAmountToPay($this->price, $campaign[0]);
        $this->assertEquals(11915, $totalAmount);
    }

    function test_InterestFreePaymentPlanCalculator_getTotalAmountToPayWithFinnishCampaign()
    {
        $this->price = 100;
        $campaign = $this->getFinnishInterestFreeCampaignAsArray();
        $totalAmount =  PaymentPlanCalculator::getTotalAmountToPay($this->price, $campaign, 2);
        $this->assertEquals(100, $totalAmount);
    }

    function test_InterestFreePaymentPlanCalculator_getMonthlyAmountToPay()
    {
        $campaign = $this->getInterestFreeCampaign();
        $totalAmount = PaymentPlanCalculator::getMonthlyAmountToPay($this->price, $campaign[0]);
        $this->assertEquals(993, $totalAmount);
    }

    function test_InterestFreePaymentPlanCalculator_getMonthlyAmountToPayWithFinnishCampaign()
    {
        $this->price = 100;
        $campaign = $this->getFinnishStandardCampaignAsArray();
        $totalAmount = PaymentPlanCalculator::getMonthlyAmountToPay($this->price, $campaign, 2);
        $this->assertEquals(20.17, $totalAmount);
    }

    function test_InterestFreePaymentPlanCalculator_getEffectiveInterestRate()
    {
        $campaign = $this->getInterestFreeCampaign();
        $totalAmount = PaymentPlanCalculator::getEffectiveInterestRate($this->price, $campaign[0]);
        $this->assertEquals(12.44, $totalAmount);
    }

    function test_InterestFreePaymentPlanCalculator_getEffectiveInterestRateWithFinnishCampaign()
    {
        $this->price = 100;
        $campaign = $this->getFinnishInterestFreeCampaignAsArray();
        $totalAmount = PaymentPlanCalculator::getEffectiveInterestRate($this->price, $campaign, 2);
        $this->assertEquals(0, $totalAmount);
    }

    function test_StandardPaymentPlanCalculator_getTotalAmountToPay()
    {
        $campaign = $this->getStandardCampaign();
        $totalAmount =  PaymentPlanCalculator::getTotalAmountToPay($this->price, $campaign[0]);
        $this->assertEquals(18067, $totalAmount);
    }

    function test_StandardPaymentPlanCalculator_getTotalAmountToPayWithFinnishCampaign()
    {
        $this->price = 100;
        $campaign = $this->getFinnishStandardCampaignAsArray();
        $totalAmount =  PaymentPlanCalculator::getTotalAmountToPay($this->price, $campaign, 2);
        $this->assertEquals(242.09, $totalAmount);
    }

    function test_StandardPaymentPlanCalculator_getMonthlyAmountToPay()
    {
        $campaign = $this->getStandardCampaign();
        $totalAmount = PaymentPlanCalculator::getMonthlyAmountToPay($this->price, $campaign[0]);
        $this->assertEquals(317, $totalAmount);
    }

    function test_StandardPaymentPlanCalculator_getMonthlyAmountToPayWithFinnishCampaign()
    {
        $this->price = 100;
        $campaign = $this->getFinnishStandardCampaignAsArray();
        $totalAmount = PaymentPlanCalculator::getMonthlyAmountToPay($this->price, $campaign, 2);
        $this->assertEquals(20.17, $totalAmount);
    }

    function test_StandardPaymentPlanCalculator_getEffectiveInterestRate()
    {
        $campaign = $this->getStandardCampaign();
        $totalAmount = PaymentPlanCalculator::getEffectiveInterestRate($this->price, $campaign[0]);
        $this->assertEquals(21.33, $totalAmount);
    }

    function test_StandardPaymentPlanCalculator_getEffectiveInterestRateWithFinnishCampaign()
    {
        $this->price = 100;
        $campaign = $this->getFinnishStandardCampaignAsArray();
        $totalAmount = PaymentPlanCalculator::getEffectiveInterestRate($this->price, $campaign, 2);
        $this->assertEquals(898.33, $totalAmount);
    }

    function test_PaymentPlanCalculator_getTotalAmountToPayFromCampaigns()
    {
        $campaigns = $this->getMixedCampaigns();
        $totalAmount = PaymentPlanCalculator::getTotalAmountToPayFromCampaigns($this->price, $campaigns);
        $this->assertEquals(18067, $totalAmount[0]['totalAmountToPay']);
        $this->assertEquals(11200, $totalAmount[1]['totalAmountToPay']);
        $this->assertEquals(11200, $totalAmount[2]['totalAmountToPay']);
        $this->assertEquals(11229, $totalAmount[3]['totalAmountToPay']);
        $this->assertEquals(11200, $totalAmount[4]['totalAmountToPay']);
        $this->assertEquals(11200, $totalAmount[5]['totalAmountToPay']);
        $this->assertEquals(11915, $totalAmount[6]['totalAmountToPay']);
        $this->assertEquals(12792, $totalAmount[7]['totalAmountToPay']);
        $this->assertEquals(13781, $totalAmount[8]['totalAmountToPay']);
    }

    function test_PaymentPlanCalculator_getMonthlyAmountToPayFromCampaigns()
    {
        $campaigns = $this->getMixedCampaigns();
        $totalAmount = PaymentPlanCalculator::getMonthlyAmountToPayFromCampaigns($this->price, $campaigns);
        $this->assertEquals(317, $totalAmount[0]['monthlyAmountToPay']);
        $this->assertEquals(11200, $totalAmount[1]['monthlyAmountToPay']);
        $this->assertEquals(11200, $totalAmount[2]['monthlyAmountToPay']);
        $this->assertEquals(11229, $totalAmount[3]['monthlyAmountToPay']);
        $this->assertEquals(11200, $totalAmount[4]['monthlyAmountToPay']);
        $this->assertEquals(11200, $totalAmount[5]['monthlyAmountToPay']);
        $this->assertEquals(993, $totalAmount[6]['monthlyAmountToPay']);
        $this->assertEquals(1066, $totalAmount[7]['monthlyAmountToPay']);
        $this->assertEquals(574, $totalAmount[8]['monthlyAmountToPay']);
    }

    function test_PaymentPlanCalculator_getEffectiveInterestRateFromCampaigns()
    {
        $campaigns = $this->getMixedCampaigns();
        $totalAmount = PaymentPlanCalculator::getEffectiveInterestRateFromCampaigns($this->price, $campaigns);
        $this->assertEquals(21.33, $totalAmount[0]['effectiveInterestRate']);
        $this->assertEquals(0, $totalAmount[1]['effectiveInterestRate']);
        $this->assertEquals(0, $totalAmount[2]['effectiveInterestRate']);
        $this->assertEquals(1.04, $totalAmount[3]['effectiveInterestRate']);
        $this->assertEquals(0, $totalAmount[4]['effectiveInterestRate']);
        $this->assertEquals(0, $totalAmount[5]['effectiveInterestRate']);
        $this->assertEquals(12.44, $totalAmount[6]['effectiveInterestRate']);
        $this->assertEquals(28.44, $totalAmount[7]['effectiveInterestRate']);
        $this->assertEquals(23.65, $totalAmount[8]['effectiveInterestRate']);
    }

    function test_PaymentPlanCalculator_getTotalAmountToPayFromCampaignsAsArray()
    {
        $campaigns = $this->getMixedCampaignsAsArray();
        $totalAmount = PaymentPlanCalculator::getTotalAmountToPayFromCampaigns($this->price, $campaigns);
        $this->assertEquals(18067, $totalAmount[0]['TotalAmountToPay']);
        $this->assertEquals(11200, $totalAmount[1]['TotalAmountToPay']);
        $this->assertEquals(11200, $totalAmount[2]['TotalAmountToPay']);
        $this->assertEquals(11229, $totalAmount[3]['TotalAmountToPay']);
        $this->assertEquals(11200, $totalAmount[4]['TotalAmountToPay']);
        $this->assertEquals(11200, $totalAmount[5]['TotalAmountToPay']);
        $this->assertEquals(11915, $totalAmount[6]['TotalAmountToPay']);
        $this->assertEquals(12792, $totalAmount[7]['TotalAmountToPay']);
        $this->assertEquals(13781, $totalAmount[8]['TotalAmountToPay']);
    }

    function test_PaymentPlanCalculator_getTotalAmountToPayFromCampaignsAsArray_ReturnOnlyValidCampaigns()
    {
        $this->price = 100;
        $campaigns = $this->getMixedCampaignsAsArray();
        $totalAmount = PaymentPlanCalculator::getTotalAmountToPayFromCampaigns($this->price, $campaigns);
        $this->assertEquals(1, count($totalAmount));
    }

    function test_PaymentPlanCalculator_getTotalAmountToPayFromCampaignsAsArray_ReturnZeroCampaigns()
    {
        $this->price = 1;
        $campaigns = $this->getMixedCampaignsAsArray();
        $totalAmount = PaymentPlanCalculator::getTotalAmountToPayFromCampaigns($this->price, $campaigns);
        $this->assertEquals(0, count($totalAmount));
    }

    function test_PaymentPlanCalculator_getMonthlyAmountToPayFromCampaignsAsArray()
    {
        $campaigns = $this->getMixedCampaignsAsArray();
        $totalAmount = PaymentPlanCalculator::getMonthlyAmountToPayFromCampaigns($this->price, $campaigns);
        $this->assertEquals(317, $totalAmount[0]['MonthlyAmountToPay']);
        $this->assertEquals(11200, $totalAmount[1]['MonthlyAmountToPay']);
        $this->assertEquals(11200, $totalAmount[2]['MonthlyAmountToPay']);
        $this->assertEquals(11229, $totalAmount[3]['MonthlyAmountToPay']);
        $this->assertEquals(11200, $totalAmount[4]['MonthlyAmountToPay']);
        $this->assertEquals(11200, $totalAmount[5]['MonthlyAmountToPay']);
        $this->assertEquals(993, $totalAmount[6]['MonthlyAmountToPay']);
        $this->assertEquals(1066, $totalAmount[7]['MonthlyAmountToPay']);
        $this->assertEquals(574, $totalAmount[8]['MonthlyAmountToPay']);
    }

    function test_PaymentPlanCalculator_getMonthlyAmountToPayFromCampaignsAsArray_ReturnOnlyValidCampaigns()
    {
        $this->price = 100;
        $campaigns = $this->getMixedCampaignsAsArray();
        $monthlyAmount = PaymentPlanCalculator::getMonthlyAmountToPayFromCampaigns($this->price, $campaigns);
        $this->assertEquals(1, count($monthlyAmount));
    }

    function test_PaymentPlanCalculator_getMonthlyAmountToPayFromCampaignsAsArray_ReturnZeroCampaigns()
    {
        $this->price = 1;
        $campaigns = $this->getMixedCampaignsAsArray();
        $monthlyAmount = PaymentPlanCalculator::getMonthlyAmountToPayFromCampaigns($this->price, $campaigns);
        $this->assertEquals(0, count($monthlyAmount));
    }

    function test_PaymentPlanCalculator_getEffectiveInterestRateFromCampaignsAsArray()
    {
        $campaigns = $this->getMixedCampaignsAsArray();
        $totalAmount = PaymentPlanCalculator::getEffectiveInterestRateFromCampaigns($this->price, $campaigns);
        $this->assertEquals(21.33, $totalAmount[0]['EffectiveInterestRate']);
        $this->assertEquals(0, $totalAmount[1]['EffectiveInterestRate']);
        $this->assertEquals(0, $totalAmount[2]['EffectiveInterestRate']);
        $this->assertEquals(1.04, $totalAmount[3]['EffectiveInterestRate']);
        $this->assertEquals(0, $totalAmount[4]['EffectiveInterestRate']);
        $this->assertEquals(0, $totalAmount[5]['EffectiveInterestRate']);
        $this->assertEquals(12.44, $totalAmount[6]['EffectiveInterestRate']);
        $this->assertEquals(28.44, $totalAmount[7]['EffectiveInterestRate']);
        $this->assertEquals(23.65, $totalAmount[8]['EffectiveInterestRate']);
    }

    function test_PaymentPlanCalculator_getEffectiveInterestRateFromCampaignsAsArray_ReturnOnlyValidCampaigns()
    {
        $this->price = 100;
        $campaigns = $this->getMixedCampaignsAsArray();
        $effectiveInterestRate = PaymentPlanCalculator::getEffectiveInterestRateFromCampaigns($this->price, $campaigns);
        $this->assertEquals(1, count($effectiveInterestRate));
    }

    function test_PaymentPlanCalculator_getEffectiveInterestRateFromCampaignsAsArray_ReturnZeroCampaigns()
    {
        $this->price = 1;
        $campaigns = $this->getMixedCampaignsAsArray();
        $effectiveInterestRate = PaymentPlanCalculator::getEffectiveInterestRateFromCampaigns($this->price, $campaigns);
        $this->assertEquals(0, count($effectiveInterestRate));
    }

    function test_PaymentPlanCalculator_getTotalAmountToPayFromCampaignsWithFinnishCampaign()
    {
        $this->price = 100;
        $campaigns = $this->getFinnishMixedCampaignsAsArray();
        $totalAmount = PaymentPlanCalculator::getTotalAmountToPayFromCampaigns($this->price, $campaigns, 2);
        $this->assertEquals(100, $totalAmount[0]['TotalAmountToPay']);
        $this->assertEquals(148.24, $totalAmount[1]['TotalAmountToPay']);
        $this->assertEquals(176.18, $totalAmount[2]['TotalAmountToPay']);
        $this->assertEquals(242.09, $totalAmount[3]['TotalAmountToPay']);
        $this->assertEquals(354.03, $totalAmount[4]['TotalAmountToPay']);
        $this->assertEquals(103.95, $totalAmount[5]['TotalAmountToPay']);
    }

    function test_PaymentPlanCalculator_getMonthlyAmountToPayFromCampaignsWithFinnishCampaign()
    {
        $this->price = 100;
        $campaigns = $this->getFinnishMixedCampaignsAsArray();
        $totalAmount = PaymentPlanCalculator::getMonthlyAmountToPayFromCampaigns($this->price, $campaigns, 2);
        $this->assertEquals(33.33, $totalAmount[0]['MonthlyAmountToPay']);
        $this->assertEquals(49.41, $totalAmount[1]['MonthlyAmountToPay']);
        $this->assertEquals(29.36, $totalAmount[2]['MonthlyAmountToPay']);
        $this->assertEquals(20.17, $totalAmount[3]['MonthlyAmountToPay']);
        $this->assertEquals(14.75, $totalAmount[4]['MonthlyAmountToPay']);
        $this->assertEquals(103.95, $totalAmount[5]['MonthlyAmountToPay']);
    }

    function test_PaymentPlanCalculator_getEffectiveInterestRateFromCampaignsWithFinnishCampaign()
    {
        $this->price = 100;
        $campaigns = $this->getFinnishMixedCampaignsAsArray();
        $totalAmount = PaymentPlanCalculator::getEffectiveInterestRateFromCampaigns($this->price, $campaigns, 2);
        $this->assertEquals(0, $totalAmount[0]['EffectiveInterestRate']);
        $this->assertEquals(1300.39, $totalAmount[1]['EffectiveInterestRate']);
        $this->assertEquals(930.86, $totalAmount[2]['EffectiveInterestRate']);
        $this->assertEquals(898.33, $totalAmount[3]['EffectiveInterestRate']);
        $this->assertEquals(611.36, $totalAmount[4]['EffectiveInterestRate']);
        $this->assertEquals(16.77, $totalAmount[5]['EffectiveInterestRate']);
    }

    function test_PaymentPlanCalculator_getAllCalculations()
    {
        $campaign = $this->getStandardCampaign();
        $campaign = PaymentPlanCalculator::getAllCalculations($this->price, $campaign[0]);
        $this->assertEquals(21.33, $campaign['effectiveInterestRate']);
        $this->assertEquals(317, $campaign['monthlyAmountToPay']);
        $this->assertEquals(18067, $campaign['totalAmountToPay']);
    }

    function test_PaymentPlanCalculator_getAllCalculationsAsArray()
    {
        $campaign = $this->getStandardCampaignAsArray();
        $campaign = PaymentPlanCalculator::getAllCalculations($this->price, $campaign);
        $this->assertEquals(21.33, $campaign['EffectiveInterestRate']);
        $this->assertEquals(317, $campaign['MonthlyAmountToPay']);
        $this->assertEquals(18067, $campaign['TotalAmountToPay']);
    }

    function test_PaymentPlanCalculator_getAllCalculationsAsArrayWithFinnishCampaign()
    {
        $this->price = 100;
        $campaign = $this->getFinnishStandardCampaignAsArray();
        $campaign = PaymentPlanCalculator::getAllCalculations($this->price, $campaign, 2);
        $this->assertEquals(898.33, $campaign['EffectiveInterestRate']);
        $this->assertEquals(20.17, $campaign['MonthlyAmountToPay']);
        $this->assertEquals(242.09, $campaign['TotalAmountToPay']);
    }

    function test_PaymentPlanCalculator_getAllCalculationsFromCampaigns()
    {
        $campaigns = $this->getMixedCampaigns();
        $campaigns = PaymentPlanCalculator::getAllCalculationsFromCampaigns($this->price, $campaigns);

        // Campaign 0
        $this->assertEquals(21.33, $campaigns[0]['effectiveInterestRate']);
        $this->assertEquals(317, $campaigns[0]['monthlyAmountToPay']);
        $this->assertEquals(18067, $campaigns[0]['totalAmountToPay']);

        // Campaign 1
        $this->assertEquals(0, $campaigns[1]['effectiveInterestRate']);
        $this->assertEquals(11200, $campaigns[1]['monthlyAmountToPay']);
        $this->assertEquals(11200, $campaigns[1]['totalAmountToPay']);

        // Campaign 2
        $this->assertEquals(0, $campaigns[2]['effectiveInterestRate']);
        $this->assertEquals(11200, $campaigns[2]['monthlyAmountToPay']);
        $this->assertEquals(11200, $campaigns[2]['totalAmountToPay']);

        // Campaign 3
        $this->assertEquals(1.04, $campaigns[3]['effectiveInterestRate']);
        $this->assertEquals(11229, $campaigns[3]['monthlyAmountToPay']);
        $this->assertEquals(11229, $campaigns[3]['totalAmountToPay']);

        // Campaign 4
        $this->assertEquals(0, $campaigns[4]['effectiveInterestRate']);
        $this->assertEquals(11200, $campaigns[4]['monthlyAmountToPay']);
        $this->assertEquals(11200, $campaigns[4]['totalAmountToPay']);

        // Campaign 5
        $this->assertEquals(0, $campaigns[5]['effectiveInterestRate']);
        $this->assertEquals(11200, $campaigns[5]['monthlyAmountToPay']);
        $this->assertEquals(11200, $campaigns[5]['totalAmountToPay']);

        // Campaign 6
        $this->assertEquals(12.44, $campaigns[6]['effectiveInterestRate']);
        $this->assertEquals(993, $campaigns[6]['monthlyAmountToPay']);
        $this->assertEquals(11915, $campaigns[6]['totalAmountToPay']);

        // Campaign 7
        $this->assertEquals(28.44, $campaigns[7]['effectiveInterestRate']);
        $this->assertEquals(1066, $campaigns[7]['monthlyAmountToPay']);
        $this->assertEquals(12792, $campaigns[7]['totalAmountToPay']);

        // Campaign 8
        $this->assertEquals(23.65, $campaigns[8]['effectiveInterestRate']);
        $this->assertEquals(574, $campaigns[8]['monthlyAmountToPay']);
        $this->assertEquals(13781, $campaigns[8]['totalAmountToPay']);
    }

    function test_PaymentPlanCalculator_getAllCalculationsFromCampaignsAsArray()
    {
        $campaigns = $this->getMixedCampaignsAsArray();
        $campaigns = PaymentPlanCalculator::getAllCalculationsFromCampaigns($this->price, $campaigns);

        // Campaign 0
        $this->assertEquals(21.33, $campaigns[0]['EffectiveInterestRate']);
        $this->assertEquals(317, $campaigns[0]['MonthlyAmountToPay']);
        $this->assertEquals(18067, $campaigns[0]['TotalAmountToPay']);

        // Campaign 1
        $this->assertEquals(0, $campaigns[1]['EffectiveInterestRate']);
        $this->assertEquals(11200, $campaigns[1]['MonthlyAmountToPay']);
        $this->assertEquals(11200, $campaigns[1]['TotalAmountToPay']);

        // Campaign 2
        $this->assertEquals(0, $campaigns[2]['EffectiveInterestRate']);
        $this->assertEquals(11200, $campaigns[2]['MonthlyAmountToPay']);
        $this->assertEquals(11200, $campaigns[2]['TotalAmountToPay']);

        // Campaign 3
        $this->assertEquals(1.04, $campaigns[3]['EffectiveInterestRate']);
        $this->assertEquals(11229, $campaigns[3]['MonthlyAmountToPay']);
        $this->assertEquals(11229, $campaigns[3]['TotalAmountToPay']);

        // Campaign 4
        $this->assertEquals(0, $campaigns[4]['EffectiveInterestRate']);
        $this->assertEquals(11200, $campaigns[4]['MonthlyAmountToPay']);
        $this->assertEquals(11200, $campaigns[4]['TotalAmountToPay']);

        // Campaign 5
        $this->assertEquals(0, $campaigns[5]['EffectiveInterestRate']);
        $this->assertEquals(11200, $campaigns[5]['MonthlyAmountToPay']);
        $this->assertEquals(11200, $campaigns[5]['TotalAmountToPay']);

        // Campaign 6
        $this->assertEquals(12.44, $campaigns[6]['EffectiveInterestRate']);
        $this->assertEquals(993, $campaigns[6]['MonthlyAmountToPay']);
        $this->assertEquals(11915, $campaigns[6]['TotalAmountToPay']);

        // Campaign 7
        $this->assertEquals(28.44, $campaigns[7]['EffectiveInterestRate']);
        $this->assertEquals(1066, $campaigns[7]['MonthlyAmountToPay']);
        $this->assertEquals(12792, $campaigns[7]['TotalAmountToPay']);

        // Campaign 8
        $this->assertEquals(23.65, $campaigns[8]['EffectiveInterestRate']);
        $this->assertEquals(574, $campaigns[8]['MonthlyAmountToPay']);
        $this->assertEquals(13781, $campaigns[8]['TotalAmountToPay']);
    }

    function test_PaymentPlanCalculator_getAllCalculationsFromCampaignsAsArray_ReturnOnlyValidCampaigns()
    {
        $this->price = 100;
        $campaigns = $this->getMixedCampaignsAsArray();
        $allCalculations = PaymentPlanCalculator::getAllCalculationsFromCampaigns($this->price, $campaigns);
        $this->assertEquals(1, count($allCalculations));
    }

    function test_PaymentPlanCalculator_getAllCalculationsFromCampaignsAsArray_ReturnZeroCampaigns()
    {
        $this->price = 1;
        $campaigns = $this->getMixedCampaignsAsArray();
        $allCalculations = PaymentPlanCalculator::getAllCalculationsFromCampaigns($this->price, $campaigns);
        $this->assertEquals(0, count($allCalculations));
    }

    function test_PaymentPlanCalculator_getAllCalculationsFromCampaignsAsArrayWithFinnishCampaign()
    {
        $this->price = 100;
        $campaigns = $this->getFinnishMixedCampaignsAsArray();
        $campaigns = PaymentPlanCalculator::getAllCalculationsFromCampaigns($this->price, $campaigns, 2);

        // Campaign 0
        $this->assertEquals(0, $campaigns[0]['EffectiveInterestRate']);
        $this->assertEquals(33.33, $campaigns[0]['MonthlyAmountToPay']);
        $this->assertEquals(100, $campaigns[0]['TotalAmountToPay']);

        // Campaign 1
        $this->assertEquals(1300.39, $campaigns[1]['EffectiveInterestRate']);
        $this->assertEquals(49.41, $campaigns[1]['MonthlyAmountToPay']);
        $this->assertEquals(148.24, $campaigns[1]['TotalAmountToPay']);

        // Campaign 2
        $this->assertEquals(930.86, $campaigns[2]['EffectiveInterestRate']);
        $this->assertEquals(29.36, $campaigns[2]['MonthlyAmountToPay']);
        $this->assertEquals(176.18, $campaigns[2]['TotalAmountToPay']);

        // Campaign 3
        $this->assertEquals(898.33, $campaigns[3]['EffectiveInterestRate']);
        $this->assertEquals(20.17, $campaigns[3]['MonthlyAmountToPay']);
        $this->assertEquals(242.09, $campaigns[3]['TotalAmountToPay']);

        // Campaign 4
        $this->assertEquals(611.36, $campaigns[4]['EffectiveInterestRate']);
        $this->assertEquals(14.75, $campaigns[4]['MonthlyAmountToPay']);
        $this->assertEquals(354.03, $campaigns[4]['TotalAmountToPay']);

        // Campaign 5
        $this->assertEquals(16.77, $campaigns[5]['EffectiveInterestRate']);
        $this->assertEquals(103.95, $campaigns[5]['MonthlyAmountToPay']);
        $this->assertEquals(103.95, $campaigns[5]['TotalAmountToPay']);
    }

    function test_PaymentPlanCalculator_convertFromCheckoutArray()
    {
        $arr = $this->getInterestAndAmortizationFreeCampaignAsArray();
        $class = new ReflectionClass('\Svea\WebPay\Helper\PaymentPlanHelper\PaymentPlanCalculator');
        $method = $class->getMethod('convertFromCheckoutArray');
        $method->setAccessible(true);
        $convertedArray = $method->invoke(null, $arr);

        $this->assertEquals(array_key_exists("campaignCode", $convertedArray), true);
        $this->assertEquals(array_key_exists("description", $convertedArray), true);
        $this->assertEquals(array_key_exists("toAmount", $convertedArray), true);
        $this->assertEquals($convertedArray['checkout'], true);
        $this->assertEquals($convertedArray['paymentPlanType'], "InterestAndAmortizationFree");
    }

    function test_PaymentPlanCalculator_convertToCheckoutArray()
    {
        $arr = $this->getInterestAndAmortizationFreeCampaignAsArray();
        $class = new ReflectionClass('\Svea\WebPay\Helper\PaymentPlanHelper\PaymentPlanCalculator');
        $methodConvertFrom = $class->getMethod('convertFromCheckoutArray');
        $methodConvertFrom->setAccessible(true);
        $convertedFromCheckoutArray = $methodConvertFrom->invoke(null, $arr);

        $methodConvertTo = $class->getMethod('convertToCheckoutArray');
        $methodConvertTo->setAccessible(true);
        $convertedToCheckoutArray = $methodConvertTo->invoke(null, $convertedFromCheckoutArray);

        $this->assertEquals(array_key_exists("CampaignCode", $convertedToCheckoutArray), true);
        $this->assertEquals($convertedToCheckoutArray['PaymentPlanType'], 2);
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage paymentPlanType not recognized
     */
    function test_InterestAndAmortizationFreePaymentPlanCalculator_getTotalAmountToPay_invalid_paymentPlanType()
    {
        $campaign = $this->getStandardCampaign();
        $campaign['paymentPlanType'] = "invalid";
        PaymentPlanCalculator::getTotalAmountToPay($this->price, $campaign);
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage Monthly payment can not be below 0
     */
    function test_EffectiveInterestRateCalculator_invalid_monthlyPayment()
    {
        $calculator = new EffectiveInterestRateCalculator(10000.00);

        $calculator->calculate(10000, 100, -1, 12, 0);
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage Contract length must be at least 1 month
     */
    function test_EffectiveInterestRateCalculator_invalid_contractLength()
    {
        $calculator = new EffectiveInterestRateCalculator(10000.00);

        $calculator->calculate(10000, 100, 100, 0, 0);
    }

}