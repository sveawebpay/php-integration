<?php
namespace Svea\WebService\WebServiceSoap;

/**
 * Auth object, holder of
 * Username
 * Password
 * ClientNumber
 */
class SveaAuth {

    public $Username;
    public $Password;
    public $ClientNumber;
    
    /**
     * creates a SveaAuth instance w/the given username, password & clientnumber  
     * 
     * @param string $Username
     * @param string $Password
     * @param string $ClientNumber
     */
    function __construct( $Username = NULL, $Password = NULL, $ClientNumber = NULL ) {                
        if( $Username ) $this->Username = $Username;
        if( $Password ) $this->Password = $Password;
        if( $ClientNumber ) $this->ClientNumber = $ClientNumber; 
    }
}