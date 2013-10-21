<?php
namespace Svea;

$root = realpath(dirname(__FILE__) );
require_once $root . '/../../../src/Includes.php';

class HelperTest extends \PHPUnit_Framework_TestCase {

    // TODO check out parameterised tests
    function testBankersRounding_RoundsHalfToEven() {
        $this->assertEquals( 1, Helper::bround(0.51) );
        $this->assertEquals( 1, Helper::bround(1.49) );
        $this->assertEquals( 2, Helper::bround(1.5) );
                      
        $this->assertEquals( 1, Helper::bround(1.49999) ); //seems to work with up to 5 decimals, then float creep pushes us over 1.5
        $this->assertEquals( 2, Helper::bround(1.500000000000000000000000000000000000000000) );
        $this->assertEquals( 1, Helper::bround(1.0) );
        $this->assertEquals( 1, Helper::bround(1) );
        //$this->assert( 1, bround("1") );     raise illegalArgumentException??
                
        $this->assertEquals( 4, Helper::bround(4.5) );
        $this->assertEquals( 6, Helper::bround(5.5) );

        $this->assertEquals( -1, Helper::bround(-1.1) );
        $this->assertEquals( -2, Helper::bround(-1.5) );

        $this->assertEquals( 0, Helper::bround(-0.5) );
        $this->assertEquals( 0, Helper::bround(0) ); 
        $this->assertEquals( 0, Helper::bround(0.5) );
                
        $this->assertEquals( 262462, Helper::bround(262462.5) );             
    }
}
?>
