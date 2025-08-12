<?php

namespace Monta\CheckoutApiWrapper\Objects;

class Product
{
    /**
     * @var string
     */
    public string $sku;
    /**
     * @var int
     */
    public int $lengthMm;
    /**
     * @var int
     */
    public int $widthMm;
    /**
     * @var int
     */
    public int $heightMm;
    /**
     * @var int
     */
    public int $weightGrammes;
    /**
     * @var int
     */
    public int $quantity;
    /**
     * @var float
     */
    public float $price;

    /**
     * @param string $sku
     * @param int $lengthMm
     * @param int $widthMm
     * @param int $heightMm
     * @param int $weightGrammes
     * @param int $quantity
     * @param float $price
     */
    public function __construct(
        string $sku,
        int $lengthMm,
        int $widthMm,
        int $heightMm,
        int $weightGrammes,
        int $quantity,
        float $price,
    )
    {
        $this->setSku($sku);
        $this->setLength($lengthMm);
        $this->setWidth($widthMm);
        $this->setHeight($heightMm);
        $this->setWeight($weightGrammes);
        $this->setQuantity($quantity);
        if ($price != null) {
            $this->setPrice($price);
        }
    }

    /**
     * @param $price
     *
     * @return $this
     */
    public function setPrice($price): Product
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @param $sku
     *
     * @return $this
     */
    public function setSku($sku): Product
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @param $length
     *
     * @return $this
     */
    public function setLength($length): Product
    {
        $this->lengthMm = $length;
        return $this;
    }

    /**
     * @param $width
     *
     * @return $this
     */
    public function setWidth($width): Product
    {
        $this->widthMm = $width;
        return $this;
    }

    /**
     * @param $height
     *
     * @return $this
     */
    public function setHeight($height): Product
    {
        $this->heightMm = $height;
        return $this;
    }

    /**
     * @param $weight
     *
     * @return $this
     */
    public function setWeight($weight): Product
    {
        $this->weightGrammes = $weight;
        return $this;
    }

    /**
     * @param $quantity
     *
     * @return $this
     */
    public function setQuantity($quantity): Product
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'SKU' => $this->sku,
            'LengthMm' => $this->lengthMm,
            'WidthMm' => $this->widthMm,
            'HeightMm' => $this->heightMm,
            'WeightGrammes' => $this->weightGrammes,
            'Quantity' => $this->quantity,
            'Price' => $this->price
        ];
    }
}
