<?php

namespace Svea\WebPay\Test\UnitTest;

use Exception;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\PaymentMethod;
use Svea\WebPay\Constant\DistributionType;


/**
 * Svea\WebPay\WebPay unit tests checks that we validate all required methods for the various entry point methods
 *
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class WebPayUnitTest extends \PHPUnit_Framework_TestCase
{


    /// createOrder
    // useInvoicePayment return type
    // web service eu: invoice
    // web service eu: paymentplan
    // bypass paypage: usepaymentmethod
    // paypage: cardonly
    // paypage: directbankonly
    // paypage
    public function test_createOrder_useInvoicePayment_returns_InvoicePayment()
    {
        $createOrder = WebPay::createOrder(ConfigurationService::getDefaultConfig());
        // we should set attributes here if real request
        $request = $createOrder->useInvoicePayment();
        $this->assertInstanceOf("Svea\WebPay\WebService\Payment\InvoicePayment", $request);
    }

    public function test_createOrder_usePaymentPlanPayment_returns_PaymentPlanPayment()
    {
        $createOrder = WebPay::createOrder(ConfigurationService::getDefaultConfig());
        $request = $createOrder->usePaymentPlanPayment(TestUtil::getGetPaymentPlanParamsForTesting());
        $this->assertInstanceOf("Svea\WebPay\WebService\Payment\PaymentPlanPayment", $request);
    }

    public function test_createOrder_usePayPageCardOnly_returns_CardPayment()
    {
        $createOrder = WebPay::createOrder(ConfigurationService::getDefaultConfig());
        $request = $createOrder->usePayPageCardOnly();
        $this->assertInstanceOf("Svea\WebPay\HostedService\Payment\CardPayment", $request);
    }

    public function test_createOrder_usePayPageDirectBankOnly_returns_DirectPayment()
    {
        $createOrder = WebPay::createOrder(ConfigurationService::getDefaultConfig());
        $request = $createOrder->usePayPageDirectBankOnly();
        $this->assertInstanceOf("Svea\WebPay\HostedService\Payment\DirectPayment", $request);
    }

    public function test_createOrder_usePaymentMethod_returns_PaymentMethodPayment()
    {
        $createOrder = WebPay::createOrder(ConfigurationService::getDefaultConfig());
        $request = $createOrder->usePaymentMethod("mocked_paymentMethod");
        $this->assertInstanceOf("Svea\WebPay\HostedService\Payment\PaymentMethodPayment", $request);
    }

    public function test_createOrder_usePayPage_returns_PayPagePayment()
    {
        $createOrder = WebPay::createOrder(ConfigurationService::getDefaultConfig());
        $request = $createOrder->usePayPage();
        $this->assertInstanceOf("Svea\WebPay\HostedService\Payment\PayPagePayment", $request);
    }

    // individualCustomer validation - common        
    function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_SE_addOrderRow()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            //->addOrderRow( 
            //    Svea\WebPay\WebPayItem::orderRow()
            //        ->setQuantity(1.0)
            //        ->setAmountExVat(4.0)
            //        ->setAmountIncVat(5.0)
            //)
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
                    ->setNationalIdNumber("4605092222")
            )
            ->setCountryCode("SE")
            ->setOrderDate(date('c'));

        // prepareRequest() validates the order and throws SveaWebPayException on validation failure
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing values : OrderRows are required. Use function addOrderRow(Svea\WebPay\WebPayItem::orderRow) to get orderrow setters.'
        );
        $order->useInvoicePayment()->prepareRequest();
    }

    function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_SE_addCustomerDetails()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
            )
            //->addCustomerDetails(
            //    Svea\WebPay\WebPayItem::individualCustomer()
            //        ->setNationalIdNumber("4605092222")
            //)
            ->setCountryCode("SE")
            ->setOrderDate(date('c'));

        // prepareRequest() validates the order and throws SveaWebPayException on validation failure
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing customerIdentity : customerIdentity is required. Use function addCustomerDetails().'
        );
        $order->useInvoicePayment()->prepareRequest();
    }

    function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_SE_setCountryCode()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
                    ->setNationalIdNumber("4605092222")
            )
            //->setCountryCode("SE")
            ->setOrderDate(date('c'));

        // prepareRequest() validates the order and throws SveaWebPayException on validation failure
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing value : CountryCode is required. Use function setCountryCode().'
        );
        $order->useInvoicePayment()->prepareRequest();
    }

    function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_SE_setOrderDate()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
                    ->setNationalIdNumber("4605092222")
            )
            ->setCountryCode("SE")//->setOrderDate(date('c'))
        ;

        // prepareRequest() validates the order and throws SveaWebPayException on validation failure
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing value : OrderDate is Required. Use function setOrderDate().'
        );
        $order->useInvoicePayment()->prepareRequest();
    }

    // SE
    // IndividualCustomer validation
    function test_validates_all_required_methods_for_createOrder_useInvoicePayment_IndividualCustomer_SE()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
                    ->setNationalIdNumber("4605092222")
            )
            ->setCountryCode("SE")
            ->setOrderDate(date('c'));
        // prepareRequest() validates the order and throws SveaWebPayException on validation failure
        try {
            $order->useInvoicePayment()->prepareRequest();
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_missing_credentials_SE()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
            //missing ->setNationalIdNumber("4605092222")
            //or if we use ->setNationalIdNumber("")
            //or if we use ->setNationalIdNumber(null)
            )
            ->setCountryCode("SE")
            ->setOrderDate(date('c'));

        // prepareRequest() validates the order and throws SveaWebPayException on validation failure
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing value : NationalIdNumber is required for individual customers when countrycode is SE, NO, DK or FI. Use function setNationalIdNumber().'
        );
        $order->useInvoicePayment()->prepareRequest();
    }
    // CompanyCustomer
    // TODO

    // NO
    // IndividualCustomer validation
    function test_validates_all_required_methods_for_createOrder_useInvoicePayment_IndividualCustomer_NO()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
                    ->setNationalIdNumber("17054512066")
            )
            ->setCountryCode("NO")
            ->setOrderDate(date('c'));
        // prepareRequest() validates the order and throws SveaWebPayException on validation failure
        try {
            $order->useInvoicePayment()->prepareRequest();
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_missing_credentials_NO()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
            //->setNationalIdNumber("17054512066")
            )
            ->setCountryCode("NO")
            ->setOrderDate(date('c'));

        // prepareRequest() validates the order and throws SveaWebPayException on validation failure
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing value : NationalIdNumber is required for individual customers when countrycode is SE, NO, DK or FI. Use function setNationalIdNumber().'
        );
        $order->useInvoicePayment()->prepareRequest();
    }
    // CompanyCustomer
    // TODO

    // DK
    // IndividualCustomer validation
    function test_validates_all_required_methods_for_createOrder_useInvoicePayment_IndividualCustomer_DK()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
                    ->setNationalIdNumber("2603692503")
            )
            ->setCountryCode("DK")
            ->setOrderDate(date('c'));
        // prepareRequest() validates the order and throws SveaWebPayException on validation failure
        try {
            $order->useInvoicePayment()->prepareRequest();
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_missing_credentials_DK()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
            //->setNationalIdNumber("2603692503")
            )
            ->setCountryCode("DK")
            ->setOrderDate(date('c'));

        // prepareRequest() validates the order and throws SveaWebPayException on validation failure
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing value : NationalIdNumber is required for individual customers when countrycode is SE, NO, DK or FI. Use function setNationalIdNumber().'
        );
        $order->useInvoicePayment()->prepareRequest();
    }
    // CompanyCustomer
    // TODO

    // FI
    // IndividualCustomer validation
    function test_validates_all_required_methods_for_createOrder_useInvoicePayment_IndividualCustomer_FI()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
                    ->setNationalIdNumber("160264-999N")
            )
            ->setCountryCode("FI")
            ->setOrderDate(date('c'));
        // prepareRequest() validates the order and throws SveaWebPayException on validation failure
        try {
            $order->useInvoicePayment()->prepareRequest();
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_missing_credentials_FI()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
            //->setNationalIdNumber("160264-999N")
            )
            ->setCountryCode("FI")
            ->setOrderDate(date('c'));

        // prepareRequest() validates the order and throws SveaWebPayException on validation failure
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing value : NationalIdNumber is required for individual customers when countrycode is SE, NO, DK or FI. Use function setNationalIdNumber().'
        );
        $order->useInvoicePayment()->prepareRequest();
    }
    // CompanyCustomer
    // TODO

    // DE
    // IndividualCustomer validation
    function test_validates_all_required_methods_for_createOrder_useInvoicePayment_IndividualCustomer_DE()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
                    ->setBirthDate("19680403")
                    ->setName("Theo", "Giebel")
                    ->setStreetAddress("Zörgiebelweg", 21)
                    ->setZipCode("13591")
                    ->setLocality("BERLIN")
            )
            ->setCountryCode("DE")
            ->setOrderDate(date('c'));
        // prepareRequest() validates the order and throws SveaWebPayException on validation failure
        try {
            $order->useInvoicePayment()->prepareRequest();
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
            //-missing value : BirthDate is required for individual customers when countrycode is DE. Use function setBirthDate().
            //-missing value : Name is required for individual customers when countrycode is DE. Use function setName().
            //-missing value : StreetAddress is required for all customers when countrycode is DE. Use function setStreetAddress().
            //-missing value : ZipCode is required for all customers when countrycode is DE. Use function setZipCode().
            //-missing value : Locality is required for all customers when countrycode is DE. Use function setLocality()
        }
    }

    function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_missing_credentials_DE()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
            //->setBirthDate("19680403")
            //->setName("Theo", "Giebel")
            //->setStreetAddress("Zörgiebelweg", 21)
            //->setZipCode("13591")
            //->setLocality("BERLIN")  
            )
            ->setCountryCode("DE")
            ->setOrderDate(date('c'));

        // prepareRequest() validates the order and throws SveaWebPayException on validation failure
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing value : ZipCode is required for all customers when countrycode is DE. Use function setZipCode().'
        );
        $order->useInvoicePayment()->prepareRequest();
    }
    // CompanyCustomer
    // TODO       

    // NL
    // IndividualCustomer validation
    function test_validates_all_required_methods_for_createOrder_useInvoicePayment_IndividualCustomer_NL()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
                    ->setBirthDate("19550307")
                    ->setInitials("SB")
                    ->setName("Sneider", "Boasman")
                    ->setStreetAddress("Gate 42", 23)
                    ->setZipCode("1102 HG")
                    ->setLocality("BARENDRECHT")
            )
            ->setCountryCode("NL")
            ->setOrderDate(date('c'));
        // prepareRequest() validates the order and throws SveaWebPayException on validation failure
        try {
            $order->useInvoicePayment()->prepareRequest();
        } catch (Exception $e) {
            // fail on validation error:
            //-missing value : BirthDate is required for individual customers when countrycode is NL. Use function setBirthDate().
            //-missing value : Initials is required for individual customers when countrycode is NL. Use function setInitials().
            //-missing value : Name is required for individual customers when countrycode is NL. Use function setName()
            //-missing value : StreetAddress is required for all customers when countrycode is NL. Use function setStreetAddress().
            //-missing value : ZipCode is required for all customers when countrycode is NL. Use function setZipCode().
            //-missing value : Locality is required for all customers when countrycode is NL. Use function setLocality().
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_missing_credentials_NL()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
            //->setBirthDate("19550307")
            //->setInitials("SB")
            //->setName("Sneider", "Boasman")
            //->setStreetAddress("Gate 42", 23)
            //->setZipCode("1102 HG")
            //->setLocality("BARENDRECHT")     
            )
            ->setCountryCode("NL")
            ->setOrderDate(date('c'));

        // prepareRequest() validates the order and throws SveaWebPayException on validation failure
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing value : ZipCode is required for all customers when countrycode is NL. Use function setZipCode()'
        );
        $order->useInvoicePayment()->prepareRequest();
    }
//    $this->setExpectedException(
//        'Svea\WebPay\BuildOrder\Validator\ValidationException', 
//        '-missing value : NationalIdNumber is required for individual customers when countrycode is SE, NO, DK or FI. Use function setNationalIdNumber().'
//    );   

    /// Svea\WebPay\WebPay::deliverOrder()
    // deliverInvoiceOrder
    public function test_deliverOrder_deliverInvoiceOrder_without_order_rows_goes_against_adminservice_DeliverOrders()
    {
        $deliverOrder = WebPay::deliverOrder(ConfigurationService::getDefaultConfig());
        $request = $deliverOrder->deliverInvoiceOrder();
        $this->assertInstanceOf("Svea\WebPay\AdminService\DeliverOrdersRequest", $request);
        $this->assertEquals("Invoice", $request->orderBuilder->orderType);
    }

    public function test_deliverOrder_deliverInvoiceOrder_with_order_rows_goes_against_DeliverOrderEU()
    {
        $deliverOrder = WebPay::deliverOrder(ConfigurationService::getDefaultConfig());
        $deliverOrder->addOrderRow(WebPayItem::orderRow());
        $request = $deliverOrder->deliverInvoiceOrder();
        $this->assertInstanceOf("Svea\WebPay\WebService\HandleOrder\DeliverInvoice", $request);         // WebService\DeliverInvoice => soap call DeliverOrderEU  
    }

    // deliverPaymentPlanOrder
    public function test_deliverOrder_deliverPaymentPlanOrder_without_order_rows_goes_against_DeliverOrderEU()
    {
        $deliverOrder = WebPay::deliverOrder(ConfigurationService::getDefaultConfig());
        $request = $deliverOrder->deliverPaymentPlanOrder();
        $this->assertInstanceOf("Svea\WebPay\WebService\HandleOrder\DeliverPaymentPlan", $request);
        $this->assertEquals("PaymentPlan", $request->orderBuilder->orderType);
    }

    public function test_deliverOrder_deliverPaymentPlanOrder_with_order_rows_goes_against_DeliverOrderEU()
    {
        $deliverOrder = WebPay::deliverOrder(ConfigurationService::getDefaultConfig());
        $deliverOrder->addOrderRow(WebPayItem::orderRow());   // order rows are ignored by DeliverOrderEU, can't partially deliver PaymentPlan
        $request = $deliverOrder->deliverPaymentPlanOrder();
        $this->assertInstanceOf("Svea\WebPay\WebService\HandleOrder\DeliverPaymentPlan", $request);
    }

    // card
    public function test_deliverOrder_deliverCardOrder_returns_ConfirmTransaction()
    {
        $deliverOrder = WebPay::deliverOrder(ConfigurationService::getDefaultConfig());
        $deliverOrder->addOrderRow(WebPayItem::orderRow());
        $request = $deliverOrder->deliverCardOrder();
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedAdminRequest\ConfirmTransaction", $request);
    }


    /// TODO getAddresses
    // setCountryCode
    // setIdentifier

    /// TODO getPaymentPlanParams
    // setCountryCode
    // setIdentifier

    /// listPaymentMethods
    function test_validates_all_required_methods_for_listPaymentMethods()
    {
        $order = WebPay::listPaymentMethods(ConfigurationService::getDefaultConfig())
            ->setCountryCode("SE");
        try {
            $order->prepareRequest();
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    function test_validates_missing_required_method_for_listPaymentMethods_setCountryCode()
    {
        $order = WebPay::listPaymentMethods(ConfigurationService::getDefaultConfig())//->setCountryCode("SE")
        ;

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing value : CountryCode is required. Use function setCountryCode().'
        );
        $order->prepareRequest();
    }

    /// Svea\WebPay\WebPay::getAddresses()
    public function test_getAddresses_returns_GetAddresses()
    {
        $request = WebPay::getAddresses(ConfigurationService::getDefaultConfig());
        $this->assertInstanceOf("Svea\WebPay\WebService\GetAddress\GetAddresses", $request);
    }

    /// Svea\WebPay\WebPay::getPaymentPlanParams()
    public function test_getPaymentPlanParams_returns_GetPaymentPlanParams()
    {
        $request = WebPay::getPaymentPlanParams(ConfigurationService::getDefaultConfig());
        $this->assertInstanceOf("Svea\WebPay\WebService\GetPaymentPlanParams\GetPaymentPlanParams", $request);
    }


    // Verify that orderRows may be specified with zero amount (INT-581) when creating an order
    public function test_createOrder_useInvoicePayment_allows_orderRow_with_zero_amount()
    {
        $createOrder = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(0.0)
                    ->setAmountIncVat(0.0)
                    ->setVatPercent(0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
                    ->setNationalIdNumber("4605092222")
            )
            ->setCountryCode("SE")
            ->setOrderDate(date('c'));

        $request = $createOrder->useInvoicePayment();

        // prepareRequest() validates the order and throws SveaWebPayException on validation failure
        try {
            $request->prepareRequest();
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    public function test_createOrder_usePaymentPlanPayment_allows_orderRow_with_zero_amount()
    {
        $createOrder = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(0.0)
                    ->setAmountIncVat(0.0)
                    ->setVatPercent(0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
                    ->setNationalIdNumber("4605092222")
            )
            ->setCountryCode("SE")
            ->setOrderDate(date('c'));

        $request = $createOrder->usePaymentPlanPayment(TestUtil::getGetPaymentPlanParamsForTesting());

        // prepareRequest() validates the order and throws SveaWebPayException on validation failure
        try {
            $request->prepareRequest();
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    public function test_createOrder_usePaymentMethod_KORTCERT_allows_orderRow_with_zero_amount()
    {
        $createOrder = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(0.0)
                    ->setAmountIncVat(0.0)
                    ->setVatPercent(0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
                    ->setNationalIdNumber("4605092222")
            )
            ->setCountryCode("SE")
            ->setOrderDate(date('c'))
            ->setCurrency("SEK")
            ->setClientOrderNumber(date('c'));
        // prepareRequest() validates the order and throws SveaWebPayException on validation failure
        try {
            $request = $createOrder->usePaymentMethod(PaymentMethod::KORTCERT)->setReturnUrl("http://myurl.se")->getPaymentForm();
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    // Verify that orderRows may be specified with zero amount (INT-581) when delivering an order
    public function test_deliverOrder_deliverInvoiceOrder_allows_orderRow_with_zero_amount()
    {
        $dummyorderid = 123456;
        $deliverOrderBuilder = WebPay::deliverOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($dummyorderid)
            ->setCountryCode("SE")
            ->setInvoiceDistributionType(DistributionType::POST)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(0.0)// recommended to specify price using AmountExVat & VatPercent
                    ->setVatPercent(0)// recommended to specify price using AmountExVat & VatPercent
                    ->setQuantity(0)                     // required
            );

        try {
            $request = $deliverOrderBuilder->deliverInvoiceOrder()->prepareRequest();
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    public function test_deliverOrder_deliverPaymentPlanOrder_allows_orderRow_with_zero_amount()
    {
        $dummyorderid = 123456;
        $deliverOrderBuilder = WebPay::deliverOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($dummyorderid)
            ->setCountryCode("SE")
            ->setInvoiceDistributionType(DistributionType::POST)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(0.0)// recommended to specify price using AmountExVat & VatPercent
                    ->setVatPercent(0)// recommended to specify price using AmountExVat & VatPercent
                    ->setQuantity(0)                     // required
            );

        try {
            $request = $deliverOrderBuilder->deliverPaymentPlanOrder()->prepareRequest();
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    public function test_deliverOrder_deliverCardOrder_allows_orderRow_with_zero_amount()
    {
        $dummyorderid = 123456;
        $deliverOrderBuilder = WebPay::deliverOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($dummyorderid)
            ->setCountryCode("SE")
            ->setInvoiceDistributionType(DistributionType::POST)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(0.0)// recommended to specify price using AmountExVat & VatPercent
                    ->setVatPercent(0)// recommended to specify price using AmountExVat & VatPercent
                    ->setQuantity(0)                     // required
            );

        try {
            $request = $deliverOrderBuilder->deliverCardOrder()->prepareRequest();
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }
}
