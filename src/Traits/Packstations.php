<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 2/12/2026 17:16
 */
namespace Monta\CheckoutApiWrapper\Traits;

trait Packstations
{
    public const string CUSTOMER_POSTNUMBER_PREFIX = "DHLPCPostNummer_";

    public const string POINT_PACKSTATION_PREFIX = "PackingStationCode_";

    /** Whether this PUDO is a Packstation
     *
     * @return bool
     */
    public function isPackstation(): bool
    {
        return str_contains($this->getShipperOptionsWithValue(), self::POINT_PACKSTATION_PREFIX);
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
     * @param string $addValue - New value to add to CSV string
     * @return void
     */
    public function addShipperOptionsWithValue(string $addValue): void
    {
        // Postnumber always contains this prefix
        $newValue = self::CUSTOMER_POSTNUMBER_PREFIX . $addValue;

        // explode and trim CSV into array
        $codes = array_filter(
            array_map('trim', explode(',', $this->shipperOptionsWithValue ?? '')));

        $found = false;

        foreach ($codes as $key => $code) {
            if (str_starts_with($code, self::CUSTOMER_POSTNUMBER_PREFIX)) {
                // replace the old postnumber
                $codes[$key] = $newValue;
                $found = true;
                break; // only one expected, end loop
            }
        }

        if (!$found) {
            $codes[] = $newValue;
        }

        // Implode back into CSV string
        $this->shipperOptionsWithValue = implode(',', $codes);
    }
}