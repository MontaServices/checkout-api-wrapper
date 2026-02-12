<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 2/12/2026 17:16
 */
namespace Monta\CheckoutApiWrapper\Traits;

trait Packstations
{
    /** Whether this PUDO is a Packstation
     *
     * @return bool
     */
    public function isPackstation(): bool
    {
        return str_contains($this->shipperOptionsWithValue, "PackingStationCode_");
    }

    /**
     * Field contains o.a. the Postnumber for DHLDE packstations
     *
     * @return string
     */
    protected function getShipperOptionsWithValue(): string
    {
        return $this->shipperOptionsWithValue;
    }

    /**
     * @param string $addValue - New shipperoption to add to CSV string
     * @return void
     */
    public function addShipperOptionsWithValue(string $addValue): void
    {
        // Get property from CSV string as array
        $codes = array_filter(
            array_map('trim', explode(',', $this->shipperOptionsWithValue ?? '')),
        );

        // TODO every change now adds a new code to line. Refactor to always just write "Postnumber_XX,{postnumber}"
        if (!in_array($addValue, $codes, true)) {
            $codes[] = $addValue;
        }

        // Implode back into CSV string
        $this->shipperOptionsWithValue = implode(',', $codes);
    }
}