<?php

namespace Monta\CheckoutApiWrapper\Objects;

class Product
{
    /**
     * @param string $sku
     * @param int $quantity
     * @param int $lengthMm
     * @param int $widthMm
     * @param int $heightMm
     * @param int $weightGrammes
     * @param float $price
     */
    public function __construct(
        public string $sku,
        public int $quantity,
        public int $lengthMm,
        public int $widthMm,
        public int $heightMm,
        public int $weightGrammes,
        public float $price,
    )
    {
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
