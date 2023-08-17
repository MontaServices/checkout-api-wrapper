<?php

namespace Monta\CheckoutApiWrapper\Objects;

class Option
{
    /**
     * @var string
     */
    public string $code;

    /**
     * @var string
     */
    public string $description;

    /**
     * @var float
     */
    public float $price;

    /**
     * @var string
     */
    public string $priceFormatted;

    /**
     * @param $code
     * @param $description
     * @param $price
     * @param $priceFormatted
     */
    public function __construct($code, $description, $price, $priceFormatted)
    {
        $this->code = $code;
        $this->description = $description;
        $this->price = $price;
        $this->priceFormatted = $priceFormatted;
    }

    /**
     * @return string
     */
    public function getPriceFormatted(): string
    {
        return $this->priceFormatted;
    }

    /**
     * @param $priceFormatted
     * @return void
     */
    public function setPriceFormatted($priceFormatted): void
    {
        $this->priceFormatted = $priceFormatted;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return void
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param $price
     * @return void
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $option = null;
        foreach ($this as $key => $value) {
            $option[$key] = $value;
        }

        return $option;
    }
}
