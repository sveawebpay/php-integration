<?php

require_once '../../vendor/autoload.php';

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Config\ConfigurationService;

error_reporting( E_ALL );
ini_set('display_errors', 'On');

$customer = WebPayItem::individualCustomer()
    ->setNationalIdNumber("194605092222")
    ->setBirthDate(1986, 03, 31)
    ->setName("Janko", "Stevanovic")
    ->setStreetAddress("Neka tamo", 1)
    ->setCoAddress("c/o BB, Batajnica")
    ->setLocality("Okrug Beograda")
    ->setEmail('batajarules@svea.com')
    ->setZipCode("99999");

$orderObject = WebPay::createOrder(ConfigurationService::getDefaultConfig())
    ->addOrderRow(WebPayItem::orderRow()
        ->setQuantity(1)
        ->setAmountIncVat(1000)
        ->setVatPercent(25.00)
        ->setArticleNumber('Cowboy Hat')
        ->setDescription('Some desc for Cowboy Hat')
    )
    ->addOrderRow(WebPayItem::orderRow()
        ->setQuantity(3)
        ->setAmountIncVat(500.33)
        ->setVatPercent(25)
        ->setArticleNumber('Cowboy lasso')
        ->setDescription('Some desc for Cowboy Lasso twine')
    )
    ->addCustomerDetails($customer)
    ->setCountryCode("SE")
    ->setCurrency("SEK")
    ->setOrderDate(date('c'))
    ->useAccountCredit("111111")
    ->doRequest();


var_dump($orderObject);
