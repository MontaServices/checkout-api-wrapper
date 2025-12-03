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
     * TODO rename to shipperOptions
     * @param ShippingOption[] $deliveryOptions - converted into objects in setter
     * @param string $optionCodes @deprecated, not referenced anywhere
     * @param string[] $shipperCodes
     * @param string $shipperGroupName
     * @param string $imageUrl - Constructed based on other properties
     */
    public function __construct(
        public string $shipper,
        string $code,
        public string $displayNameShort,
        public string $displayName,
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
        protected string $shipperGroupName = "",
        public string $imageUrl = "",
    )
    {
        parent::__construct($code, $displayName, $price, $priceFormatted);

        // Properties are set in constructor, this setter has custom functionality
        $this->setDeliveryOptions($deliveryOptions);

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
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     * @return ShippingOption
     */
    public function setDisplayName(string $displayName): ShippingOption
    {
        $this->displayName = $displayName;
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

    /** Get a shipperoption from this ShippingOption by code
     *
     * @param string $code - Shipper option code
     * @return Option|null
     */
    public function getShipperOptionByCode(string $code): ?Option
    {
        // Filter array on callback, match on code
        $filtered = array_filter(array: $this->getDeliveryOptions(), callback: fn($option) => $option->getCode() == $code);

        // Return the first (only) element, or null if none found
        return reset($filtered) ?? null;
    }

    /** TODO rename to getShipperOptions
     * @param string|null $onlyColumn
     * @return ShippingOption[]
     */
    public function getDeliveryOptions(string $onlyColumn = null): array
    {
        return $onlyColumn ?
            // when passed, return only one column
            array_column($this->deliveryOptions, $onlyColumn)
            // otherwise return whole array
            : $this->deliveryOptions;
    }

    /** Convert stdClass from API to array of Option objects
     * TODO rename to setShipperOptions
     * @param array $shipperOptions
     * @return ShippingOption
     */
    public function setDeliveryOptions(array $shipperOptions): ShippingOption
    {
        // index array on 'code' column to remove any duplicates
        $shipperOptions = array_column($shipperOptions, null, 'code');
        foreach ($shipperOptions as $option) {
            // Convert into Option
            $list[] = Option::construct((array)$option);
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
     * @param bool $includeShipperOptions - Include price of all options
     * @return float
     */
    public function getPrice(bool $includeShipperOptions = false): float
    {
        // base shipping price
        return $this->price
            // if requested, add sum of all options
            + ($includeShipperOptions ? array_sum($this->getDeliveryOptions('price')) : 0);
    }
}