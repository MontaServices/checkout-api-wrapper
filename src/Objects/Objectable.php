<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 11/18/2025 12:36
 */
namespace Monta\CheckoutApiWrapper\Objects;

abstract class Objectable
{
    /** @var string - Shipper images are located here, grouped on ShipperGroupName (placeholder) */
    protected const string SHIPPER_IMAGE_URL = "https://cdn.monta.nl/PublicFiles/Images/shippers/%s/icon.svg";

    /** @var array - Source data */
    protected array $additionalData = [];

    /**
     * @param array $additionalData
     * @return $this
     */
    protected function setAdditionalData(array $additionalData): static
    {
        // Remove any nested data
        unset($additionalData['additionalData']);
        $this->additionalData = $additionalData;
        return $this;
    }

    /** Update data array
     *
     * @param array $additionalData
     * @return $this
     */
    public function updateAdditionalData(array $additionalData): static
    {
        // update array, overwrite existing keys
        $this->additionalData = array_merge($this->additionalData, $additionalData);
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
    protected function getImageUrl(string $value = null): string
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
     * @param string|null $className
     * @return static|null
     */
    public static function construct(array $data, string $className = null): ?static
    {
        // Use the passed className or use the class that was called
        if (!$className) {
            $className = static::class;
        }

        // Get array with only the keys that are a property (to splat into constructor)
        $props = array_intersect_key(
            $data,
            // get all properties from specific child class
            $className::getVars()
        );

        return !empty($props) ?
            // construct class
            (new $className(...$props))
                // keep the source data
                ->setAdditionalData($data) : null;
    }
}