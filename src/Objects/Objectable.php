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

    /** Public function for getting all properties (including protected) of this class
     *
     * @return string[]
     */
    public static function getVars(): array
    {
        // call static because we want the child class
        return get_class_vars(static::class);
    }

    /**
     * @param string $json
     * @return static|null
     */
    public static function constructFromJson(string $json): ?static
    {
        // Convert JSON into array, splat into property constructor
        $converted = json_decode($json, true);

        // Keep the entire data string in $data property
        $converted['additionalData'] = $converted;
        // Get array with only the keys that are a property (to splat into constructor)
        $props = array_intersect_key(
            $converted,
            // call `static` instead of `self` to call the child class
            static::getVars()
        );

        return !empty($props) ? new static(...$props) : null;
    }
}