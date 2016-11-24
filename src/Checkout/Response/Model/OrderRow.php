<?php

namespace Svea\WebPay\Checkout\Response\Model;

class OrderRow
{
    const MINOR_CURRENCY = 100;

    /**
     * @var int $articleNumber
     */
    private $articleNumber;//  = 321

    /**
     * @var string $name
     */
    private $name;//  = Fork

    /**
     * @var int $quantity
     */
    private $quantity;//  = 200

    /**
     * @var int $unitPrice
     */
    private $unitPrice;//  = 1000

    /**
     * @var int $discountPercent
     */
    private $discountPercent;//  = 0

    /**
     * @var int $vatPercent
     */
    private $vatPercent;//  = 2500

    /**
     * @var string $unit
     */
    private $unit;//  =

    /**
     * @var int $temporaryReference
     */
    private $temporaryReference;//  = 231


    public function map($data)
    {
        $this->articleNumber = $data['ArticleNumber'];
        $this->name = $data['Name'];
        $this->quantity = $data['Quantity'];
        $this->unitPrice = $data['UnitPrice'];
        $this->discountPercent = $data['DiscountPercent'];
        $this->vatPercent = $data['VatPercent'];
        $this->temporaryReference = $data['TemporaryReference'];
        $this->unit = $data['Unit'];
    }

    public function getRefactoredData()
    {
        return array(
            'ArticleNumber' => $this->articleNumber,
            'Name'  => $this->name,
            'Quantity'  => $this->quantity / $this::MINOR_CURRENCY,
            'UnitPrice' => $this->unitPrice / $this::MINOR_CURRENCY,
            'VatPercent' => $this->vatPercent / $this::MINOR_CURRENCY,
            'DiscountPercent' => $this->discountPercent,
            'Unit' => $this->unit,
            'TemporaryReference'    => $this->temporaryReference,
        );
    }
}