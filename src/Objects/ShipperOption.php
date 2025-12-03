<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 12/3/2025 13:14
 */
namespace Monta\CheckoutApiWrapper\Objects;

class ShipperOption extends Objectable
{
    /**
     * @param string $code
     * @param string $description - display name
     * @param float|null $price
     * @param string|null $priceFormatted
     */
    public function __construct(
        public string $code,
        public string $description = "",
        public ?float $price = null,
        public ?string $priceFormatted = null,
    )
    {
    }
}