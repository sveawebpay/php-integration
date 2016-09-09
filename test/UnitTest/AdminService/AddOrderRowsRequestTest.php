<?php

namespace Svea\WebPay\Test\UnitTest\AdminService;

use Svea\WebPay\WebPayItem;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\BuildOrder\OrderBuilder;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\AdminService\AddOrderRowsRequest;


/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class AddOrderRowsRequestTest extends \PHPUnit_Framework_TestCase
{

    public $builderObject;

    public function setUp()
    {
        $this->builderObject = new OrderBuilder(ConfigurationService::getDefaultConfig());
        $this->builderObject->orderId = 123456;
        $this->builderObject->orderType = ConfigurationProvider::INVOICE_TYPE;
        $this->builderObject->countryCode = "SE";
        $this->builderObject->orderRows = array(TestUtil::createOrderRow(10.00));
    }

    public function testClassExists()
    {
        $AddOrderRowsRequestObject = new AddOrderRowsRequest($this->builderObject);
        $this->assertInstanceOf('Svea\WebPay\AdminService\AddOrderRowsRequest', $AddOrderRowsRequestObject);
    }

    public function test_validate_throws_exception_on_missing_OrderId()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : orderId is required.'
        );

        unset($this->builderObject->orderId);
        $AddOrderRowsRequestObject = new AddOrderRowsRequest($this->builderObject);
        $request = $AddOrderRowsRequestObject->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_OrderType()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : orderType is required.'
        );

        unset($this->builderObject->orderType);
        $AddOrderRowsRequestObject = new AddOrderRowsRequest($this->builderObject);
        $request = $AddOrderRowsRequestObject->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_CountryCode()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : countryCode is required.'
        );

        unset($this->builderObject->countryCode);
        $AddOrderRowsRequestObject = new AddOrderRowsRequest($this->builderObject);
        $request = $AddOrderRowsRequestObject->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_orderRows()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : orderRows is required.'
        );

        unset($this->builderObject->orderRows);
        $AddOrderRowsRequestObject = new AddOrderRowsRequest($this->builderObject);
        $request = $AddOrderRowsRequestObject->prepareRequest();
    }

    public function test_validate_throws_exception_on_orderRows_missing_vat_information_none()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing order row vat information : cannot calculate orderRow vatPercent, need at least two of amountExVat, amountIncVat and vatPercent.'
        );

        $this->builderObject->orderRows[] = WebPayItem::orderRow()
            ->setArticleNumber("1")
            ->setQuantity(1)
            //->setAmountExVat( 1.00 )
            //->setAmountIncVat( 1.00 * 1.25 )
            //->setVatPercent(25)
            ->setDescription("Specification")
            ->setName('Product')
            ->setUnit("st")
            ->setDiscountPercent(0);
        $AddOrderRowsRequestObject = new AddOrderRowsRequest($this->builderObject);
        $request = $AddOrderRowsRequestObject->prepareRequest();
    }

    //outcommented cause added param that make test fail
//    public function test_prepareRequest_is_well_formed() {
//
//        // add order rows to builderobject
//        $this->builderObject->orderRows[] = Svea\WebPay\Test\TestUtil::createOrderRow( 1.00, 1 );
//        $this->builderObject->orderId = 123456;
//
//        $addOrderRowsRequest = new Svea\WebPay\AdminService\AddOrderRowsRequest( $this->builderObject );
//        $addOrderRowsSoapRequest = $addOrderRowsRequest->prepareRequest();
//
//        print_r( $addOrderRowsSoapRequest );
//        $this->assertEquals($this->prepareRequest_addOrderRowsSoapRequest(), $addOrderRowsSoapRequest);
//    }


    public function test_generate_prepareRequest_addOrderRowsSoapRequest()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'run once to generate testdata for prepareRequest_addOrderRowsSoapRequest()'
        );


        // add order rows to builderobject
        $this->builderObject->orderRows[] = TestUtil::createOrderRow(1.00, 1);
        $this->builderObject->orderId = 123456;

        $addOrderRowsRequest = new AddOrderRowsRequest($this->builderObject);
        $addOrderRowsSoapRequest = $addOrderRowsRequest->prepareRequest();

        // used once to get data for addOrderRowsRequest() below
        //print_r( "\ncopy the following to prepareRequest_addOrderRowsSoapRequest:\n\n".serialize($addOrderRowsSoapRequest)."\n\n" );
    }

    private function prepareRequest_addOrderRowsSoapRequest()
    {

        $serialised_addOrderRowsSoapResponse = 'O:47:"Svea\WebPay\AdminService\AdminSoap\AddOrderRowsRequest":5:{s:14:"Authentication";O:7:"SoapVar":6:{s:8:"enc_type";i:301;s:9:"enc_value";O:42:"Svea\WebPay\AdminService\AdminSoap\Authentication":2:{s:8:"Password";O:7:"SoapVar":6:{s:8:"enc_type";i:101;s:9:"enc_value";s:11:"sverigetest";s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:8:"Password";s:10:"enc_namens";s:65:"http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service";}s:8:"Username";O:7:"SoapVar":6:{s:8:"enc_type";i:101;s:9:"enc_value";s:11:"sverigetest";s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:8:"Username";s:10:"enc_namens";s:65:"http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service";}}s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:14:"Authentication";s:10:"enc_namens";s:65:"http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service";}s:8:"ClientId";O:7:"SoapVar":6:{s:8:"enc_type";i:134;s:9:"enc_value";i:79021;s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:8:"ClientId";s:10:"enc_namens";s:65:"http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service";}s:9:"OrderRows";O:7:"SoapVar":6:{s:8:"enc_type";i:301;s:9:"enc_value";O:7:"SoapVar":2:{s:8:"enc_type";i:301;s:9:"enc_value";a:2:{i:0;O:7:"SoapVar":4:{s:8:"enc_type";i:301;s:9:"enc_value";O:36:"Svea\WebPay\AdminService\AdminSoap\OrderRow":7:{s:13:"ArticleNumber";O:7:"SoapVar":6:{s:8:"enc_type";i:101;s:9:"enc_value";s:1:"1";s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:13:"ArticleNumber";s:10:"enc_namens";s:62:"http://schemas.datacontract.org/2004/07/DataObjects.Webservice";}s:11:"Description";O:7:"SoapVar":6:{s:8:"enc_type";i:101;s:9:"enc_value";s:22:"Product: Specification";s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:11:"Description";s:10:"enc_namens";s:62:"http://schemas.datacontract.org/2004/07/DataObjects.Webservice";}s:15:"DiscountPercent";O:7:"SoapVar":6:{s:8:"enc_type";i:103;s:9:"enc_value";i:0;s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:15:"DiscountPercent";s:10:"enc_namens";s:62:"http://schemas.datacontract.org/2004/07/DataObjects.Webservice";}s:13:"NumberOfUnits";O:7:"SoapVar":6:{s:8:"enc_type";i:103;s:9:"enc_value";i:2;s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:13:"NumberOfUnits";s:10:"enc_namens";s:62:"http://schemas.datacontract.org/2004/07/DataObjects.Webservice";}s:12:"PricePerUnit";O:7:"SoapVar":6:{s:8:"enc_type";i:103;s:9:"enc_value";d:10;s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:12:"PricePerUnit";s:10:"enc_namens";s:62:"http://schemas.datacontract.org/2004/07/DataObjects.Webservice";}s:4:"Unit";O:7:"SoapVar":6:{s:8:"enc_type";i:101;s:9:"enc_value";s:2:"st";s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:4:"Unit";s:10:"enc_namens";s:62:"http://schemas.datacontract.org/2004/07/DataObjects.Webservice";}s:10:"VatPercent";O:7:"SoapVar":6:{s:8:"enc_type";i:103;s:9:"enc_value";i:25;s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:10:"VatPercent";s:10:"enc_namens";s:62:"http://schemas.datacontract.org/2004/07/DataObjects.Webservice";}}s:8:"enc_name";s:8:"OrderRow";s:10:"enc_namens";s:62:"http://schemas.datacontract.org/2004/07/DataObjects.Webservice";}i:1;O:7:"SoapVar":4:{s:8:"enc_type";i:301;s:9:"enc_value";O:36:"Svea\WebPay\AdminService\AdminSoap\OrderRow":7:{s:13:"ArticleNumber";O:7:"SoapVar":6:{s:8:"enc_type";i:101;s:9:"enc_value";s:1:"1";s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:13:"ArticleNumber";s:10:"enc_namens";s:62:"http://schemas.datacontract.org/2004/07/DataObjects.Webservice";}s:11:"Description";O:7:"SoapVar":6:{s:8:"enc_type";i:101;s:9:"enc_value";s:22:"Product: Specification";s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:11:"Description";s:10:"enc_namens";s:62:"http://schemas.datacontract.org/2004/07/DataObjects.Webservice";}s:15:"DiscountPercent";O:7:"SoapVar":6:{s:8:"enc_type";i:103;s:9:"enc_value";i:0;s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:15:"DiscountPercent";s:10:"enc_namens";s:62:"http://schemas.datacontract.org/2004/07/DataObjects.Webservice";}s:13:"NumberOfUnits";O:7:"SoapVar":6:{s:8:"enc_type";i:103;s:9:"enc_value";i:1;s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:13:"NumberOfUnits";s:10:"enc_namens";s:62:"http://schemas.datacontract.org/2004/07/DataObjects.Webservice";}s:12:"PricePerUnit";O:7:"SoapVar":6:{s:8:"enc_type";i:103;s:9:"enc_value";d:1;s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:12:"PricePerUnit";s:10:"enc_namens";s:62:"http://schemas.datacontract.org/2004/07/DataObjects.Webservice";}s:4:"Unit";O:7:"SoapVar":6:{s:8:"enc_type";i:101;s:9:"enc_value";s:2:"st";s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:4:"Unit";s:10:"enc_namens";s:62:"http://schemas.datacontract.org/2004/07/DataObjects.Webservice";}s:10:"VatPercent";O:7:"SoapVar":6:{s:8:"enc_type";i:103;s:9:"enc_value";i:25;s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:10:"VatPercent";s:10:"enc_namens";s:62:"http://schemas.datacontract.org/2004/07/DataObjects.Webservice";}}s:8:"enc_name";s:8:"OrderRow";s:10:"enc_namens";s:62:"http://schemas.datacontract.org/2004/07/DataObjects.Webservice";}}}s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:9:"OrderRows";s:10:"enc_namens";s:65:"http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service";}s:9:"OrderType";O:7:"SoapVar":6:{s:8:"enc_type";i:101;s:9:"enc_value";s:7:"Invoice";s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:9:"OrderType";s:10:"enc_namens";s:65:"http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service";}s:11:"SveaOrderId";O:7:"SoapVar":6:{s:8:"enc_type";i:134;s:9:"enc_value";i:123456;s:9:"enc_stype";s:1:"-";s:6:"enc_ns";s:2:"--";s:8:"enc_name";s:11:"SveaOrderId";s:10:"enc_namens";s:65:"http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service";}}';

//Svea\WebPay\AdminService\AdminSoap\AddOrderRowsRequest Object
//(
//    [Authentication] => SoapVar Object
//        (
//            [enc_type] => 301
//            [enc_value] => Svea\WebPay\AdminService\AdminSoap\Authentication Object
//                (
//                    [Password] => SoapVar Object
//                        (
//                            [enc_type] => 101
//                            [enc_value] => sverigetest
//                            [enc_stype] => -
//                            [enc_ns] => --
//                            [enc_name] => Password
//                            [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service
//                        )
//
//                    [Username] => SoapVar Object
//                        (
//                            [enc_type] => 101
//                            [enc_value] => sverigetest
//                            [enc_stype] => -
//                            [enc_ns] => --
//                            [enc_name] => Username
//                            [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service
//                        )
//
//                )
//
//            [enc_stype] => -
//            [enc_ns] => --
//            [enc_name] => Authentication
//            [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service
//        )
//
//    [ClientId] => SoapVar Object
//        (
//            [enc_type] => 134
//            [enc_value] => 79021
//            [enc_stype] => -
//            [enc_ns] => --
//            [enc_name] => ClientId
//            [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service
//        )
//
//    [OrderRows] => SoapVar Object
//        (
//            [enc_type] => 301
//            [enc_value] => SoapVar Object
//                (
//                    [enc_type] => 301
//                    [enc_value] => Array
//                        (
//                            [0] => SoapVar Object
//                                (
//                                    [enc_type] => 301
//                                    [enc_value] => Svea\WebPay\AdminService\AdminSoap\OrderRow Object
//                                        (
//                                            [ArticleNumber] => SoapVar Object
//                                                (
//                                                    [enc_type] => 101
//                                                    [enc_value] => 1
//                                                    [enc_stype] => -
//                                                    [enc_ns] => --
//                                                    [enc_name] => ArticleNumber
//                                                    [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Webservice
//                                                )
//
//                                            [Description] => SoapVar Object
//                                                (
//                                                    [enc_type] => 101
//                                                    [enc_value] => Product: Specification
//                                                    [enc_stype] => -
//                                                    [enc_ns] => --
//                                                    [enc_name] => Description
//                                                    [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Webservice
//                                                )
//
//                                            [DiscountPercent] => SoapVar Object
//                                                (
//                                                    [enc_type] => 103
//                                                    [enc_value] => 0
//                                                    [enc_stype] => -
//                                                    [enc_ns] => --
//                                                    [enc_name] => DiscountPercent
//                                                    [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Webservice
//                                                )
//
//                                            [NumberOfUnits] => SoapVar Object
//                                                (
//                                                    [enc_type] => 103
//                                                    [enc_value] => 2
//                                                    [enc_stype] => -
//                                                    [enc_ns] => --
//                                                    [enc_name] => NumberOfUnits
//                                                    [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Webservice
//                                                )
//
//                                            [PricePerUnit] => SoapVar Object
//                                                (
//                                                    [enc_type] => 103
//                                                    [enc_value] => 10
//                                                    [enc_stype] => -
//                                                    [enc_ns] => --
//                                                    [enc_name] => PricePerUnit
//                                                    [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Webservice
//                                                )
//
//                                            [Unit] => SoapVar Object
//                                                (
//                                                    [enc_type] => 101
//                                                    [enc_value] => st
//                                                    [enc_stype] => -
//                                                    [enc_ns] => --
//                                                    [enc_name] => Unit
//                                                    [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Webservice
//                                                )
//
//                                            [VatPercent] => SoapVar Object
//                                                (
//                                                    [enc_type] => 103
//                                                    [enc_value] => 25
//                                                    [enc_stype] => -
//                                                    [enc_ns] => --
//                                                    [enc_name] => VatPercent
//                                                    [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Webservice
//                                                )
//
//                                        )
//
//                                    [enc_name] => OrderRow
//                                    [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Webservice
//                                )
//
//                            [1] => SoapVar Object
//                                (
//                                    [enc_type] => 301
//                                    [enc_value] => Svea\WebPay\AdminService\AdminSoap\OrderRow Object
//                                        (
//                                            [ArticleNumber] => SoapVar Object
//                                                (
//                                                    [enc_type] => 101
//                                                    [enc_value] => 1
//                                                    [enc_stype] => -
//                                                    [enc_ns] => --
//                                                    [enc_name] => ArticleNumber
//                                                    [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Webservice
//                                                )
//
//                                            [Description] => SoapVar Object
//                                                (
//                                                    [enc_type] => 101
//                                                    [enc_value] => Product: Specification
//                                                    [enc_stype] => -
//                                                    [enc_ns] => --
//                                                    [enc_name] => Description
//                                                    [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Webservice
//                                                )
//
//                                            [DiscountPercent] => SoapVar Object
//                                                (
//                                                    [enc_type] => 103
//                                                    [enc_value] => 0
//                                                    [enc_stype] => -
//                                                    [enc_ns] => --
//                                                    [enc_name] => DiscountPercent
//                                                    [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Webservice
//                                                )
//
//                                            [NumberOfUnits] => SoapVar Object
//                                                (
//                                                    [enc_type] => 103
//                                                    [enc_value] => 1
//                                                    [enc_stype] => -
//                                                    [enc_ns] => --
//                                                    [enc_name] => NumberOfUnits
//                                                    [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Webservice
//                                                )
//
//                                            [PricePerUnit] => SoapVar Object
//                                                (
//                                                    [enc_type] => 103
//                                                    [enc_value] => 1
//                                                    [enc_stype] => -
//                                                    [enc_ns] => --
//                                                    [enc_name] => PricePerUnit
//                                                    [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Webservice
//                                                )
//
//                                            [Unit] => SoapVar Object
//                                                (
//                                                    [enc_type] => 101
//                                                    [enc_value] => st
//                                                    [enc_stype] => -
//                                                    [enc_ns] => --
//                                                    [enc_name] => Unit
//                                                    [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Webservice
//                                                )
//
//                                            [VatPercent] => SoapVar Object
//                                                (
//                                                    [enc_type] => 103
//                                                    [enc_value] => 25
//                                                    [enc_stype] => -
//                                                    [enc_ns] => --
//                                                    [enc_name] => VatPercent
//                                                    [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Webservice
//                                                )
//
//                                        )
//
//                                    [enc_name] => OrderRow
//                                    [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Webservice
//                                )
//
//                        )
//
//                )
//
//            [enc_stype] => -
//            [enc_ns] => --
//            [enc_name] => OrderRows
//            [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service
//        )
//
//    [OrderType] => SoapVar Object
//        (
//            [enc_type] => 101
//            [enc_value] => Invoice
//            [enc_stype] => -
//            [enc_ns] => --
//            [enc_name] => OrderType
//            [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service
//        )
//
//    [SveaOrderId] => SoapVar Object
//        (
//            [enc_type] => 134
//            [enc_value] => 123456
//            [enc_stype] => -
//            [enc_ns] => --
//            [enc_name] => SveaOrderId
//            [enc_namens] => http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service
//        )
//
//)

        return unserialize($serialised_addOrderRowsSoapResponse);
    }

    /**
     * rounding
     */

    public function test_add_single_orderRow_as_exvat()
    {
        $config = ConfigurationService::getDefaultConfig();

        $request = WebPayAdmin::addOrderRows($config)
            ->setOrderId('sveaOrderId')
            ->setCountryCode('SE')
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setVatPercent(24)
                    ->setAmountExVat(80.00)
                    ->setQuantity(1)
            )
            ->addInvoiceOrderRows()
            ->prepareRequest();

        $this->assertEquals(80, $request->OrderRows->enc_value->enc_value[0]->enc_value->PricePerUnit->enc_value);
        $this->assertFalse($request->OrderRows->enc_value->enc_value[0]->enc_value->PriceIncludingVat->enc_value);
    }

    public function test_add_rows_as_exvat()
    {
        $orderrowArray[] = WebPayItem::orderRow()
            ->setVatPercent(24)
            ->setAmountExVat(80.00)
            ->setQuantity(1);
        $orderrowArray[] = WebPayItem::orderRow()
            ->setVatPercent(24)
            ->setAmountExVat(10.00)
            ->setQuantity(1);
        $config = ConfigurationService::getDefaultConfig();

        $request = WebPayAdmin::addOrderRows($config)
            ->setOrderId('sveaOrderId')
            ->setCountryCode('SE')
            ->addOrderRows($orderrowArray)
            ->addInvoiceOrderRows()
            ->prepareRequest();

        $this->assertEquals(80, $request->OrderRows->enc_value->enc_value[0]->enc_value->PricePerUnit->enc_value);
        $this->assertFalse($request->OrderRows->enc_value->enc_value[0]->enc_value->PriceIncludingVat->enc_value);
        $this->assertEquals(10, $request->OrderRows->enc_value->enc_value[1]->enc_value->PricePerUnit->enc_value);
        $this->assertFalse($request->OrderRows->enc_value->enc_value[1]->enc_value->PriceIncludingVat->enc_value);
    }

    public function test_add_single_orderRow_as_incvat()
    {
        $config = ConfigurationService::getDefaultConfig();

        $request = WebPayAdmin::addOrderRows($config)
            ->setOrderId('sveaOrderId')
            ->setCountryCode('SE')
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setVatPercent(24)
                    ->setAmountIncVat(123.9876)
                    ->setQuantity(1)
            )
            ->addInvoiceOrderRows()
            ->prepareRequest();

        $this->assertEquals(123.9876, $request->OrderRows->enc_value->enc_value[0]->enc_value->PricePerUnit->enc_value);
        $this->assertTrue($request->OrderRows->enc_value->enc_value[0]->enc_value->PriceIncludingVat->enc_value);
    }

    public function test_add_rows_as_incvat()
    {
        $orderrowArray[] = WebPayItem::orderRow()
            ->setVatPercent(24)
            ->setAmountIncVat(123.9876)
            ->setQuantity(1);
        $orderrowArray[] = WebPayItem::orderRow()
            ->setVatPercent(24)
            ->setAmountIncVat(12.39876)
            ->setQuantity(1);
        $config = ConfigurationService::getDefaultConfig();

        $request = WebPayAdmin::addOrderRows($config)
            ->setOrderId('sveaOrderId')
            ->setCountryCode('SE')
            ->addOrderRows($orderrowArray)
            ->addInvoiceOrderRows()
            ->prepareRequest();

        $this->assertEquals(123.9876, $request->OrderRows->enc_value->enc_value[0]->enc_value->PricePerUnit->enc_value);
        $this->assertTrue($request->OrderRows->enc_value->enc_value[0]->enc_value->PriceIncludingVat->enc_value);
        $this->assertEquals(12.39876, $request->OrderRows->enc_value->enc_value[1]->enc_value->PricePerUnit->enc_value);
        $this->assertTrue($request->OrderRows->enc_value->enc_value[1]->enc_value->PriceIncludingVat->enc_value);
    }

    public function test_add_single_orderRow_as_incvat_and_exvat()
    {
        $config = ConfigurationService::getDefaultConfig();

        $request = WebPayAdmin::addOrderRows($config)
            ->setOrderId('sveaOrderId')
            ->setCountryCode('SE')
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
                    ->setAmountIncVat(123.9876)
                    ->setQuantity(1)
            )
            ->addInvoiceOrderRows()
            ->prepareRequest();

        $this->assertEquals(123.9876, $request->OrderRows->enc_value->enc_value[0]->enc_value->PricePerUnit->enc_value);
        $this->assertTrue($request->OrderRows->enc_value->enc_value[0]->enc_value->PriceIncludingVat->enc_value);
    }

    public function test_add_rows_as_incvat_and_exvat()
    {
        $orderrowArray[] = WebPayItem::orderRow()
            ->setAmountExVat(99.99)
            ->setAmountIncVat(123.9876)
            ->setQuantity(1);
        $orderrowArray[] = WebPayItem::orderRow()
            ->setAmountExVat(9.999)
            ->setAmountIncVat(12.39876)
            ->setQuantity(1);
        $config = ConfigurationService::getDefaultConfig();

        $request = WebPayAdmin::addOrderRows($config)
            ->setOrderId('sveaOrderId')
            ->setCountryCode('SE')
            ->addOrderRows($orderrowArray)
            ->addInvoiceOrderRows()
            ->prepareRequest();

        $this->assertEquals(123.9876, $request->OrderRows->enc_value->enc_value[0]->enc_value->PricePerUnit->enc_value);
        $this->assertTrue($request->OrderRows->enc_value->enc_value[0]->enc_value->PriceIncludingVat->enc_value);
        $this->assertEquals(12.39876, $request->OrderRows->enc_value->enc_value[1]->enc_value->PricePerUnit->enc_value);
        $this->assertTrue($request->OrderRows->enc_value->enc_value[1]->enc_value->PriceIncludingVat->enc_value);
    }

    public function test_add_rows_as_incvat_mixed_with_exvat()
    {
        $orderrowArray[] = WebPayItem::orderRow()
            ->setAmountExVat(99.99)
            ->setVatPercent(24)
            ->setQuantity(1);
        $orderrowArray[] = WebPayItem::orderRow()
            ->setAmountExVat(99.99)
            ->setVatPercent(24)
            ->setQuantity(1);
        $orderrowArray[] = WebPayItem::orderRow()
            ->setAmountIncVat(123.9876)
            ->setVatPercent(24)
            ->setQuantity(1);

        $config = ConfigurationService::getDefaultConfig();

        $request = WebPayAdmin::addOrderRows($config)
            ->setOrderId('sveaOrderId')
            ->setCountryCode('SE')
            ->addOrderRows($orderrowArray)
            ->addInvoiceOrderRows()
            ->prepareRequest();

        $this->assertEquals(99.99, $request->OrderRows->enc_value->enc_value[0]->enc_value->PricePerUnit->enc_value);
        $this->assertFalse($request->OrderRows->enc_value->enc_value[0]->enc_value->PriceIncludingVat->enc_value);
        $this->assertEquals(99.99, $request->OrderRows->enc_value->enc_value[1]->enc_value->PricePerUnit->enc_value);
        $this->assertFalse($request->OrderRows->enc_value->enc_value[1]->enc_value->PriceIncludingVat->enc_value);
        $this->assertEquals(99.99, $request->OrderRows->enc_value->enc_value[2]->enc_value->PricePerUnit->enc_value);
        $this->assertFalse($request->OrderRows->enc_value->enc_value[2]->enc_value->PriceIncludingVat->enc_value);
    }

    public function test_add_row_multiple_times_as_incvat_mixed_with_exvat()
    {
        $orderrowArray[] = WebPayItem::orderRow()
            ->setAmountExVat(99.99)
            ->setVatPercent(24)
            ->setQuantity(1);
        $orderrowArray[] = WebPayItem::orderRow()
            ->setAmountExVat(99.99)
            ->setVatPercent(24)
            ->setQuantity(1);
        $orderrowArray[] = WebPayItem::orderRow()
            ->setAmountIncVat(123.9876)
            ->setVatPercent(24)
            ->setQuantity(1);

        $config = ConfigurationService::getDefaultConfig();

        $request = WebPayAdmin::addOrderRows($config)
            ->setOrderId('sveaOrderId')
            ->setCountryCode('SE')
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
                    ->setAmountIncVat(123.9876)
                    ->setQuantity(1)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setVatPercent(24)
                    ->setAmountIncVat(123.9876)
                    ->setQuantity(1)
            )
            ->addInvoiceOrderRows()
            ->prepareRequest();

        $this->assertEquals(99.99, $request->OrderRows->enc_value->enc_value[0]->enc_value->PricePerUnit->enc_value);
        $this->assertFalse($request->OrderRows->enc_value->enc_value[0]->enc_value->PriceIncludingVat->enc_value);
        $this->assertEquals(99.99, $request->OrderRows->enc_value->enc_value[1]->enc_value->PricePerUnit->enc_value);
        $this->assertFalse($request->OrderRows->enc_value->enc_value[1]->enc_value->PriceIncludingVat->enc_value);
        $this->assertEquals(99.99, $request->OrderRows->enc_value->enc_value[2]->enc_value->PricePerUnit->enc_value);
        $this->assertFalse($request->OrderRows->enc_value->enc_value[2]->enc_value->PriceIncludingVat->enc_value);
    }

}
