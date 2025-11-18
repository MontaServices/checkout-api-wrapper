<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 11/18/2025 12:36
 */
namespace Monta\CheckoutApiWrapper\Objects;

abstract class Objectable
{
    /** @var array - Source data */
    protected array $additionalData = [];

    /**
     * @param array $additionalData
     * @return $this
     */
    public function setAdditionalData(array $additionalData): static
    {
        $this->additionalData = $additionalData;
        return $this;
    }

    /**
     * @param string|null $key
     * @return mixed
     */
    protected function getAdditionalData(string $key = null): mixed
    {
        if ($key) {
            return $this->additionalData[$key] ?? null;
        } else {
            // Otherwise return the entire array
            return $this->additionalData;
        }
    }

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
     * Call as `static` instead of `self` for child class
     *
     * @return string[]
     */
    public static function getVars(): array
    {
        // call static because we want the child class
        return get_class_vars(static::class);
    }

    /**
     * @param string $json - JSON-encoded array of properties
     * @return static|null
     */
    public static function constructFromJson(string $json): ?static
    {
        // Convert JSON into array
        return static::construct(json_decode($json, true));
    }

    /** Construct object from array
     *
     * @param array $data
     * @return static|null
     */
    public static function construct(array $data): ?static
    {
        // Get array with only the keys that are a property (to splat into constructor)
        $props = array_intersect_key(
            $data,
            // call `static` instead of `self` to call the child class
            static::getVars()
        );

        return !empty($props) ?
            // construct class
            (new static(...$props))
                // keep the source data
                ->setAdditionalData($data) : null;
    }

}