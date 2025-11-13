<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 11/13/2025 12:00
 */
namespace Monta\CheckoutApiWrapper\Service;

use Monta\CheckoutApiWrapper\Objects\Option;

class Options
{
    /** Convert JSON string from selected option to Option object
     *
     * @param string $json
     * @return Option|null
     */
    public function convertOption(string $json): ?Option
    {
        // Convert JSON into array, splat into property constructor
        $converted = json_decode($json, true);
        $props = [];
        // Copy each field into property
        foreach ($converted as $key => $value) {
            if (property_exists(Option::class, $key)) {
                $props[$key] = $value;
            }
        }
        // TODO also include shipper options in calculations
        return !empty($props) ? new Option(...$props) : null;
    }
}