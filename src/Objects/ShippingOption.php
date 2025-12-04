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
     * @param string|null $from
     * @param string|null $to
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
     */
    public function __construct(
        public string $shipper,
        string $code,
        public string $displayNameShort,
        string $displayName,
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

        if ($shipperCodes) {
            // ShipperCodes is usually an array of one code, pick the first one
            // TODO use $this->shipperGroupName as soon as that's added to REST API output
            $this->imageUrl = $this->getImageUrl(reset($this->shipperCodes));
        }
    }

    /**
     * @return string
     * @deprecated - Not referenced anywhere
     */
    public function getOptionCodes(): string
    {
        return $this->optionCodes;
    }

    /**
     * @param string $optionCodes
     * @deprecated - Not referenced anywhere
     */
    public function setOptionCodes(string $optionCodes): void
    {
        $this->optionCodes = $optionCodes;
    }

    /**
     * @return string|null
     */
    public function getFrom(): ?string
    {
        return $this->from;
    }

    /**
     * @param string|null $from
     * @return $this
     */
    public function setFrom(?string $from): ShippingOption
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTo(): ?string
    {
        return $this->to;
    }

    /**
     * @param string|null $to
     * @return ShippingOption
     */
    public function setTo(?string $to): ShippingOption
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShipper(): string
    {
        return $this->shipper;
    }

    /**
     * @param string $shipper
     * @return ShippingOption
     */
    public function setShipper(string $shipper): ShippingOption
    {
        $this->shipper = $shipper;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryType(): string
    {
        return $this->deliveryType;
    }

    /**
     * @param string $deliveryType
     * @return ShippingOption
     */
    public function setDeliveryType(string $deliveryType): ShippingOption
    {
        $this->deliveryType = $deliveryType;
        return $this;
    }

    /**
     * @param string $shippingType
     * @return ShippingOption
     */
    public function setShippingType(string $shippingType): ShippingOption
    {
        $this->shippingType = $shippingType;
        return $this;
    }

    /**
     * @return int
     */
    public function getDiscountPercentage(): int
    {
        return $this->discountPercentage;
    }

    /**
     * @param int $discountPercentage
     * @return ShippingOption
     */
    public function setDiscountPercentage(int $discountPercentage): ShippingOption
    {
        $this->discountPercentage = $discountPercentage;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsPreferred(): bool
    {
        return $this->isPreferred;
    }

    /**
     * @param bool $isPreferred
     * @return ShippingOption
     */
    public function setIsPreferred(bool $isPreferred): ShippingOption
    {
        $this->isPreferred = $isPreferred;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsSustainable(): bool
    {
        return $this->isSustainable;
    }

    /**
     * @param bool $isSustainable
     * @return ShippingOption
     */
    public function setIsSustainable(bool $isSustainable): ShippingOption
    {
        $this->isSustainable = $isSustainable;
        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayNameShort(): string
    {
        return $this->displayNameShort;
    }

    /**
     * @param string $displayNameShort
     */
    public function setDisplayNameShort(string $displayNameShort): void
    {
        $this->displayNameShort = $displayNameShort;
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
        $filtered = array_filter(array: $this->getShipperOptions(), callback: fn($option) => $option->getCode() == $code);

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
    public function getSelectedShipperOptions(string $onlyColumn = null): array
    {
        // frontend passes the selected ShipperOptions in this property
        $shipperOptions = $this->getOriginalData('selectedShipperOptions') ?? [];
        return $onlyColumn ?
            // when passed, return only one column
            array_column($shipperOptions, $onlyColumn)
            // otherwise return whole array
            : $shipperOptions;
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
     * @return array
     */
    public function getShipperCodes(): array
    {
        return $this->shipperCodes;
    }

    /**
     * @param array $shipperCodes
     */
    public function setShipperCodes(array $shipperCodes): void
    {
        $this->shipperCodes = $shipperCodes;
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