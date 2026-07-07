<?php

namespace Svea\WebPay\WebService\SveaSoap;

class SveaIdentity
{
    public $FirstName;
    public $LastName;
    public $Initials;
    public $BirthDate;
    public $CompanyVatNumber;

    /**
     * Dynamically crate an instancevariable depending on Company or Individual
     * @param bool $bool - False means Individual
     */
    public function __construct($bool = false)
    {
        //if Individual
        if ($bool == FALSE) {
            $this->FirstName = "";
            $this->LastName = "";
            $this->Initials = "";
            $this->BirthDate = "";

        } //if Company
        else {
            $this->CompanyVatNumber = "";
        }
    }
}
