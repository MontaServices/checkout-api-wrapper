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
    public int $length;
    /**
     * @var int
     */
    public int $width;
    /**
     * @var int
     */
    public int $height;
    /**
     * @var int
     */
    public int $weight;
    /**
     * @var int
     */
    public int $quantity;
	/**
	 * @var float
	 */
	public float $price_incl;

    /**
     * @param string $sku
     * @param int $length
     * @param int $width
     * @param int $height
     * @param int $weight
     * @param int $quantity
     * @param float $price
     */
    public function __construct(string $sku, int $length, int $width, int $height, int $weight, int $quantity, float $price)
    {

        $this->setSku($sku);
        $this->setLength($length);
        $this->setWidth($width);
        $this->setHeight($height);
        $this->setWeight($weight);
        $this->setQuantity($quantity);
		if($price != null) {
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
		$this->price_incl = $price;
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
        $this->length = $length;
        return $this;
    }

    /**
     * @param $width
     *
     * @return $this
     */
    public function setWidth($width): Product
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @param $height
     *
     * @return $this
     */
    public function setHeight($height): Product
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @param $weight
     *
     * @return $this
     */
    public function setWeight($weight): Product
    {
        $this->weight = $weight;
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
            'LengthMm' => $this->length,
            'WidthMm' => $this->width,
            'HeightMm' => $this->height,
            'WeightGrammes' => $this->weight,
            'Quantity' => $this->quantity,
        ];
    }
}
