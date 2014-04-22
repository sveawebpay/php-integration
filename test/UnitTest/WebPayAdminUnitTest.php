<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../src/Includes.php';
require_once $root . '/../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class WebPayAdminUnitTest extends \PHPUnit_Framework_TestCase {

    public function test_WebPayAdmin_class_exists() {
        $adminObject = new \WebPayAdmin();        
        $this->assertInstanceOf( "WebPayAdmin", $adminObject );
    }

    //HostedRequest/HandleOrder classes
    
    public function test_annulTransaction() {
        $config = SveaConfig::getDefaultConfig();
        $annulTransactionObject = \WebPayAdmin::annulTransaction($config);        
        $this->assertInstanceOf( "Svea\AnnulTransaction", $annulTransactionObject );
    }
    
    public function test_confirmTransaction() {
        $config = SveaConfig::getDefaultConfig();
        $confirmTransactionObject = \WebPayAdmin::confirmTransaction($config);        
        $this->assertInstanceOf( "Svea\ConfirmTransaction", $confirmTransactionObject );
    }

    public function test_creditTransaction() {
        $config = SveaConfig::getDefaultConfig();
        $creditTransactionObject = \WebPayAdmin::creditTransaction($config);        
        $this->assertInstanceOf( "Svea\CreditTransaction", $creditTransactionObject );
    }

    
}
