<?php

namespace Monta\CheckoutApiWrapper\Objects;

class Shipper
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $code;

    /**
     * @param $name
     * @param $code
     */
    public function __construct($name, $code)
    {
        $this->setName($name);
        $this->setCode($code);
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name): Shipper
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param $code
     *
     * @return $this
     */
    public function setCode($code): Shipper
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name
        ];
    }
}
