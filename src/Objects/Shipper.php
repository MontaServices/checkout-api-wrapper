<?php

namespace Monta\CheckoutApiWrapper\Objects;

// alias for sibling must remain or not all autoloading will work
use Monta\CheckoutApiWrapper\Objects\Objectable as Objectable;

class Shipper extends Objectable
{

    /**
     * @param string $name
     * @param string $code
     */
    public function __construct(
        public string $name,
        public string $code
    )
    {
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