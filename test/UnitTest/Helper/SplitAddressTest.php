<?php
namespace Svea;

$root = realpath(dirname(__FILE__) );
require_once $root . '/../../../src/Includes.php';

class SplitAddressTest extends \PHPUnit_Framework_TestCase {

    // SplitStreetAddress
    function testStreet(){
        $address = Helper::splitStreetAddress("Street");
        $this->assertEquals( "Street", $address[1]);
        $this->assertEquals( "", $address[2]);
    }
    function testStreet_10(){
        $address = Helper::splitStreetAddress("Street 10");
          $this->assertEquals( "Street", $address[1]);
          $this->assertEquals( "10", $address[2]);
    }
    function test_Street_10(){
        $address = Helper::splitStreetAddress(" Street 10 ");
          $this->assertEquals( "Street", $address[1]);
          $this->assertEquals( "10", $address[2]);
    }
    function testStreet_10bis(){
        $address = Helper::splitStreetAddress("Street 10bis");
          $this->assertEquals( "Street", $address[1]);
          $this->assertEquals( "10bis", $address[2]);
    }
    function testStreet_10_bis(){
        $address = Helper::splitStreetAddress("Street 10 bis");
          $this->assertEquals( "Street", $address[1]);
          $this->assertEquals( "10 bis", $address[2]);
    }
    function testStreet___10__bis(){
        $address = Helper::splitStreetAddress("Street   10  bis");
          $this->assertEquals( "Street", $address[1]);
          $this->assertEquals( "10  bis", $address[2]);
    }
    function test3rd_street_11(){
        $address = Helper::splitStreetAddress("3rd street 11");
          $this->assertEquals( "3rd street", $address[1]);
          $this->assertEquals( "11", $address[2]);
    }
    function test3rd_street_11bis(){
        $address = Helper::splitStreetAddress("3rd street 11bis");
          $this->assertEquals( "3rd street", $address[1]);
          $this->assertEquals( "11bis", $address[2]);
    }
    function test3rd_street_11_bis(){
        $address = Helper::splitStreetAddress("3rd street 11 bis");
          $this->assertEquals( "3rd street", $address[1]);
          $this->assertEquals( "11 bis", $address[2]);
    }
    function test_3rd___street___11___bis(){
        $address = Helper::splitStreetAddress(" 3rd   street   11   bis ");
          $this->assertEquals( "3rd   street", $address[1]);
          $this->assertEquals( "11   bis", $address[2]);
    }
    function testSankt_Larsgatan_1_Lgh_1003(){
        $address = Helper::splitStreetAddress("Sankt Larsgatan 1 Lgh 1003");
        $this->assertEquals( "Sankt Larsgatan", $address[1]);
        $this->assertEquals( "1 Lgh 1003", $address[2]);
    }

    //    //Svea testperson
    function testGate_42_23(){
        $address = Helper::splitStreetAddress("Gate 42 23");
        $this->assertEquals( "Gate", $address[1]);
        $this->assertEquals( "42 23", $address[2]); // ok, see testInvoiceRequestNLAcceptedWithDoubleHousenumber
    }
    
    // decided not to implement this case, as it looks like a corner case w/"street 42" and housenumber 23 after    
//    //Svea testperson
//    function testGate_42_23(){
//        $address = Helper::splitStreetAddress("Gate 42 23");
//        $this->assertEquals( "Gate 42", $address[1]);
//        $this->assertEquals( "23", $address[2]);
//    }
    
    //Interpuncation in streetaddress
    function testStreetcomma_10(){
        $address = Helper::splitStreetAddress("Street, 10");
          $this->assertEquals( "Street", $address[1]);
          $this->assertEquals( "10", $address[2]);
    }
//    function testGate_42comma_23(){
//        $address = Helper::splitStreetAddress("Gate 4, 23");
//        $this->assertEquals( "Gate 42", $address[1]);
//        $this->assertEquals( "23", $address[2]);
//    }
//    function testSankt_Larsgatan_1comma_Lgh_1003(){
//        $address = Helper::splitStreetAddress("Sankt Larsgatan 1, Lgh 1003");
//        $this->assertEquals( "Sankt Larsgatan", $address[1]);
//        $this->assertEquals( "1 Lgh 1003", $address[2]);
//    }



    //Examples given by getzenned.nl
    function testSingelstraat_10(){
        $address = Helper::splitStreetAddress("Singelstraat 10");
        $this->assertEquals( "Singelstraat", $address[1]);
        $this->assertEquals( "10", $address[2]);
    }
    function test3e_laan_12(){
        $address = Helper::splitStreetAddress("3e laan 12");
        $this->assertEquals( "3e laan", $address[1]);
        $this->assertEquals( "12", $address[2]);
    }
    function testHeeregracht_12bis(){
        $address = Helper::splitStreetAddress("Heeregracht 12bis");
        $this->assertEquals( "Heeregracht", $address[1]);
        $this->assertEquals( "12bis", $address[2]);
    }
    
    //International characters
    function testÖnskevägen_10(){
        $address = Helper::splitStreetAddress("Önskevägen 10");
        $this->assertEquals( "Önskevägen", $address[1]);
        $this->assertEquals( "10", $address[2]);
    }
    function testÅlandshav_10å(){
        $address = Helper::splitStreetAddress("Ålandshav 10å");
        $this->assertEquals( "Ålandshav", $address[1]);
        $this->assertEquals( "10å", $address[2]);
            //print_r($address);
    }
    function testÅväg_änna_10(){
        $address = Helper::splitStreetAddress("Åväg änna 10");
        $this->assertEquals( "Åväg änna", $address[1]);
        $this->assertEquals( "10", $address[2]);
    }
    function testÄÅÖåäöÜü(){
        $address = Helper::splitStreetAddress("ÄÅÖåäöÜü");
        $this->assertEquals( "ÄÅÖåäöÜü", $address[1]);
    }
    function testÄÅÆÖØÜßäåæöøü_10(){
        $address = Helper::splitStreetAddress("ÄÅÆÖØÜßäåæöøü 10");
        $this->assertEquals( "ÄÅÆÖØÜßäåæöøü", $address[1]);
        $this->assertEquals( "10", $address[2]);
    }
}

?>
