<?php

namespace Svea\WebPay\Test\UnitTest\AdminService;

use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Config\ConfigurationService;


/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class UpdateOrderRequestTest extends \PHPUnit_Framework_TestCase
{

    public $notes = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
                    Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque
                    penatibus et magnis';

    /**
     * req: SveaOrderId, ClientId
     * update: Notes, ClientOrderNumber
     */
    public function test_updateorder_clientnr_invoice()
    {
        print_r(strlen($this->notes));
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPayAdmin::updateOrder($config)//only need clientid
        ->setCountryCode('SE')//req for config
        ->setOrderId('test')
            ->setClientOrderNumber('123')//string
            ->updateInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals('123', $request->ClientOrderNumber->enc_value);
    }

    public function test_updateorder_addnotes_invoice()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPayAdmin::updateOrder($config)//only need clientid
        ->setCountryCode('SE')//req for config
        ->setOrderId('test')
            ->setNotes($this->notes)//string 200 chars
            ->updateInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals($this->notes, $request->Notes->enc_value);
    }

    public function test_updateorder_clientnr_paymentplan()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPayAdmin::updateOrder($config)//only need clientid
        ->setCountryCode('SE')//req for config
        ->setOrderId('test')
            ->setClientOrderNumber('123')//string
            ->updateInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals('123', $request->ClientOrderNumber->enc_value);
    }

    public function test_updateorder_addnotes_paymentplan()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPayAdmin::updateOrder($config)//only need clientid
        ->setCountryCode('SE')//req for config
        ->setOrderId('test')
            ->setNotes($this->notes)//string 200 chars
            ->updateInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals($this->notes, $request->Notes->enc_value);
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -String length : The field Notes must be a string with a maximum length of 200.
     *
     */
    public function test_updateorder_addnotes_chars_validate_invoice()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPayAdmin::updateOrder($config)//only need clientid
        ->setCountryCode('SE')//req for config
        ->setOrderId('test')
            ->setNotes('Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    Nullam faucibus turpis ut nibh cursus, volutpat consectetur odio
                    consequat. Quisque fermentum, augue eget scelerisque hendrerit,
                    libero odio mollis metus, eleifend semper enim ligula eu eros.
                    Nullam varius, nunc sit amet tincidunt volutpat, sem sapien semper
                    libero, at consectetur arcu nulla quis dolor. Nunc bibendum vulputate
                    consequat. Mauris luctus dolor non dui aliquet, ut finibus metus porttitor.
                    Etiam ut lacinia augue, id fringilla lorem. Duis vel pellentesque purus,
                    in feugiat ligula. Curabitur efficitur, nunc et mattis volutpat,
                    urna turpis tempus magna, nec convallis nisi mauris ut eros. ')//string to long
            ->updateInvoiceOrder()
            ->prepareRequest();
        $this->assertEquals($this->notes, $request->Notes->enc_value);
    }

}