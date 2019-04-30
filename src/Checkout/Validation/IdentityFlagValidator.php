<?php

namespace Svea\WebPay\Checkout\Validation;

/**
 * Class IdentityFlagValidator
 * @package Svea\Svea\WebPay\WebPay\Checkout\Validation
 */
class IdentityFlagValidator
{
    public function validate($flags, $errors)
    {
        $reflection = new \ReflectionClass('Svea\WebPay\Checkout\Model\IdentityFlags');
        $consts = $reflection->getConstants();

        foreach($flags as $flag)
        {
            $valid = false;
            foreach($consts as $const)
            {
                if($const == $flag)
                {
                    $valid = true;
                    break;
                }
            }
            if($valid == false)
            {
                $errors['NonValidIdentityFlag'] = $flag . " is not a valid identity flag.";
            }
        }

        return $errors;
    }
}
