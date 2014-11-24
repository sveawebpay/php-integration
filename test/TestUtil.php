<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../src/Includes.php';

/**
 * @author Jonas Lith, Kristian Grossman-Madsen, Anneli Halld'n for Svea WebPay
 */
class TestUtil {

    /**
     * creates a populated order object for use in tests
     *
     * @return Svea\createOrderBuilder object
     *
     */
    public static function createOrder( $customer = NULL ) {

        // poor man's overloading
        if( $customer == NULL ) {
            $customer = TestUtil::createIndividualCustomer("SE");
        }

        $config = Svea\SveaConfig::getDefaultConfig();

        $orderObject = WebPay::createOrder($config)
                ->addOrderRow( TestUtil::createOrderRow() )
                ->addCustomerDetails( $customer )
                ->setCountryCode("SE")
                ->setCurrency("SEK")
                ->setOrderDate( date('c') )
        ;

        return $orderObject;
    }

    /**
     * creates a populated order object for use in tests
     *
     * @return Svea\createOrderBuilder object
     *
     */
    public static function createOrderWithoutOrderRows( $customer = NULL ) {

        // poor man's overloading
        if( $customer == NULL ) {
            $customer = TestUtil::createIndividualCustomer("SE");
        }

        $config = Svea\SveaConfig::getDefaultConfig();

        $orderObject = WebPay::createOrder($config)
                ->addCustomerDetails( $customer )
                ->setCountryCode("SE")
                ->setCurrency("SEK")
                ->setOrderDate( date('c') )
        ;

        return $orderObject;
    }

    /**
     * Creates an OrderRow object for use in populating order objects.
     * @param float $amount, defaults to 100
     * @param int $amount, defaults to 2
     *
     * @return Svea\OrderRow object
     */
    public static function createOrderRow( $amount = 100.00, $quantity = 2 ) {
        return WebPayItem::orderRow()
            ->setArticleNumber("1")
            ->setQuantity( $quantity )
            ->setAmountExVat( $amount )
            ->setDescription("Specification")
            ->setName('Product')
            ->setUnit("st")
            ->setVatPercent(25)
            ->setDiscountPercent(0);
    }

    /**
     * Creates an NumberedOrderRow object for use in populating order objects.
     * @param float $amount, defaults to 100
     * @param int $amount, defaults to 2
     *
     * @return Svea\OrderRow object
     */
    public static function createNumberedOrderRow( $amount = 100.00, $quantity = 1, $number = 1 ) {
        return WebPayItem::numberedOrderRow()
            ->setArticleNumber("1")
            ->setQuantity( $quantity )
            ->setAmountExVat( $amount )
            ->setDescription("Specification")
            ->setName('Product')
            ->setUnit("st")
            ->setVatPercent(25)
            ->setDiscountPercent(0)
            ->setRowNumber( $number )
        ;
    }

    /**
     * Returns an individual customer object.
     * The object is populated using test customer data for the given country.
     *
     * Defaults to a SE customer in good credit standing with Svea, i.e. any
     * transaction using this customer should be accepted by Svea services.
     *
     * @param country -- accepts SE (default) and NL
     * @return Svea\IndividualCustomer
     */
    public static function createIndividualCustomer( $country="SE" ) {
        switch( strtoupper($country) ) {

        //####1.3.1 Options for individual customers
        //```php
        //->addCustomerDetails(
        //    WebPayItem::individualCustomer()
        //    ->setNationalIdNumber(194605092222) //Required for individual customers in SE, NO, DK, FI
        //    ->setInitials("SB")                 //Required for individual customers in NL
        //    ->setBirthDate(1923, 12, 20)        //Required for individual customers in NL and DE
        //    ->setName("Tess", "Testson")        //Required for individual customers in NL and DE
        //    ->setStreetAddress("Gatan", 23)     //Required in NL and DE
        //    ->setZipCode(9999)                  //Required in NL and DE
        //    ->setLocality("Stan")               //Required in NL and DE
        //    ->setEmail("test@svea.com")         //Optional but desirable
        //    ->setIpAddress("123.123.123")       //Optional but desirable
        //    ->setCoAddress("c/o Eriksson")      //Optional
        //    ->setPhoneNumber(999999)            //Optional
        //    )

        case( "NL" ):
            return WebPayItem::individualCustomer()
                ->setBirthDate(1955, 03, 07)
                ->setName("Sneider", "Boasman")
                ->setStreetAddress("Gate", 42)
                ->setCoAddress(138)
                ->setLocality("BARENDRECHT")
                ->setZipCode("1102 HG")
                ->setInitials("SB");
            break;

        case( "SE" ):
            return WebPayItem::individualCustomer()
                ->setNationalIdNumber("194605092222")
                ->setBirthDate(1946, 05, 09)
                ->setName("Tess T", "Persson")
                ->setStreetAddress("Testgatan", 1)
                ->setCoAddress("c/o Eriksson, Erik")
                ->setLocality("Stan")
                ->setZipCode("99999");
            break;
        case( "FI" ):
            return WebPayItem::individualCustomer()
                ->setNationalIdNumber("160264-999N");
            break;

        default:
            throw new Exception("undefined IndividualCustomerCountry in TestUtil -- add country and credentials");
            break;
        }
    }

    public static function createCompanyCustomer( $country="SE" ) {
        switch( strtoupper($country) ) {

        //####1.3.2 Options for company customers
        //```php
        //->addCustomerDetails(
        //    WebPayItem::companyCustomer()
        //    ->setNationalIdNumber(2345234)       //Required in SE, NO, DK, FI
        //    ->setVatNumber("NL2345234")         //Required in NL and DE
        //    ->setCompanyName("TestCompagniet")  //Required in NL and DE
        //    ->setStreetAddress("Gatan", 23)     //Required in NL and DE
        //    ->setZipCode(9999)                  //Required in NL and DE
        //    ->setLocality("Stan")               //Required in NL and DE
        //    ->setEmail("test@svea.com")         //Optional but desirable
        //    ->setIpAddress("123.123.123")       //Optional but desirable
        //    ->setCoAddress("c/o Eriksson")      //Optional
        //    ->setPhoneNumber(999999)            //Optional
        //    ->setAddressSelector("7fd7768")     //Optional, string recieved from WebPay::getAddress() request
        //    )

        case( "SE" ):
            return WebPayItem::companyCustomer()
                ->setNationalIdNumber("4608142222")
                ->setCompanyName("Tess T", "Persson")
                ->setStreetAddress("Testgatan", 1)
                ->setCoAddress("c/o Eriksson, Erik")
                ->setLocality("Stan")
                ->setZipCode("99999");
            break;

        default:
            throw new Exception("undefined CompanyCustomerCountry in TestUtil -- add country and credentials");
            break;
        }
    }

    /**
     * Creates an OrderRow object with amount 100 and a given tax rate
     *
     * @param int vatPercent the tax rate for this order row (defaults to 25 if omitted)
     * @return Svea\OrderRow object
     */
    public static function createOrderRowWithVat( $vatPercent = 25 ) {
        return WebPayItem::orderRow()
            ->setArticleNumber("1")
            ->setQuantity(1)
            ->setAmountExVat(100.00)
            ->setDescription("Specification")
            ->setName('Prod')
            ->setUnit("st")
            ->setVatPercent( $vatPercent )
            ->setDiscountPercent(0);
    }

    public static function createHostedOrderRow() {
        return WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(1);
    }

    /**
     * Returns ShippingFeeRow to use as shorthand in testFunctions
     * Use function run($functionname) to run shorthand function
     * @return type
     */
    public function buildShippingFee() {
        return function($orderbuilder) {
            return $orderbuilder
              ->addFee(
                WebPayItem::shippingFee()
                     ->setShippingId('33')
                    ->setName('shipping')
                    ->setDescription("Specification")
                    ->setAmountExVat(50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                );
        };
    }

    /**
     * Returns InvoicefeeRow to use as shorthand in testFunctions
     * Use function run($functionname) to run shorthand function
     * @return type
     */
    public function buildInvoiceFee() {
        return function($orderbuilder) {
            return $orderbuilder
                ->addFee(
                WebPayItem::invoiceFee()
                    ->setName('Svea fee')
                    ->setDescription("Fee for invoice")
                    ->setAmountExVat(50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                        );
        };
    }

    /**
     * Use to get a campaign code to i.e. use as argument to usePaymentPlanPayment()
     * @return string  the first available campaignCode
     */
    public static function getGetPaymentPlanParamsForTesting( $country = "SE" ) {
        $addressRequest = WebPay::getPaymentPlanParams(Svea\SveaConfig::getDefaultConfig());
        $response = $addressRequest
                ->setCountryCode($country)
                ->doRequest();
         return $response->campaignCodes[0]->campaignCode;
    }
}
