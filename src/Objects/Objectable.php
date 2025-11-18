<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 11/18/2025 12:36
 */
namespace Monta\CheckoutApiWrapper\Objects;

class Objectable
{
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

    /** Generic function for converting object to JSON
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}