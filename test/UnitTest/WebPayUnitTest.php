<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../src/Includes.php';
require_once $root . '/../TestUtil.php';

/**
 * WebPay unit tests checks that we validate all required methods for the various entry point methods
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class WebPayUnitTest extends \PHPUnit_Framework_TestCase {


        /// createOrder
        // useInvoicePayment    
        // individualCustomer validation - common        
	function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_SE_addOrderRow() {
            $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
                        //->addOrderRow( 
                        //    WebPayItem::orderRow()
                        //        ->setQuantity(1.0)
                        //        ->setAmountExVat(4.0)
                        //        ->setAmountIncVat(5.0)
                        //)
                        ->addCustomerDetails(
                            WebPayItem::individualCustomer()
                                ->setNationalIdNumber("4605092222")
                        )
                        ->setCountryCode("SE")
                        ->setOrderDate(date('c'))
            ;

            // prepareRequest() validates the order and throws SveaWebPayException on validation failure
            $this->setExpectedException(
                'Svea\ValidationException', 
                '-missing values : OrderRows are required. Use function addOrderRow(WebPayItem::orderRow) to get orderrow setters.'
            );   
            $order->useInvoicePayment()->prepareRequest();               
        }        
	function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_SE_addCustomerDetails() {
            $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
                        ->addOrderRow( 
                            WebPayItem::orderRow()
                                ->setQuantity(1.0)
                                ->setAmountExVat(4.0)
                                ->setAmountIncVat(5.0)
                        )
                        //->addCustomerDetails(
                        //    WebPayItem::individualCustomer()
                        //        ->setNationalIdNumber("4605092222")
                        //)
                        ->setCountryCode("SE")
                        ->setOrderDate(date('c'))
            ;

            // prepareRequest() validates the order and throws SveaWebPayException on validation failure
            $this->setExpectedException(
                'Svea\ValidationException', 
                '-missing customerIdentity : customerIdentity is required. Use function addCustomerDetails().'
            );   
            $order->useInvoicePayment()->prepareRequest();               
        }                
	function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_SE_setCountryCode() {
            $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
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
                        ->setOrderDate(date('c'))
            ;

            // prepareRequest() validates the order and throws SveaWebPayException on validation failure
            $this->setExpectedException(
                'Svea\ValidationException', 
                '-missing value : CountryCode is required. Use function setCountryCode().'
            );      
            $order->useInvoicePayment()->prepareRequest();       
        }       
	function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_SE_setOrderDate() {
            $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
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
                        //->setOrderDate(date('c'))
            ;

            // prepareRequest() validates the order and throws SveaWebPayException on validation failure
            $this->setExpectedException(
                'Svea\ValidationException', 
                '-missing value : OrderDate is Required. Use function setOrderDate().'
            );   
            $order->useInvoicePayment()->prepareRequest(); 
        }   
                              
        // SE
        // IndividualCustomer validation
	function test_validates_all_required_methods_for_createOrder_useInvoicePayment_IndividualCustomer_SE() {
            $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
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
                        ->setOrderDate(date('c'))
            ;
            // prepareRequest() validates the order and throws SveaWebPayException on validation failure
            try {
                $order->useInvoicePayment()->prepareRequest();
            }
            catch (Exception $e){
                // fail on validation error
                $this->fail( "Unexpected validation exception: " . $e->getMessage() );
            }
        }
        
        function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_missing_credentials_SE() {
            $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
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
                        ->setOrderDate(date('c'))
            ;

            // prepareRequest() validates the order and throws SveaWebPayException on validation failure
            $this->setExpectedException(
                'Svea\ValidationException', 
                '-missing value : NationalIdNumber is required for individual customers when countrycode is SE, NO, DK or FI. Use function setNationalIdNumber().'
            );   
            $order->useInvoicePayment()->prepareRequest();      
        }    
        // CompanyCustomer
        // TODO
        
        // NO
        // IndividualCustomer validation
	function test_validates_all_required_methods_for_createOrder_useInvoicePayment_IndividualCustomer_NO() {
            $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
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
                        ->setOrderDate(date('c'))
            ;
            // prepareRequest() validates the order and throws SveaWebPayException on validation failure
            try {
                $order->useInvoicePayment()->prepareRequest();
            }
            catch (Exception $e){
                // fail on validation error
                $this->fail( "Unexpected validation exception: " . $e->getMessage() );
            }
        }

        function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_missing_credentials_NO() {
            $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
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
                        ->setOrderDate(date('c'))
            ;

            // prepareRequest() validates the order and throws SveaWebPayException on validation failure
            $this->setExpectedException(
                'Svea\ValidationException', 
                '-missing value : NationalIdNumber is required for individual customers when countrycode is SE, NO, DK or FI. Use function setNationalIdNumber().'
            );   
            $order->useInvoicePayment()->prepareRequest();      
        }   
        // CompanyCustomer
        // TODO
        
        // DK
        // IndividualCustomer validation
	function test_validates_all_required_methods_for_createOrder_useInvoicePayment_IndividualCustomer_DK() {
            $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
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
                        ->setOrderDate(date('c'))
            ;
            // prepareRequest() validates the order and throws SveaWebPayException on validation failure
            try {
                $order->useInvoicePayment()->prepareRequest();
            }
            catch (Exception $e){
                // fail on validation error
                $this->fail( "Unexpected validation exception: " . $e->getMessage() );
            }
        }

        function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_missing_credentials_DK() {
            $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
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
                        ->setOrderDate(date('c'))
            ;

            // prepareRequest() validates the order and throws SveaWebPayException on validation failure
            $this->setExpectedException(
                'Svea\ValidationException', 
                '-missing value : NationalIdNumber is required for individual customers when countrycode is SE, NO, DK or FI. Use function setNationalIdNumber().'
            );   
            $order->useInvoicePayment()->prepareRequest();      
        }   
        // CompanyCustomer
        // TODO
        
        // FI
        // IndividualCustomer validation
	function test_validates_all_required_methods_for_createOrder_useInvoicePayment_IndividualCustomer_FI() {
            $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
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
                        ->setOrderDate(date('c'))
            ;
            // prepareRequest() validates the order and throws SveaWebPayException on validation failure
            try {
                $order->useInvoicePayment()->prepareRequest();
            }
            catch (Exception $e){
                // fail on validation error
                $this->fail( "Unexpected validation exception: " . $e->getMessage() );
            }
        }

        function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_missing_credentials_FI() {
            $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
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
                        ->setOrderDate(date('c'))
            ;

            // prepareRequest() validates the order and throws SveaWebPayException on validation failure
            $this->setExpectedException(
                'Svea\ValidationException', 
                '-missing value : NationalIdNumber is required for individual customers when countrycode is SE, NO, DK or FI. Use function setNationalIdNumber().'
            );   
            $order->useInvoicePayment()->prepareRequest();      
        }
        // CompanyCustomer
        // TODO
        
        // DE
        // IndividualCustomer validation
        function test_validates_all_required_methods_for_createOrder_useInvoicePayment_IndividualCustomer_DE() {
            $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
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
                        ->setOrderDate(date('c'))
            ;
            // prepareRequest() validates the order and throws SveaWebPayException on validation failure
            try {
                $order->useInvoicePayment()->prepareRequest();
            }
            catch (Exception $e){
                // fail on validation error
                $this->fail( "Unexpected validation exception: " . $e->getMessage() );
                //-missing value : BirthDate is required for individual customers when countrycode is DE. Use function setBirthDate().
                //-missing value : Name is required for individual customers when countrycode is DE. Use function setName().
                //-missing value : StreetAddress is required for all customers when countrycode is DE. Use function setStreetAddress().
                //-missing value : ZipCode is required for all customers when countrycode is DE. Use function setZipCode().
                //-missing value : Locality is required for all customers when countrycode is DE. Use function setLocality()
            }
        }

        function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_missing_credentials_DE() {
            $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
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
                        ->setOrderDate(date('c'))
            ;

            // prepareRequest() validates the order and throws SveaWebPayException on validation failure
            $this->setExpectedException(
                'Svea\ValidationException', 
                '-missing value : ZipCode is required for all customers when countrycode is DE. Use function setZipCode().'
            );   
            $order->useInvoicePayment()->prepareRequest();      
        }
        // CompanyCustomer
        // TODO       
        
        // NL
        // IndividualCustomer validation
	function test_validates_all_required_methods_for_createOrder_useInvoicePayment_IndividualCustomer_NL() {
            $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
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
                        ->setOrderDate(date('c'))
            ;
            // prepareRequest() validates the order and throws SveaWebPayException on validation failure
            try {
                $order->useInvoicePayment()->prepareRequest();
            }
            catch (Exception $e){
                // fail on validation error:
                //-missing value : BirthDate is required for individual customers when countrycode is NL. Use function setBirthDate().
                //-missing value : Initials is required for individual customers when countrycode is NL. Use function setInitials().
                //-missing value : Name is required for individual customers when countrycode is NL. Use function setName()
                //-missing value : StreetAddress is required for all customers when countrycode is NL. Use function setStreetAddress().
                //-missing value : ZipCode is required for all customers when countrycode is NL. Use function setZipCode().
                //-missing value : Locality is required for all customers when countrycode is NL. Use function setLocality().
                $this->fail( "Unexpected validation exception: " . $e->getMessage() );
            }
        }

        function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_missing_credentials_NL() {
            $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
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
                        ->setOrderDate(date('c'))
            ;

            // prepareRequest() validates the order and throws SveaWebPayException on validation failure
            $this->setExpectedException(
                'Svea\ValidationException', 
                '-missing value : ZipCode is required for all customers when countrycode is NL. Use function setZipCode()'
            );   
            $order->useInvoicePayment()->prepareRequest();      
        }
        // CompanyCustomer
        // TODO
        
        
        
        
        
        
        
        
        /// TODO deliverOrder
        // deliverInvoiceOrder
        // ...
        // deliverPaymentPlanOrder
        // ...
        // deliverCardOrder
        // ...
        
        /// TODO getAddresses
        // setCountryCode
        // setIdentifier
        
        /// TODO getPaymentPlanParams
        // setCountryCode
        // setIdentifier
        
        /// listPaymentMethods
        function test_validates_all_required_methods_for_listPaymentMethods() {
            $order = WebPay::listPaymentMethods(Svea\SveaConfig::getDefaultConfig())
                        ->setCountryCode("SE")
            ;
            try {
                $order->prepareRequest();
            }
            catch (Exception $e){
                // fail on validation error
                $this->fail( "Unexpected validation exception: " . $e->getMessage() );
            }
        }

        function test_validates_missing_required_method_for_listPaymentMethods_setCountryCode() {
            $order = WebPay::listPaymentMethods(Svea\SveaConfig::getDefaultConfig())
                        //->setCountryCode("SE")
            ;
            
            $this->setExpectedException(
                'Svea\ValidationException', 
                '-missing value : CountryCode is required. Use function setCountryCode().'                
            );   
            $order->prepareRequest();         
        }             
}
