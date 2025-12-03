<?php

namespace Monta\CheckoutApiWrapper\Objects;

// alias for sibling must remain or not all autoloading will work
use Monta\CheckoutApiWrapper\Objects\Objectable as Objectable;
use Monta\CheckoutApiWrapper\Traits\CachedOptions;

/**
 * Class represents either delivery and pickup option, once selected.
 * Originally meant for a ShipperOption but not really functional that way.
 */
class Option extends Objectable
{
    use CachedOptions;

    protected const string DELIVERY_TYPE = 'delivery';

    protected const string PICKUP_TYPE = 'pickup';

    /**
     * @param string $code
     * @param string $description - display name
     * @param float|null $price
     * @param string|null $priceFormatted
     * @param array $shipperOptions - e.g. "NoNeighbor" etc.
     */
    public function __construct(
        public string $code,
        public string $description = "",
        public ?float $price = null,
        public ?string $priceFormatted = null,
    )
    {
    }

    /**
     * @return string
     */
    public function getPriceFormatted(): string
    {
        return $this->priceFormatted;
    }

    /**
     * @param $priceFormatted
     */
    public function setPriceFormatted($priceFormatted): void
    {
        $this->priceFormatted = $priceFormatted;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param $price
     * @return $this
     */
    public function setPrice($price): self
    {
        $this->price = $price;
        return $this;
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
            // Check if total price is equal to cached price
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
     * @return string - JSON string with all it's data ready for DB writing or API output
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
            'short_code' => $this->getAdditionalData('shipper'),
        ];
        switch ($type) {
            /** Delivery specific fields */
            case self::DELIVERY_TYPE:
                // Options is just an array of codes, total_price includes their price
                $details['options'] = $this->getDeliveryOptions(onlyColumn: 'code');
                $additionalInfo['name'] = $this->getAdditionalData('displayName');
                $additionalInfo['date'] = date("Y-m-d H:i:s"); // TODO get desired delivery datetime
                $additionalInfo['time'] = date("H:i - H:i"); // TODO desired delivery time slot (from and to fields)
                break;
            /** Pickup specific output */
            case self::PICKUP_TYPE:
                // Construct object back from array
                // This is possible because $additionalData started as a PickupPoint, encoded to JSON for frontend.
                // Then returned from frontend to Quote, where it was saved as JSON string.
                // Then decoded back to array in Monta\CheckoutApiWrapper\Objects\Objectable::constructFromJson()
                // Which could return anything but at this point we know it was a Pickup option.
                $pickup = PickupPoint::construct($this->getAdditionalData());
                $details['short_code'] = $pickup->getShipperCode();
                // Pickup point has address in additional data
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
        $type = self::DELIVERY_TYPE;

        // Pickup point has no delivery type but has a postal code
        if (!$this->getAdditionalData('deliveryType') && $this->getAdditionalData('postalCode')) {
            $type = self::PICKUP_TYPE;
        }
        return $type;
    }
}