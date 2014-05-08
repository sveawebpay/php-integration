<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CancelOrderRequestTest extends PHPUnit_Framework_TestCase {

    public function testClassExists() {
        $cancelOrderRequest = new Svea\CancelOrderRequest( new stdClass() );

        $this->assertInstanceOf('Svea\CancelOrderRequest', $cancelOrderRequest);
    }
}
