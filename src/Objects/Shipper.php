<?php

namespace Monta\CheckoutApiWrapper\Objects;

class Shipper
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
