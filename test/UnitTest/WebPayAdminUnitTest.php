<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../src/Includes.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class WebPayAdminUnitTest extends \PHPUnit_Framework_TestCase {

    public function test_WebPayAdmin_exists() {
        $adminObject = new \WebPayAdmin();
        
        $this->assertInstanceOf( "WebPayAdmin", $adminObject );
    }
}
