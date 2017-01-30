<?php

namespace Svea\WebPay\Checkout\Response\Model;

class OrderRow
{
    const MINOR_CURRENCY = 100;

    /**
     * @var int $rowId
     */
    private $rowId;

    /**
     * @var int $articleNumber
     */
    private $articleNumber;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var int $quantity
     */
    private $quantity;

    /**
     * @var int $unitPrice
     */
    private $unitPrice;

    /**
     * @var int $discountPercent
     */
    private $discountPercent;

    /**
     * @var int $vatPercent
     */
    private $vatPercent;

    /**
     * @var string $unit
     */
    private $unit;

    /**
     * @var int $temporaryReference
     */
    private $temporaryReference;


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
        $this->rowId = $data['RowNumber'];
    }

    public function getRefactoredData()
    {
        return array(
            'ArticleNumber' => $this->articleNumber,
            'Name' => $this->name,
            'Quantity' => $this->quantity / $this::MINOR_CURRENCY,
            'UnitPrice' => $this->unitPrice / $this::MINOR_CURRENCY,
            'VatPercent' => $this->vatPercent / $this::MINOR_CURRENCY,
            'DiscountPercent' => $this->discountPercent,
            'Unit' => $this->unit,
            'TemporaryReference' => $this->temporaryReference,
            'RowId' => $this->rowId
        );
    }
}