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
        // Get array values with only the keys that are a property
        $props = array_intersect_key(
            $converted,
            get_class_vars(Option::class)
        );
        // TODO also include shipper options in calculations
        return !empty($props) ? new Option(...$props) : null;
    }
}