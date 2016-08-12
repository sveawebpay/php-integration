<?php

namespace Svea\WebPay\Test\UnitTest\Helper;

use Svea\WebPay\Helper\Helper;

class SplitAddressTest extends \PHPUnit_Framework_TestCase
{

    function debugPrintSplitStreetAddressOutput($address)
    {
        $PRINT_TO_CONSOLE = false;      // set to true to get debug output

        // you may force netbeans output window encoding to use utf-8 by adding 
        // netbeans_default_options= "... -J-Dfile.encoding=UTF-8"
        // to <netbeans install folder>/etc/netbeans.conf
        if ($PRINT_TO_CONSOLE) {
            print_r("\naddress[0]: " . (isset($address[0]) ? '"' . $address[0] . '"' : "not set") . "\n");
            print_r("address[1]: " . (isset($address[1]) ? '"' . $address[1] . '"' : "not set") . "\n");
            print_r("address[2]: " . (isset($address[2]) ? '"' . $address[2] . '"' : "not set") . "\n");
        }
    }

    // splitStreetAddress
    function testStreet()
    {
        $address = Helper::splitStreetAddress("Street");

        $this->debugPrintSplitStreetAddressOutput($address);

        $this->assertEquals("Street", $address[1]);
        $this->assertEquals("", $address[2]);
    }

    function testStreet_10()
    {
        $address = Helper::splitStreetAddress("Street 10");

        $this->debugPrintSplitStreetAddressOutput($address);

        $this->assertEquals("Street", $address[1]);
        $this->assertEquals("10", $address[2]);
    }

    function testUnicodeTwoCodePointGraphemeFirstInStreetnameStreet_10()
    {
        $cc = $this->unicodeChar2string("\u006F\u0308");    // cc for combined codepoints
        $address = Helper::splitStreetAddress($cc . "Street 10");

        $this->debugPrintSplitStreetAddressOutput($address);

        $this->assertEquals($cc . "Street", $address[1]);
        $this->assertEquals("10", $address[2]);
    }

    function testUnicodeTwoCodePointGraphemeLastInStreetnameStreet_10()
    {
        $cc = $this->unicodeChar2string("\u006F\u0308");    // cc for combined codepoints
        $address = Helper::splitStreetAddress("Street" . $cc . " 10");

        $this->debugPrintSplitStreetAddressOutput($address);

        $this->assertEquals("Street" . $cc, $address[1]);
        $this->assertEquals("10", $address[2]);
    }

    function testUnicodeTwoCodePointGraphemeInsideStreetnameStreet_10()
    {
        $cc = $this->unicodeChar2string("\u006F\u0308");    // cc for combined codepoints
        $address = Helper::splitStreetAddress("Str" . $cc . "eet" . " 10");

        $this->debugPrintSplitStreetAddressOutput($address);

        $this->assertEquals("Str" . $cc . "eet", $address[1]);
        $this->assertEquals("10", $address[2]);
    }

    function test_Street_10()
    {
        $address = Helper::splitStreetAddress(" Street 10 ");

        $this->debugPrintSplitStreetAddressOutput($address);

        $this->assertEquals("Street", $address[1]);
        $this->assertEquals("10", $address[2]);
    }

    function testStreet_10bis()
    {
        $address = Helper::splitStreetAddress("Street 10bis");

        $this->debugPrintSplitStreetAddressOutput($address);

        $this->assertEquals("Street", $address[1]);
        $this->assertEquals("10bis", $address[2]);
    }

    function testStreet_10_bis()
    {
        $address = Helper::splitStreetAddress("Street 10 bis");

        $this->debugPrintSplitStreetAddressOutput($address);

        $this->assertEquals("Street", $address[1]);
        $this->assertEquals("10 bis", $address[2]);
    }

    function testStreet___10__bis()
    {
        $address = Helper::splitStreetAddress("Street   10  bis");

        $this->debugPrintSplitStreetAddressOutput($address);

        $this->assertEquals("Street", $address[1]);
        $this->assertEquals("10  bis", $address[2]);
    }

    function test3rd_street_11()
    {
        $address = Helper::splitStreetAddress("3rd street 11");

        $this->debugPrintSplitStreetAddressOutput($address);

        $this->assertEquals("3rd street", $address[1]);
        $this->assertEquals("11", $address[2]);
    }

    function test3rd_street_11bis()
    {
        $address = Helper::splitStreetAddress("3rd street 11bis");

        $this->debugPrintSplitStreetAddressOutput($address);

        $this->assertEquals("3rd street", $address[1]);
        $this->assertEquals("11bis", $address[2]);
    }

    function test3rd_street_11_bis()
    {
        $address = Helper::splitStreetAddress("3rd street 11 bis");

        $this->debugPrintSplitStreetAddressOutput($address);

        $this->assertEquals("3rd street", $address[1]);
        $this->assertEquals("11 bis", $address[2]);
    }

    function test_3rd___street___11___bis()
    {
        $address = Helper::splitStreetAddress(" 3rd   street   11   bis ");

        $this->debugPrintSplitStreetAddressOutput($address);

        $this->assertEquals("3rd   street", $address[1]);
        $this->assertEquals("11   bis", $address[2]);
    }

    function testSankt_Larsgatan_1_Lgh_1003()
    {
        $address = Helper::splitStreetAddress("Sankt Larsgatan 1 Lgh 1003");

        $this->debugPrintSplitStreetAddressOutput($address);

        $this->assertEquals("Sankt Larsgatan", $address[1]);
        $this->assertEquals("1 Lgh 1003", $address[2]);
    }

    //Svea testperson DK
    function testGate_42_23()
    {
        $address = Helper::splitStreetAddress("Gate 42 23");

        $this->debugPrintSplitStreetAddressOutput($address);

        $this->assertEquals("Gate", $address[1]);
        $this->assertEquals("42 23", $address[2]); // ok, see testInvoiceRequestNLAcceptedWithDoubleHousenumber
    }

    // decided not to implement this case, as it looks like a corner case w/"street 42" and housenumber 23 after    
//    //Svea testperson DK
//    function testGate_42_23(){
//        $address = Helper::splitStreetAddress("Gate 42 23");
//        $this->assertEquals( "Gate 42", $address[1]);
//        $this->assertEquals( "23", $address[2]);
//    }

    //Interpuncation in streetaddress
    function testStreetcomma_10()
    {
        $address = Helper::splitStreetAddress("Street, 10");

        $this->debugPrintSplitStreetAddressOutput($address);

        $this->assertEquals("Street", $address[1]);
        $this->assertEquals("10", $address[2]);
    }
    // decided not to implement this case, as it looks like a corner case    
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
    function testSingelstraat_10()
    {
        $address = Helper::splitStreetAddress("Singelstraat 10");

        $this->debugPrintSplitStreetAddressOutput($address);

        $this->assertEquals("Singelstraat", $address[1]);
        $this->assertEquals("10", $address[2]);
    }

    function test3e_laan_12()
    {
        $address = Helper::splitStreetAddress("3e laan 12");
        $this->debugPrintSplitStreetAddressOutput($address);
        $this->assertEquals("3e laan", $address[1]);
        $this->assertEquals("12", $address[2]);
    }

    function testHeeregracht_12bis()
    {
        $address = Helper::splitStreetAddress("Heeregracht 12bis");
        $this->debugPrintSplitStreetAddressOutput($address);
        $this->assertEquals("Heeregracht", $address[1]);
        $this->assertEquals("12bis", $address[2]);
    }

    //International characters
    function testÖnskevägen_10()
    {
        $address = Helper::splitStreetAddress("Önskevägen 10");
        $this->debugPrintSplitStreetAddressOutput($address);
        $this->assertEquals("Önskevägen", $address[1]);
        $this->assertEquals("10", $address[2]);
    }

    function testÅlandshav_10å()
    {
        $address = Helper::splitStreetAddress("Ålandshav 10å");
        $this->debugPrintSplitStreetAddressOutput($address);
        $this->assertEquals("Ålandshav", $address[1]);
        $this->assertEquals("10å", $address[2]);
    }

    function testÅväg_änna_10()
    {
        $address = Helper::splitStreetAddress("Åväg änna 10");
        $this->debugPrintSplitStreetAddressOutput($address);
        $this->assertEquals("Åväg änna", $address[1]);
        $this->assertEquals("10", $address[2]);
    }

    function testÄÅÖåäöÜü()
    {
        $address = Helper::splitStreetAddress("ÄÅÖåäöÜü");
        $this->debugPrintSplitStreetAddressOutput($address);
        $this->assertEquals("ÄÅÖåäöÜü", $address[1]);
    }

    function testÄÅÆÖØÜßäåæöøü_10()
    {
        $address = Helper::splitStreetAddress("ÄÅÆÖØÜßäåæöøü 10");
        $this->debugPrintSplitStreetAddressOutput($address);
        $this->assertEquals("ÄÅÆÖØÜßäåæöøü", $address[1]);
        $this->assertEquals("10", $address[2]);
    }

    function unicodeChar2string($unicode_char)
    {
        return json_decode('"' . $unicode_char . '"');
    }

    function testBaselineCharacterMatches()
    {
        $charstring = "ö";

        $prefix = "abc";
        $suffix = "xyz";
        $number = "10";
        $addressString = $prefix . $charstring . $suffix . " " . $number;

        $this->assertEquals("ö", $charstring);
        $this->assertEquals(2, strlen($charstring));

        $address = Helper::splitStreetAddress($addressString);
        $this->debugPrintSplitStreetAddressOutput($address);
        $this->assertEquals($prefix . $charstring . $suffix, $address[1]);
        $this->assertEquals($number, $address[2]);
    }

    // test unicode combined characters (i.e. U+00F6 (ö) as two code points -- U+006F (o) + U+0308 (¨), combining diaeresis)
    function testNoCombinedCharacterMatches()
    {

        $charstring = $this->unicodeChar2string("\u00F6");

        $prefix = "abc";
        $suffix = "xyz";
        $number = "10";
        $addressString = $prefix . $charstring . $suffix . " " . $number;

        $this->assertEquals("ö", $charstring);
        $this->assertEquals(2, strlen($charstring));

        $address = Helper::splitStreetAddress($addressString);
        $this->debugPrintSplitStreetAddressOutput($address);
        $this->assertEquals($prefix . $charstring . $suffix, $address[1]);
        $this->assertEquals($number, $address[2]);
    }

    // test unicode combined characters (i.e. U+00F6 (ö) as two code points -- U+006F (o) + U+0308 (¨), combining diaeresis)
    function testCombinedCharacterMatches()
    {

        $charstring = $this->unicodeChar2string("\u006F\u0308");

        $prefix = "abc";
        $suffix = "xyz";
        $number = "10";
        $addressString = $prefix . $charstring . $suffix . " " . $number;

        $this->assertNotEquals("ö", $charstring);                 // Not same as "ö", but prints the same to console using utf-8 output
        $this->assertEquals(3, strlen($charstring));              // Grapheme "ö" is represented by two code points => string w/length 3

        $address = Helper::splitStreetAddress($addressString);
        $this->debugPrintSplitStreetAddressOutput($address);
        $this->assertEquals($prefix . $charstring . $suffix, $address[1]);
        $this->assertEquals($number, $address[2]);
    }

    function testUnicodeRegExExample()
    {
        $match = preg_match("/([\x{06F0}-\x{06F9}]+)/u", '۱۲۳۴۵۶۷۸۹۰', $address);
        $this->debugPrintSplitStreetAddressOutput($address);
        $this->assertEquals(1, $match);
    }
}

