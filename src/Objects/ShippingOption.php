<?php

namespace Monta\CheckoutApiWrapper\Objects;

use Monta\CheckoutApiWrapper\Objects\Option as MontaCheckout_Option;

class ShippingOption
{
    /**
     * @var string
     */
    public string $shipper;

    /**
     * @var int
     */
    public int $discountPercentage;

    /**
     * @var bool
     */
    public bool $isPreferred;

    /**
     * @var bool
     */
    public bool $isSustainable;

    /**
     * @var string
     */
    public string $displayName;

	/**
	 * @var ?string
	 */
    public ?string $from;

	/**
	 * @var ?string
	 */
	public ?string $to;

    /**
     * @var string
     */
    public string $deliveryType;

    /**
     * @var string
     */
    public string $shippingType;

    /**
     * @var float
     */
    public float $price;

    /**
     * @var string
     */
    public string $priceFormatted;

    /**
     * @var array
     */
    public array $deliveryOptions;

    /**
     * @var string
     */
    public string $optionCodes;

    /**
     * @var string
     */
    public string $code;

    /**
     * @var string
     */
    public string $displayNameShort;

	public array $shipperCodes;

    /**
     * @return string
     */
    public function getOptionCodes(): string
    {
        return $this->optionCodes;
    }

    /**
     * @param string $optionCodes
     */
    public function setOptionCodes(string $optionCodes): void
    {
        $this->optionCodes = $optionCodes;
    }

	/**
	 * @param string $shipper
	 * @param string $code
	 * @param string $displayNameShort
	 * @param string $displayName
	 * @param ?string $from
	 * @param ?string $to
	 * @param string $deliveryType
	 * @param string $shippingType
	 * @param float $price
	 * @param string $priceFormatted
	 * @param int $discountPercentage
	 * @param bool $isPreferred
	 * @param bool $isSustainable
	 * @param array $deliveryOptions
	 * @param string $optionCodes
	 * @param array $shipperCodes
	 */
    public function __construct(string $shipper, string $code, string $displayNameShort, string $displayName, ?string $from, ?string $to, string $deliveryType, string $shippingType, float $price, string $priceFormatted, int $discountPercentage, bool $isPreferred, bool $isSustainable, array $deliveryOptions, string $optionCodes, array $shipperCodes)
    {
        $this->setShipper($shipper);
        $this->setCode($code);
        $this->setDisplayNameShort($displayNameShort);
        $this->setDisplayName($displayName);

		$this->setFrom($from);
		$this->setTo($to);

        $this->setDeliveryType($deliveryType);
        $this->setShippingType($shippingType);
        $this->setPrice($price);
        $this->setPriceFormatted($priceFormatted);
        $this->setDiscountPercentage($discountPercentage);
        $this->setIsPreferred($isPreferred);
        $this->setIsSustainable($isSustainable);
        $this->setDeliveryOptions($deliveryOptions);
        $this->setOptionCodes($optionCodes);
		$this->setShipperCodes($shipperCodes);
    }

	/**
	 * @return string|null
	 */
	public function getFrom(): ?string
	{
		return $this->from;
	}

	/**
	 * @param ?string $from
	 * @return ShippingOption
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
	 * @param ?string $to
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
    public function getShippingType(): String
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
     * @param void $displayNameShort
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

    /**
     * @param array $deliveryOptions
     * @return ShippingOption
     */
    public function setDeliveryOptions(array $deliveryOptions): ShippingOption
    {
        $list = [];
        foreach ($deliveryOptions as $option) {

            $list[] = new MontaCheckout_Option($option->code, $option->description, $option->price, $option->priceFormatted);
        }

        $this->deliveryOptions = $list;

        return $this;
    }

    /**
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

	/**
	 * @return array
	 */
	public function getShipperCodes(): array {
		return $this->shipperCodes;
	}

	/**
	 * @param array $shipperCodes
	 */
	public function setShipperCodes( array $shipperCodes ): void {
		$this->shipperCodes = $shipperCodes;
	}
}
