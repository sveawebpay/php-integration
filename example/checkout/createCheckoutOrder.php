<?php //
/**
 * example file, how to create an checkout order request
 *
 * @author Savo Garovic and Janko Stevanovic  for Svea Svea\WebPay\WebPay
 */

require_once '../../vendor/autoload.php';

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Checkout\Model\PresetValue;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

// get config object
$myConfig = ConfigurationService::getTestConfig();

$locale = 'sv-Se';

$orderBuilder = WebPay::checkout($myConfig);

$orderBuilder->setCountryCode('SE')// customer country, we recommend basing this on the customer billing address
->setCurrency('SEK')
    ->setClientOrderNumber(rand(270000, 670000))
    ->setCheckoutUri('http://localhost:51925/')
    ->setConfirmationUri('http://localhost:51925/checkout/confirm')
    ->setPushUri('https://svea.com/push.aspx?sid=123&svea_order=123')
    ->setTermsUri('http://localhost:51898/terms')
    ->setLocale($locale);

$presetPhoneNumber = WebPayItem::presetValue()
    ->setTypeName(\Svea\WebPay\Checkout\Model\PresetValue::PHONE_NUMBER)
    ->setValue('+381212121')
    ->setIsReadonly(true);

$presetPostalCode = WebPayItem::presetValue()
    ->setTypeName(\Svea\WebPay\Checkout\Model\PresetValue::POSTAL_CODE)
    ->setValue('11000')
    ->setIsReadonly(false);


$orderBuilder->addPresetValue($presetPhoneNumber);
$orderBuilder->addPresetValue($presetPostalCode);


//Svea\WebPay\WebPayItem::fixedDiscount()->setAmountIncVat(10); // this is 10 $ fir example, not percent

// create and add items to order
$firstBoughtItem = WebPayItem::orderRow()
    ->setAmountIncVat(100.00)// - required
    ->setVatPercent(25)// - required
    ->setQuantity(1)
    ->setArticleNumber('123')
//    ->setAmountExVat(12.32)   // - this action is not allowed for checkout
    ->setTemporaryReference('230')
    ->setName('Fork');

$secondBoughtItem = WebPayItem::orderRow()
    ->setAmountIncVat(10.00)
    ->setVatPercent(25)
    ->setQuantity(2)
    ->setDescription('Korv med bröd')
    ->setArticleNumber('321')
    ->setTemporaryReference('231')
    ->setName('Fork');

$discountItem = WebPayItem::fixedDiscount()
    ->setName('Promo coupon')
    ->setVatPercent(25)
    ->setTemporaryReference('123')
//    ->setAmountExVat(12.32)   // - this action is not allowed for checkout
    ->setAmountIncVat(20.00);

$shippingItem = WebPayItem::shippingFee()
    ->setAmountIncVat(17.60)
    ->setVatPercent(25)
    ->setTemporaryReference('123')
//    ->setAmountExVat(25.32)   // - this action is not allowed for checkout
    ->setName('incvatShippingFee');

$orderBuilder->addOrderRow($firstBoughtItem);
$orderBuilder->addOrderRow($secondBoughtItem);
$orderBuilder->addDiscount($discountItem);
$orderBuilder->addFee($shippingItem);

try {
    $response = $orderBuilder->createOrder();
    print_r($response);
} catch (\Exception $e) {
    echo "<pre>" . print_r($e->getMessage(), true) . "</pre>";
}
