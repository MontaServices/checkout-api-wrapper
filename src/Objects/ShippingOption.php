<?php

namespace Monta\CheckoutApiWrapper\Objects;

// alias for sibling must remain or not all autoloading will work
use Monta\CheckoutApiWrapper\Objects\Option as Option;

/**
 * This is a Delivery Option, typically under a Timeframe.
 */
class ShippingOption extends Option
{
    public const string SHIPPING_OPTIONS_KEY = 'DeliveryOptions';

    public const string SHIPPING_STANDARD_KEY = 'StandardShipper';

    /** Constructor with promoted properties
     * Beware, these properties must match the exact output of the API
     *
     * @param string $shipper
     * @param string $code
     * @param string $displayNameShort
     * @param string $displayName
     * @param string|null $date - Desired delivery date
     * @param string|null $from - desired delivery time start
     * @param string|null $to - desired delivery time end
     * @param string $deliveryType
     * @param string $shippingType
     * @param float $price
     * @param string $priceFormatted
     * @param int $discountPercentage
     * @param bool $isPreferred
     * @param bool $isSustainable
     * TODO rename $deliveryOptions to shipperOptions, but the API result uses `deliveryOptions`
     * @param ShipperOption[] $deliveryOptions - converted into objects in setter
     * @param string $optionCodes @deprecated, not referenced anywhere
     * @param string[] $shipperCodes
     * @param string $shipperGroupName
     * @param string $imageUrl - Constructed based on other properties
     * @param array $selectedShipperOptions
     */
    public function __construct(
        public string $shipper,
        string $code,
        public string $displayNameShort,
        string $displayName,
        public ?string $date = null,
        public ?string $from = null,
        public ?string $to = null,
        public string $deliveryType = "",
        public string $shippingType = "",// TODO what is this? now a function on Option
        float $price = 0,
        string $priceFormatted = "",
        public int $discountPercentage = 0,
        public bool $isPreferred = false,
        public bool $isSustainable = false,
        public array $deliveryOptions = [],
        public string $optionCodes = "",
        public array $shipperCodes = [],
        string $shipperGroupName = "",
        string $imageUrl = "",
        public array $selectedShipperOptions = [],
    )
    {
        parent::__construct(
            code: $code,
            displayName: $displayName,
            price: $price,
            priceFormatted: $priceFormatted,
            imageUrl: $imageUrl,
            shipperGroupName: $shipperGroupName,
        );

        // Properties are set in constructor, this setter has custom functionality
        $this->setShipperOptions($deliveryOptions);

        // When ImageUrl was not passed, construct it
        if ($shipperCodes && !$imageUrl) {
            // ShipperCodes is usually an array of one code, pick the first one
            // TODO use $this->shipperGroupName as soon as that's added to REST API output
            $this->imageUrl = $this->getImageUrl(reset($this->shipperCodes));
        }
    }

    /**
     * @return ?string
     */
    public function getDesiredDeliveryDate(): ?string
    {
        return $this->date;
    }

    /**
     * @return string|null
     */
    public function getFrom(): ?string
    {
        return $this->from;
    }

    /**
     * @return string|null
     */
    public function getTo(): ?string
    {
        return $this->to;
    }

    /** Get a ShipperOption from this ShippingOption by code
     * Typically used to retrieve from cached Option
     *
     * @param string $code
     * @return ShipperOption|null
     */
    public function getShipperOptionByCode(string $code): ?ShipperOption
    {
        // Filter array on callback, match on code
        $filtered = array_filter(
            array: $this->getShipperOptions(),
            callback: fn($option) => $option->getCode() == $code,
        );

        // Return the first (only) element, or null if none found
        return reset($filtered) ?? null;
    }

    /** The entire list of possible shipper options for this Option
     *
     * @return ShipperOption[]
     */
    public function getShipperOptions(): array
    {
        return $this->deliveryOptions;
    }

    /** The selected Shipper Options
     *
     * @param string|null $onlyColumn
     * @return array - assoc arrays of selected options (or flat array with one column)
     */
    public function getSelectedShipperOptions(?string $onlyColumn = null): array
    {
        // frontend passes the selected ShipperOptions in this property
        $shipperOptions = $this->selectedShipperOptions;
        return $onlyColumn ?
            // when passed, return only one column
            array_column($shipperOptions, $onlyColumn)
            // otherwise return whole array
            : $shipperOptions;
    }

    /** When this Option is selected, update its Shipper options
     *
     * @param array $shipperOptions
     * @return void
     */
    public function setSelectedShipperOptions(array $shipperOptions): void
    {
        $this->selectedShipperOptions = $shipperOptions;
    }

    /** Convert stdClass from API to array of Option objects
     *
     * @param array $shipperOptions
     * @return ShippingOption
     */
    public function setShipperOptions(array $shipperOptions): ShippingOption
    {
        // index array on 'code' column to remove any duplicates
        $shipperOptions = array_column($shipperOptions, null, 'code');
        $list = [];
        foreach ($shipperOptions as $option) {
            // Convert into Option
            $list[] = ShipperOption::construct((array)$option);
        }

        $this->deliveryOptions = $list;

        return $this;
    }

    /**
     * @param bool $includeShipperOptions - Include price of selected options
     * @return float
     */
    public function getPrice(bool $includeShipperOptions = false): float
    {
        // base shipping price
        return $this->price
            // if requested, add sum of all selected shipperOptions
            + ($includeShipperOptions ? array_sum($this->getSelectedShipperOptions('price')) : 0);
    }
}