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
        // SE
        // individualCustomer
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

        // test individualCustomer()
        function test_validates_missing_required_method_for_useInvoicePayment_IndividualCustomer_missing_setNationalIdNumber() {
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
                      
        // TODO createOrder -- other payment methods, countries, companycustomer, validate order rows et al.
        
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
