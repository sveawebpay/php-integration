<?php
namespace Svea\WebService\WebServiceSoap;

class SveaIdentity {

    /**
     * Dynamically crate an instancevariable depending on Company or Individual
     * @param type $bool. False means Individual
     */
    public function __construct($bool = false) {
        //if Individual
        if ($bool == FALSE) {
            $this->FirstName = "";
            $this->LastName = "";
            $this->Initials = "";
            $this->BirthDate = "";
            //if Company
        } else {
            $this->CompanyVatNumber = "";
        }
    }
}
