<?php

namespace Monta\CheckoutApiWrapper\Objects;

/**
 * Class Option
 *
 */
class Option
{

    public $code;
    public $description;
    public $price;
    public $priceFormatted;

    /**
     * @return mixed
     */
    public function getPriceFormatted()
    {
        return $this->priceFormatted;
    }

    /**
     * @param mixed $priceFormatted
     */
    public function setPriceFormatted($priceFormatted): void
    {
        $this->priceFormatted = $priceFormatted;
    }

    /**
     * @param $code
     * @param $description
     * @param $price
     */
    public function __construct($code, $description, $price, $priceFormatted)
    {
        $this->code = $code;
        $this->description = $description;
        $this->price = $price;
        $this->priceFormatted = $priceFormatted;
    }


    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code): void
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @return array
     */
    public function toArray()
    {

        $option = null;
        foreach ($this as $key => $value) {
            $option[$key] = $value;
        }

        return $option;
    }
}
