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

        // Keep the entire data string in $data property
        $converted['additionalData'] = $converted;

        // Get array with only the keys that are a property (to splat into constructor)
        $props = array_intersect_key(
            $converted,
            Option::getVars()
        );

        return !empty($props) ? new Option(...$props) : null;
    }
}