<?php

namespace Svea\WebPay\Checkout\Model;

use Svea\WebPay\BuildOrder\Validator\ValidationException;

/**
 * Class PresetValue hold information about additional values
 * that can be set in checkout order
 * @package Svea\Svea\WebPay\WebPay\Checkout\Model
 */
class PresetValue
{
    const NATIONAL_ID = 'nationalId';
    const EMAIL_ADDRESS = 'emailAddress';
    const PHONE_NUMBER = 'phoneNumber';
    const POSTAL_CODE = 'postalCode';
    const IS_COMPANY = 'isCompany';

    /**
     * @var string
     */
    protected $typeName;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var bool
     */
    protected $isReadonly;

    /**
     * @return string
     */
    public function getTypeName()
    {
        return $this->typeName;
    }

    /**
     * @param string $typeName
     * @return PresetValue
     * @throws ValidationException
     */
    public function setTypeName($typeName)
    {
        $this->validateInput($typeName);

        $this->typeName = $typeName;

        return $this;
    }

    /**
     * @param $typeName
     * @throws \Svea\WebPay\BuildOrder\Validator\ValidationException
     */
    private function validateInput($typeName)
    {
        $constantListValue = $this->getConstantListValues();

        if (!in_array($typeName, $constantListValue)) {
            throw new ValidationException('This Typename "' . $typeName . '" is not supported. Supported types are: ' .
                join(', ', $constantListValue) . '.');
        }
    }

    /**
     * Return values of all defined constants in this class
     *
     * @return array
     */
    private function getConstantListValues()
    {
        $rc = new \ReflectionClass($this);
        $constantList = array_values($rc->getConstants());

        return $constantList;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return PresetValue
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isIsReadonly()
    {
        return $this->isReadonly;
    }

    /**
     * @param boolean $isReadonly
     * @return PresetValue
     */
    public function setIsReadonly($isReadonly)
    {
        $this->isReadonly = $isReadonly;

        return $this;
    }


    /**
     * @return array
     */
    public function returnPresetArray()
    {
        return array(
            'typeName' => $this->typeName,
            'value' => $this->value,
            'isReadonly' => $this->isReadonly,
        );
    }
}
