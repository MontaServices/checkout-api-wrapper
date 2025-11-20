<?php

namespace Monta\CheckoutApiWrapper\Objects;

// alias for sibling must remain or not all autoloading will work
use Monta\CheckoutApiWrapper\Objects\Objectable as Objectable;
use Monta\CheckoutApiWrapper\Objects\Option as Option;

/**
 * This is a Delivery Option, typically under a Timeframe.
 */
class ShippingOption extends Objectable
{

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
     * @param ShippingOption[] $deliveryOptions - converted into objects in setter
     * @param string $optionCodes @deprecated, not referenced anywhere
     * @param string[] $shipperCodes
     * @param string $shipperGroupName
     * @param string $imageUrl - Constructed based on other properties
     */
    public function __construct(
        public string $shipper,
        public string $code,
        public string $displayNameShort,
        public string $displayName,
        public ?string $from = null,
        public ?string $to = null,
        public string $deliveryType = "",
        public string $shippingType = "",
        public float $price = 0,
        public string $priceFormatted = "",
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
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return ShippingOption
     */
    public function setCode(string $code): ShippingOption
    {
        $this->code = $code;
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
     * @return string
     */
    public function getShippingType(): string
    {
        return $this->shippingType;
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
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return ShippingOption
     */
    public function setPrice(float $price): ShippingOption
    {
        $this->price = $price;
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

    /**
     * @return string
     */
    public function getPriceFormatted(): string
    {
        return $this->priceFormatted;
    }

    /**
     * @param string $priceFormatted
     */
    public function setPriceFormatted(string $priceFormatted): void
    {
        $this->priceFormatted = $priceFormatted;
    }

    /**
     * @return array
     */
    public function getDeliveryOptions(): array
    {
        return $this->deliveryOptions;
    }

    /** Convert stdClass from API to array of Option objects
     *
     * @param array $deliveryOptions
     * @return ShippingOption
     */
    public function setDeliveryOptions(array $deliveryOptions): ShippingOption
    {
        $list = [];
        foreach ($deliveryOptions as $option) {
            // Convert stdClass into class
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
}