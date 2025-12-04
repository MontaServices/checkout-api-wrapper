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
     * @param string|null $imageUrl
     * @param string $shipperGroupName
     */
    public function __construct(
        public string $code,
        public string $displayName,
        public ?float $price = null,
        public ?string $priceFormatted = null,
        public ?string $imageUrl = "",
        protected string $shipperGroupName = "",
    )
    {
    }

    /** Custom constructor logic in here
     *
     * @param array $data
     * @param string|null $className
     * @return ShippingOption|PickupPoint|null - Passing className means something else is returned
     */
    public static function construct(array $data, string $className = null): ?static
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
     * @param string|null $imageUrl
     */
    public function setImageUrl(?string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return string
     */
    public function getShipperGroupName(): string
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
        // TODO maybe move all these specifics to subclasses?
        switch ($type) {
            /** Delivery specific fields */
            case self::DELIVERY_TYPE:
                // Options is just an array of codes, total_price includes their price
                $details['options'] = $this->getSelectedShipperOptions(onlyColumn: 'code');
                $additionalInfo['name'] = $this->getOriginalData('displayName');
                $additionalInfo['date'] = $this->getDesiredDeliveryDate();
                $additionalInfo['time'] = $this->getFrom() . " - " . $this->getTo();
                break;
            /** Pickup specific output */
            case self::PICKUP_TYPE:
                // Construct object back from array
                // This is possible because $originalData started as a PickupPoint, encoded to JSON for frontend.
                // Then returned from frontend to Quote, where it was saved as JSON string.
                // Then decoded back to array in Monta\CheckoutApiWrapper\Objects\Objectable::constructFromJson()
                // Which could return anything but at this point we know it was a Pickup option.
                $pickup = PickupPoint::construct($this->getOriginalData());
                $details['short_code'] = $pickup->getShipperCode();
                // Pickup point has address in originaldata
                // Old module converted each of these fields in the frontend
                $additionalInfo += [
                    'city' => $pickup->getCity(),
                    'code_pickup' => $pickup->get_shipper_options_with_value(),
                    'company' => $pickup->getCompany(),
                    'country' => $pickup->getCountryCode(),
                    'housenumber' => $pickup->getHouseNumber(),
                    'postal' => $pickup->getPostalCode(),
                    'shipper' => $pickup->getShipperCode(),
                    'street' => $pickup->getStreet(),
                    'description' => $pickup->getDescription(),
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

    /** Determine option type based on data
     * Since frontend just passes JSON data without classname, that information is lost
     *
     * @param array $data
     * @return string
     */
    protected static function determineType(array $data): string
    {
        // Delivery option has this field
        if (!empty($data['deliveryType'])) {
            return self::DELIVERY_TYPE;
        } else if (!empty($data['postalCode'])) {
            // Pickup point has no delivery type but has a postal code
            return self::PICKUP_TYPE;
        }
    }
}