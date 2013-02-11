<?php

$root = realpath(dirname(__FILE__));
require_once $root . '\..\..\..\src\Includes.php';

/**
 * Description of SveaConfigTest
 */
class SveaConfigTest extends PHPUnit_Framework_TestCase {
    
    function testInstancesOfSveaConfig(){
        $obj1 = SveaConfig::getConfig();
        $obj2 = SveaConfig::getConfig();
        $this->assertEquals($obj1->password, $obj2->password);
        
        $obj1->password = "Hej";
        $this->assertNotEquals($obj1->password, $obj2->password);
    }
}

?>
