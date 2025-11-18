<?php

namespace Monta\CheckoutApiWrapper\Objects;

// alias for sibling must remain or not all autoloading will work
use Monta\CheckoutApiWrapper\Objects\Objectable as Objectable;

/**
 * I have hijacked this class to represent either delivery and pickup options.
 * Originally meant for ShipperOptions
 */
class Option extends Objectable
{

    protected const string DELIVERY_TYPE = 'delivery';

    protected const string PICKUP_TYPE = 'pickup';

    /**
     * @param string $code
     * @param string $description
     * @param float|null $price
     * @param string|null $priceFormatted
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
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
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
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /** Convert selected Option to JSON in proper structure.
     * Output format determined by old Montapacking module output for backwards compatibility
     *
     * @return string - JSON string with all it's data ready for DB writing or API output
     */
    public function toJson(): string
    {
        $type = $this->getShippingType();
        $additionalInfo = [
            'code' => $this->getCode(),
            'price' => $this->getPrice(), // TODO shipper price only
            'total_price' => $this->getPrice(), // TODO shipper price + shipper options
        ];
        $details = [
            'short_code' => $this->getAdditionalData('shipper'),
            'options' => [], // TODO shipper options
        ];
        switch ($type) {
            /** Delivery specific fields */
            case self::DELIVERY_TYPE:
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