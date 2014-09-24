<?php
namespace Svea\AdminService\AdminSoap;

class Authentication {
    public $Password;
    public $Username;
    
    /**
     * AdminService request Authentication 
     * @param string $username
     * @param string $password
     */
    function __construct( $username, $password ) {
        $this->Password = new \SoapVar( $password, XSD_STRING,"-","--","Password","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->Username = new \SoapVar( $username, XSD_STRING,"-","--","Username","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
    }
}