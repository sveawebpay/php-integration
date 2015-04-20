<?php
namespace Svea;

$root = realpath(dirname(__FILE__) );
require_once $root . '/../../../src/Includes.php';

class SplittAddressTest extends \PHPUnit_Framework_TestCase {

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
            ////print_r($address);
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
        
        //echo "ÄÅÆÖØÜßäåæöøü" . "\n";
    }

    
    function unicodeChar2string( $unicode_char ) {
        return json_decode('"'.$unicode_char.'"');
    }    
    

    function testBaselineCharacterMatches() {

        $charstring = "ö";
        
        $prefix = "abc";
        $suffix = "xyz";
        $number = "10"; 
        $addressString = $prefix . $charstring . $suffix . " " . $number;
                
        $address = Helper::splitStreetAddress($addressString);
        // you may force netbeans output window encoding to use utf-8 by adding 
        // netbeans_default_options= "... -J-Dfile.encoding=UTF-8"
        // to <netbeans install folder>/etc/netbeans.conf
        
        print_r( "\naddressString: " . $addressString . "\n");
        print_r( "address[0]: " . ( isset( $address[0] ) ? $address[0] : "not set" ) . "\n" );
        print_r( "address[1]: " . ( isset( $address[1] ) ? $address[1] : "not set" ) . "\n" );
        print_r( "address[2]: " . ( isset( $address[2] ) ? $address[2] : "not set" ) . "\n" );
        
        $this->assertEquals( $prefix . $charstring . $suffix, $address[1]);
        $this->assertEquals( $number, $address[2]);                
    }    
    
    // test unicode combined characters (i.e. U+00F6 (ö) as two code points -- U+006F (o) + U+0308 (¨), combining diaeresis)
    function testNoCombinedCharacterMatches() {
        
        $charstring = $this->unicodeChar2string("\u00F6");   
        
        $prefix = "abc";
        $suffix = "xyz";
        $number = "10"; 
        $addressString = $prefix . $charstring . $suffix . " " . $number;
                
        $address = Helper::splitStreetAddress($addressString);
        // you may force netbeans output window encoding to use utf-8 by adding 
        // netbeans_default_options= "... -J-Dfile.encoding=UTF-8"
        // to <netbeans install folder>/etc/netbeans.conf
        
        print_r( "\naddressString: " . $addressString . "\n");
        print_r( "address[0]: " . ( isset( $address[0] ) ? $address[0] : "not set" ) . "\n" );
        print_r( "address[1]: " . ( isset( $address[1] ) ? $address[1] : "not set" ) . "\n" );
        print_r( "address[2]: " . ( isset( $address[2] ) ? $address[2] : "not set" ) . "\n" );
        
        $this->assertEquals( $prefix . $charstring . $suffix, $address[1]);
        $this->assertEquals( $number, $address[2]);                
    }
    
    // test unicode combined characters (i.e. U+00F6 (ö) as two code points -- U+006F (o) + U+0308 (¨), combining diaeresis)
    function testCombinedCharacterMatches() {
        
        $charstring = $this->unicodeChar2string("\u006F\u0308");   

        $prefix = "abc";
        $suffix = "xyz";
        $number = "10"; 
        $addressString = $prefix . $charstring . $suffix . " " . $number;

        $address = Helper::splitStreetAddress($addressString);
        // you may force netbeans output window encoding to use utf-8 by adding 
        // netbeans_default_options= "... -J-Dfile.encoding=UTF-8"
        // to <netbeans install folder>/etc/netbeans.conf
        
        print_r( "\naddressString: " . $addressString . "\n");
        print_r( "address[0]: " . ( isset( $address[0] ) ? $address[0] : "not set" ) . "\n" );
        print_r( "address[1]: " . ( isset( $address[1] ) ? $address[1] : "not set" ) . "\n" );
        print_r( "address[2]: " . ( isset( $address[2] ) ? $address[2] : "not set" ) . "\n" );
        
        $this->assertEquals( $prefix . $charstring . $suffix, $address[1]);
        $this->assertEquals( $number, $address[2]);                
    }    
}

?>
