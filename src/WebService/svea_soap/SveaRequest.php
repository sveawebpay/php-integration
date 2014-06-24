<?php
namespace Svea\WebService\WebServiceSoap;

/**
 * Container class for the request attributes.
 */
class SveaRequest {

    /**
     * mixed $request the request contents in a format ready for consumption by 
     * SveaDoRequest()
     */
    public $request;

    /**
     * 
     * @param mixed $request if not set, will do nothing
     */
    function __construct( $request = NULL ) {
        if( $request ) {
          $this->request = $request;  
        } 
    }
}
