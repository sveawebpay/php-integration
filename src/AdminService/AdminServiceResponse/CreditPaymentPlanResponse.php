<?php
namespace Svea\AdminService;

require_once 'AdminServiceResponse.php';

/**
 * Handles the Svea Admin Web Service CancelPaymentPlanRows request response.
 *
 * @author ann-hal for Svea Ekonomi Ab | WebPay
 */
class CreditPaymentPlanResponse extends AdminServiceResponse {



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


