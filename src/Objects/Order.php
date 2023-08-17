<?php

namespace Monta\CheckoutApiWrapper\Objects;

class Order
{
    /**
     * @var float
     */
    public float $total_incl;

    /**
     * @var float
     */
    public float $total_excl;

    /**
     * @param $incl
     * @param $excl
     */
    public function __construct($incl, $excl)
    {

        $this->setIncl($incl);
        $this->setExcl($excl);
    }

    /**
     * @param $incl
     * @return $this
     */
    public function setIncl($incl): Order
    {
        $this->total_incl = $incl;
        return $this;
    }

    /**
     * @param $excl
     * @return $this
     */
    public function setExcl($excl): Order
    {
        $this->total_excl = $excl;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'OrderValueInclVat' => $this->total_incl,
            'OrderValueExclVat' => $this->total_excl,
        ];
    }
}
