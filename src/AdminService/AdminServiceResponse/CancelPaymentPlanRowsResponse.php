<?php
namespace Svea\AdminService;

require_once 'AdminServiceResponse.php';

/**
 * Handles the Svea Admin Web Service CreditInvoiceRows request response.
 *
 * @author Kristian Grossman-Madsen
 */
class CancelPaymentPlanRowsResponse extends AdminServiceResponse {



    function __construct($message) {
         $this->formatObject($message);
    }


}

// raw response message example:
//
//stdClass Object
//(
//    [ErrorMessage] =>
//    [ResultCode] => 0
//)


