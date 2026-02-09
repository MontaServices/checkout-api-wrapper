<?php

namespace Monta\CheckoutApiWrapper\Objects;

// alias for sibling must remain or not all autoloading will work
use Monta\CheckoutApiWrapper\Objects\Objectable as Objectable;
use Monta\CheckoutApiWrapper\Traits\CachedOptions;

/**
 * Class represents either delivery and pickup option, once selected.
 */
class Option extends Objectable
{
    use CachedOptions;

    protected const string DELIVERY_TYPE = 'delivery';

    protected const string PICKUP_TYPE = 'pickup';

    /** Shared properties between both classes
     *
     * @param string $code
     * @param string $displayName
     * @param float|null $price
     * @param string|null $priceFormatted
     * @param string|bool|null $imageUrl - FALSE to explicitly hide image
     * @param string|null $shipperGroupName
     */
    public function __construct(
        public string $code,
        public string $displayName,
        public ?float $price = null,
        public ?string $priceFormatted = null,
        public string|bool|null $imageUrl = "",
        protected ?string $shipperGroupName = null,
    )
    {
    }

    /** Custom constructor logic in here
     *
     * @param array $data
     * @param string|null $className
     * @return ShippingOption|PickupPoint|null - Passing className means something else is returned
     */
    public static function construct(array $data, ?string $className = null): ?static
    {
        // Map type to classname
        switch (self::determineType($data)) {
            case self::DELIVERY_TYPE:
                $className = ShippingOption::class;
                break;
            case self::PICKUP_TYPE:
                $className = PickupPoint::class;
                break;
        }
        return parent::construct($data, $className);
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return string|bool|null
     */
    public function getImageUrl(): string|bool|null
    {
        return $this->imageUrl;
    }

    /** Set Monta CDN image URL based on other property
     *
     * @param string $imageUrl
     * @return void
     */
    public function setImageUrl(string $imageUrl): void
    {
        if ($imageUrl) {
            $this->imageUrl = sprintf(self::SHIPPER_IMAGE_URL, $imageUrl);
        }
    }

    /**
     * @return string|null
     */
    public function getShipperGroupName(): ?string
    {
        return $this->shipperGroupName;
    }

    /**
     * @param bool $throwOnFail - Throw exception if validation fails, otherwise return false
     * @return bool - Validation success
     * @throws \Exception
     */
    public function validate(bool $throwOnFail = true): bool
    {
        $valid = false;
        $errorMsg = 'Invalid Option, please try again';
        // Retrieve the cached option as the selected Option
        if ($cachedOption = $this->retrieveOption($this)) {
            // Cached Option now has the same `selectedShipperOptions` as the selected Option
            // Check if Option total price is equal to cached price
            // Never compare floats directly in PHP, always use epsilon precision difference
            if (abs($this->getPrice(true) - $cachedOption->getPrice(true)) < PHP_FLOAT_EPSILON) {
                $valid = true;
            } else {
                $errorMsg = 'Selected option `' . $this->getCode() . '` has incorrect price (' . $this->getPrice(true) . ') compared to validation cache. (' . $cachedOption->getPrice(true) . ')';
            }
        } else {
            $errorMsg = 'Cannot validate option `' . $this->getCode() . '` against cache!';
        }

        if (!$valid && $throwOnFail) {
            throw new \Exception($errorMsg);
        }

        // Return validation result
        return $valid;
    }

    /** Convert selected Option to JSON in proper structure. Works on both Delivery or PickupOption.
     * Output format determined by old Montapacking module output for backwards compatibility
     *
     * @return string - JSON string with all its data ready for DB writing or API output
     */
    public function toJson(): string
    {
        $type = $this->getShippingType();
        $additionalInfo = [
            'code' => $this->getCode(),
            'price' => $this->getPrice(false), // only base shipping price
            'total_price' => $this->getPrice(true), // including options
        ];
        $details = [
            'short_code' => $this->getOriginalData('shipper'),
        ];
        switch ($type) {
            /** Delivery specific fields */
            case self::DELIVERY_TYPE:
                /** @var ShippingOption $this */
                // Options is just an array of codes, total_price includes their price
                $details['options'] = $this->getSelectedShipperOptions(onlyColumn: 'code');
                $additionalInfo['name'] = $this->getOriginalData('displayName');
                $additionalInfo['date'] = $this->getDesiredDeliveryDate();
                $additionalInfo['time'] = $this->getFrom() . " - " . $this->getTo();
                break;
            /** Pickup specific output */
            case self::PICKUP_TYPE:
                /** @var PickupPoint $this */
                $details['short_code'] = $this->getShipperCode();
                // Old module converted each of these fields in the frontend
                $additionalInfo += [
                    'city' => $this->getCity(),
                    'code_pickup' => $this->get_shipper_options_with_value(),
                    'company' => $this->getCompany(),
                    'country' => $this->getCountryCode(),
                    'housenumber' => $this->getHouseNumber(),
                    'postal' => $this->getPostalCode(),
                    'shipper' => $this->getShipperCode(),
                    'street' => $this->getStreet(),
                    'description' => $this->getDescription(),
                ];
        }

        // Put together all the data, just as the old module did from frontend
        $data = [
            'type' => $type,
            // Details was an array of one JSON object in old module
            'details' => [$details],
            // This is an array of one JSON object
            'additional_info' => [$additionalInfo],
        ];

        return json_encode($data);
    }

    /** Determine shipping type
     *
     * @return string
     */
    protected function getShippingType(): string
    {
        return self::determineType($this->getOriginalData());
    }

    /** public method for checking if Option is pickup
     *
     * @return bool
     */
    public function isPickup(): bool
    {
        return ($this->getShippingType() == self::PICKUP_TYPE);
    }

    /** Determine option type based on data
     * Since frontend just passes JSON data without classname, that information is lost
     *
     * @param array $data
     * @return string
     */
    protected static function determineType(array $data): string
    {
        // Pickup point has no delivery type but has a postal code
        if (empty($data['deliveryType']) && !empty($data['postalCode'])) {
            return self::PICKUP_TYPE;
        }
        return self::DELIVERY_TYPE;
    }
}