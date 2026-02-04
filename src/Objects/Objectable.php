<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 11/18/2025 12:36
 */

namespace Monta\CheckoutApiWrapper\Objects;

abstract class Objectable
{
    /** @var string - Shipper images are located here, grouped on ShipperGroupName */
    protected const string SHIPPER_IMAGE_URL = "https://cdn.monta.nl/PublicFiles/Images/shippers/%s/icon.svg";

    /** @var array - Original data from API, packed and unpacked to JSON by frontend */
    protected array $originalData = [];

    /**
     * @param string|null $key
     * @return mixed
     */
    protected function getOriginalData(?string $key = null): mixed
    {
        if ($key) {
            return $this->originalData[$key] ?? null;
        } else {
            // Otherwise return the entire array
            return $this->originalData;
        }
    }

    /**
     * @param array $originalData
     * @return $this
     */
    protected function setOriginalData(array $originalData): static
    {
        // Remove any nested data
        unset($originalData['originalData']);
        $this->originalData = $originalData;
        return $this;
    }

    /** Update data
     *
     * @param array $originalData
     * @return $this
     * @deprecated - No longer used anywhere and is illogical. Real properties can be updated
     */
    public function updateOriginalData(array $originalData): static
    {
        // update array, overwrite existing keys
        $this->originalData = array_merge($this->originalData, $originalData);
        return $this;
    }

    /** Many child classes have a code property
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /** Get Monta CDN image URL based on shipper group name
     *
     * @param string|null $value
     * @return string
     */
    public function getImageUrl(?string $value = null): string
    {
        // Use passed value, otherwise fallback to Monta image
        // TODO this will not catch missing images when $value is a nonexistent image
        return sprintf(self::SHIPPER_IMAGE_URL, $value ?? "monta");
    }

    /** TODO is this not just getVars()?
     *
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
     * @return static|null|ShippingOption|PickupPoint
     */
    public static function constructFromJson(string $json): static|null|ShippingOption|PickupPoint
    {
        // Convert JSON into array
        return static::construct(json_decode($json, true));
    }

    /** Construct object from array
     *
     * @param array $data
     * @param string|null $className
     * @return static|null
     */
    public static function construct(array $data, ?string $className = null): ?static
    {
        // Use the passed className or use the class that was called
        if (!$className) {
            $className = static::class;
        }

        // Get array with only the keys that are a property (to splat into constructor)
        $props = array_intersect_key(
            $data,
            // get all properties from specific child class
            $className::getVars(),
        );

        return !empty($props) ?
            // construct class
            (new $className(...$props))
                // keep the source data
                ->setOriginalData($data) : null;
    }
}