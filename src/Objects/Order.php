<?php

namespace Monta\CheckoutApiWrapper\Objects;

class Order
{
    /**
     * @param float $total_incl
     * @param float $total_excl
     */
    public function __construct(
        public float $total_incl,
        public float $total_excl,
    )
    {
        $this->setIncl($total_incl);
        $this->setExcl($total_excl);
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
