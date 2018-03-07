<?php //
/**
 * Example of how to fetch available part payment campaigns
 *
 * @author Fredrik Sundell for Svea\WebPay
 */

require_once '../../vendor/autoload.php';

use Svea\WebPay\WebPay;
use Svea\WebPay\Config\ConfigurationService;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

// get config object
$myConfig = ConfigurationService::getTestConfig();
try {
    $request = WebPay::checkout($myConfig);

    $presetValueIsCompany = \Svea\WebPay\WebPayItem::presetValue()
        ->setTypeName(\Svea\WebPay\Checkout\Model\PresetValue::IS_COMPANY)
        ->setValue(false)
        ->setIsReadonly(true);

    $request->setCountryCode('SE')
        ->addPresetValue($presetValueIsCompany);

    $response = $request->getAvailablePartPaymentCampaigns();
    echo "<pre>" . print_r($response, true) . "</pre>";
} catch (\Exception $e) {
    echo "<pre>" . print_r($e->getMessage(), true) . "</pre>";
}