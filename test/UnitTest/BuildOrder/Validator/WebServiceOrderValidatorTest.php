<?php

namespace Svea\WebPay\Test\UnitTest\BuildOrder\Validator;

use Exception;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\Config\ConfigurationService;

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class WebServiceOrderValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : Customer values are required for Invoice and PaymentPlan orders.
     *
     * function te_stFailOnMissingCustomerIdentity() {
     * $builder = \Svea\WebPay\WebPay::createOrder($config);
     * $order = $builder
     * ->beginOrderRow()
     * ->setAmountExVat(100)
     * ->setVatPercent(20)
     * ->setQuantity(1)
     * ->endOrderRow()
     * ->setCountryCode("SE")
     * ->useInvoicePayment();
     * $order->prepareRequest();
     *
     * }
     *
     */

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -duplicated value : Customer is either an individual or a company. You can not use function setNationalIdNumber() in combination with setNationalIdNumber() or setVatNumber().
     */
    public function t_estFailOnDoubleIdentity()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(TestUtil::createHostedOrderRow())
            ->setCountryCode("SE")
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->addCustomerDetails(WebPayItem::companyCustomer()->setNationalIdNumber(4608142222))
            ->useInvoicePayment();

        $order->prepareRequest();
    }

    /**
     * Use to get paymentPlanParams to be able to test PaymentPlanRequest
     * @return type
     */
    public function getGetPaymentPlanParamsForTesting($config)
    {
        $addressRequest = WebPay::getPaymentPlanParams($config);
        $response = $addressRequest
            ->setCountryCode("SE")
            ->doRequest();

        return $response->campaignCodes[0]->campaignCode;
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -Wrong customer type : PaymentPlanPayment not allowed for Company customer.
     */
    public function testFailOnCompanyPaymentPlanPayment()
    {
        $config = ConfigurationService::getDefaultConfig();
        $code = $this->getGetPaymentPlanParamsForTesting($config);
        $builder = WebPay::CreateOrder($config);
        $order = $builder
            ->addOrderRow(TestUtil::createHostedOrderRow())
            ->setCountryCode("SE")
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->addCustomerDetails(WebPayItem::companyCustomer()->setNationalIdNumber(4608142222))
            ->usePaymentPlanPayment('5454');

        $order = $order->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -not valid : Given countrycode does not exist in our system.
     */
    public function testFailOnBadCountryCode()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(TestUtil::createHostedOrderRow())
            ->setCountryCode("ZZ")
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(111111))
            ->useInvoicePayment();

        $order->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : CountryCode is required. Use function setCountryCode().
     */
    public function testFailOnMissingCountryCode()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(TestUtil::createHostedOrderRow())
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(111111))
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->useInvoicePayment();

        $order->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : NationalIdNumber is required for individual customers when countrycode is SE, NO, DK or FI. Use function setNationalIdNumber().
     */
    public function testFailOnMissingNationalIdNumberForSeOrder()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->setCountryCode("SE")
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->addCustomerDetails(WebPayItem::individualCustomer()->setName("Tess", "Testson"))
            ->useInvoicePayment();

        $order->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : NationalIdNumber is required for individual customers when countrycode is SE, NO, DK or FI. Use function setNationalIdNumber().
     */
    public function testFailOnNationalIdNumberIsEmptyString()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->setCountryCode("SE")
            ->setOrderDate(date('c'))
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(""))
            ->useInvoicePayment();

        $order->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : OrgNumber is required for company customers when countrycode is SE, NO, DK or FI. Use function setNationalIdNumber().
     */
    public function testFailOnMissingOrgNumberForCompanyOrderSe()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->setCountryCode("SE")
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->addCustomerDetails(WebPayItem::companyCustomer()->setCompanyName("Mycompany"))
            ->useInvoicePayment();

        $order->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage
     * -missing value : BirthDate is required for individual customers when countrycode is DE. Use function setBirthDate().
     * -missing value : Name is required for individual customers when countrycode is DE. Use function setName().
     * -missing value : StreetAddress is required for all customers when countrycode is DE. Use function setStreetAddress().
     * -missing value : Locality is required for all customers when countrycode is DE. Use function setLocality().
     * -missing value : ZipCode is required for all customers when countrycode is DE. Use function setZipCode().
     */
    public function testFailOnMissingIdentityValuesForDEPaymentPlanOrder()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->setCountryCode("DE")
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->usePaymentPlanPayment(213060);

        $order->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : BirthDate is required for individual customers when countrycode is DE. Use function setBirthDate().
     */
    public function testFailOnMissingBirthDateForDeOrder()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->setCountryCode("DE")
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->addCustomerDetails(WebPayItem::individualCustomer()
                //->setBirthDate(1923, 12, 12)
                ->setName("Tess", "Testson")
                ->setStreetAddress("Gatan", 23)
                ->setZipCode(9999)
                ->setLocality("Stan")
            )
            ->useInvoicePayment();
        $order->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage
     * -missing value : Initials is required for individual customers when countrycode is NL. Use function setInitials().
     * -missing value : BirthDate is required for individual customers when countrycode is NL. Use function setBirthDate().
     * -missing value : Name is required for individual customers when countrycode is NL. Use function setName().
     * -missing value : StreetAddress is required for all customers when countrycode is NL. Use function setStreetAddress().
     * -missing value : Locality is required for all customers when countrycode is NL. Use function setLocality().
     * -missing value : ZipCode is required for all customers when countrycode is NL. Use function setZipCode().
     */
    public function testFailOnMissingValuesForNlOrder()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(TestUtil::createHostedOrderRow())
            ->setCountryCode("NL")
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->useInvoicePayment();
        //$errorArray = $order->validateOrder();
        ////print_r($errorArray);
        $order->prepareRequest(); //throws esception
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : Initials is required for individual customers when countrycode is NL. Use function setInitials().
     */
    public function testFailOnMissingInitialsForNlOrder()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->setCountryCode("NL")
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->addCustomerDetails(WebPayItem::individualCustomer()
                //->setInitials("SB")
                ->setBirthDate(1923, 12, 12)
                ->setName("Tess", "Testson")
                ->setStreetAddress("Gatan", 23)
                ->setZipCode(9999)
                ->setLocality("Stan")
            )
            ->useInvoicePayment();
        $order->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing values : OrderRows are required. Use function addOrderRow(Svea\WebPay\WebPayItem::orderRow) to get orderrow setters.
     */
    public function testFailOnMissingOrderRows()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->setCountryCode("SE")
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(46111111))
            ->useInvoicePayment();
        $order->prepareRequest();
    }

//    /**
//     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
//     * @expectedExceptionMessage -missing values : At least two of the values must be set in object \Svea\WebPay\WebPayItem::  AmountExVat, AmountIncVat or VatPercent for Orderrow. Use functions setAmountExVat(), setAmountIncVat() or setVatPercent().
//     * -missing value : Quantity is required in object \Svea\WebPay\WebPayItem. Use function \Svea\WebPay\WebPayItem::setQuantity().
//     */
//    public function testFailOnMissingOrderRowValues() {
//        $builder = \Svea\WebPay\WebPay::createOrder($config);
//        $order = $builder
//                ->addOrderRow(\Svea\WebPay\WebPayItem::orderRow())
//                ->setCountryCode("SE")
//                ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
//                ->addCustomerDetails(\Svea\WebPay\WebPayItem::individualCustomer()->setNationalIdNumber(46111111))
//                ->useInvoicePayment();
//        $order->prepareRequest();
//    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing values : Precisely two of these values must be set in the Svea\WebPay\WebPayItem object:  AmountExVat, AmountIncVat or VatPercent for Orderrow. Use functions setAmountExVat(), setAmountIncVat() or setVatPercent().
     */
    public function testFailOnOrderRowMissingAllOfAmountExVatAmountIncVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity(1)
            )
            ->setCountryCode("SE")
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(46111111))
            ->useInvoicePayment();
        $order->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing values : Precisely two of these values must be set in the Svea\WebPay\WebPayItem object:  AmountExVat, AmountIncVat or VatPercent for Orderrow. Use functions setAmountExVat(), setAmountIncVat() or setVatPercent().
     */
    public function testFailOnOrderRowIncludesAllOfAmountExVatAmountIncVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(100.00)
                ->setAmountExVat(125.00)
                ->setVatPercent(25)
                ->setQuantity(1)
            )
            ->setCountryCode("SE")
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(46111111))
            ->useInvoicePayment();
        $order->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage
     * -missing values : OrderDate is Required. Use function setOrderDate().
     */
    public function testFailOnMissingOrderDate()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(TestUtil::createHostedOrderRow())
            ->setCountryCode("SE")
            // ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(46111111))
            ->useInvoicePayment();
        $order->prepareRequest();
    }

    // order row type validation

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -incorrect datatype : articleNumber is not of type string.
     */
    public function testFailOnArticleNumberNotString()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(20)
                ->setQuantity(1)
                ->setArticleNumber(42)
            )
            ->setCountryCode("SE")
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(46111111))
            ->useInvoicePayment();
        $order->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -incorrect datatype : quantity is not numeric, set as integer or float.
     */
    public function testFailOnQuantityNotNumeric()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(20)
                ->setQuantity("1,25")    // note that i.e. "1,25" is numeric 1, so exclude strings for safety
            )
            ->setCountryCode("SE")
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(46111111))
            ->useInvoicePayment();
        $order->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -incorrect datatype : unit is not of type string.
     */
    public function testFailOnUnitNotString()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(20)
                ->setQuantity(1)
                ->setUnit(1)
            )
            ->setCountryCode("SE")
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(46111111))
            ->useInvoicePayment();
        $order->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -incorrect datatype : amountExVat is not of type float or int.
     */
    public function testFailOnAmountExVatNotFloat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat("100.00")
                ->setVatPercent(20)
                ->setQuantity(1)
            )
            ->setCountryCode("SE")
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(46111111))
            ->useInvoicePayment();
        $order->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -incorrect datatype : amountIncVat is not of type float or int.
     */
    public function testFailOnAmountIncVatNotFloat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat("100.00")
                ->setVatPercent(20)
                ->setQuantity(1)
            )
            ->setCountryCode("SE")
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(46111111))
            ->useInvoicePayment();
        $order->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -incorrect datatype : name is not of type string.
     */
    public function testFailOnNameNotString()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(20)
                ->setName(1701)
                ->setQuantity(1)
            )
            ->setCountryCode("SE")
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(46111111))
            ->useInvoicePayment();
        $order->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -incorrect datatype : description is not of type string.
     */
    public function testFailOnDescriptionNotString()
    {
        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(20)
                ->setDescription(1701)
                ->setQuantity(1)
            )
            ->setCountryCode("SE")
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(46111111))
            ->useInvoicePayment();
        $order->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -incorrect datatype : vatPercent is not of type int.
     */
    public function testFailOnVatNotInt()
    {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'vatPercent can be int and float now'
        );

        $config = ConfigurationService::getDefaultConfig();
        $builder = WebPay::createOrder($config);
        $order = $builder
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(20.33)
                ->setQuantity(1)
            )
            ->setCountryCode("SE")
            ->setOrderDate("Mon, 15 Aug 05 15:52:01 +0000")
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(46111111))
            ->useInvoicePayment();
        $order->prepareRequest();
    }


    // public function testFailOnVatDiscountNotInt() {} This attribute has no setter in OrderRow, so not tested

    /**
     * @expectedException Exception
     * @expectedExceptionMessage
     * -missing parameter: This method requires an Svea\WebPay\Config\ConfigurationProvider object as parameter. Create a class that implements class Svea\WebPay\Config\ConfigurationProvider. Set returnvalues to configuration values. Create an object from that class. Alternative use static function from class ConfigurationService e.g. ConfigurationService::getDefaultConfig(). You can replace the default config values into config files to return your own config values.');
     */
    public function testFailOnMissingCofigurationProviderCreateOrder()
    {
        $object = WebPay::createOrder();
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage
     * -missing parameter: This method requires an Svea\WebPay\Config\ConfigurationProvider object as parameter. Create a class that implements class Svea\WebPay\Config\ConfigurationProvider. Set returnvalues to configuration values. Create an object from that class. Alternative use static function from class ConfigurationService e.g. ConfigurationService::getDefaultConfig(). You can replace the default config values into config files to return your own config values.');
     */
    public function testFailOnMissingCofigurationProviderGetPaymentPlanParams()
    {
        $object = WebPay::getPaymentPlanParams();
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage
     * -missing parameter: This method requires an Svea\WebPay\Config\ConfigurationProvider object as parameter. Create a class that implements class Svea\WebPay\Config\ConfigurationProvider. Set returnvalues to configuration values. Create an object from that class. Alternative use static function from class ConfigurationService e.g. ConfigurationService::getDefaultConfig(). You can replace the default config values into config files to return your own config values.');
     */
    public function testFailOnMissingCofigurationProviderDeliverOrder()
    {
        $object = WebPay::deliverOrder();
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage
     * -missing parameter: This method requires an Svea\WebPay\Config\ConfigurationProvider object as parameter. Create a class that implements class Svea\WebPay\Config\ConfigurationProvider. Set returnvalues to configuration values. Create an object from that class. Alternative use static function from class ConfigurationService e.g. ConfigurationService::getDefaultConfig(). You can replace the default config values into config files to return your own config values.');
     */
    public function testFailOnMissingCofigurationProviderCloseOrder()
    {
        $object = WebPay::closeOrder();
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage
     * -missing parameter: This method requires an Svea\WebPay\Config\ConfigurationProvider object as parameter. Create a class that implements class Svea\WebPay\Config\ConfigurationProvider. Set returnvalues to configuration values. Create an object from that class. Alternative use static function from class ConfigurationService e.g. ConfigurationService::getDefaultConfig(). You can replace the default config values into config files to return your own config values.');
     */
    public function tes_tFailOnMissingCofigurationProviderGetAddresses()
    {
        $object = WebPay::getAddresses();
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage
     * -missing parameter: This method requires an Svea\WebPay\Config\ConfigurationProvider object as parameter. Create a class that implements class Svea\WebPay\Config\ConfigurationProvider. Set returnvalues to configuration values. Create an object from that class. Alternative use static function from class ConfigurationService e.g. ConfigurationService::getDefaultConfig(). You can replace the default config values into config files to return your own config values.');
     */
    public function tes_tFailOnMissingCofigurationProviderGetPaymentMethods()
    {
        $object = WebPay::getPaymentMethods();
    }

}
