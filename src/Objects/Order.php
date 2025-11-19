<?php

namespace Monta\CheckoutApiWrapper\Objects;

// alias for sibling must remain or not all autoloading will work
use Monta\CheckoutApiWrapper\Objects\Objectable as Objectable;

class Order extends Objectable
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
     * @return float[] - Keyed on type
     */
    public function toArray(): array
    {
        return [
            'OrderValueInclVat' => $this->total_incl,
            'OrderValueExclVat' => $this->total_excl,
        ];
    }
}