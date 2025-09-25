<?php

namespace Monta\CheckoutApiWrapper\Objects;

// alias for sibling must remain or not all autoloading will work
use Monta\CheckoutApiWrapper\Objects\ShippingOption as ShippingOption;

class TimeFrame
{
    /** Constructor with promoted properties
     *
     * @param ?string $date
     * @param ?string $day
     * @param ?string $month
     * @param ?string $dateFormatted
     * @param ?string $dateOnlyFormatted
     * @param array $options
     */
    public function __construct(
        public ?string $date = null,
        public ?string $day = null,
        public ?string $month = null,
        public ?string $dateFormatted = null,
        public ?string $dateOnlyFormatted = null,
        public array $options = [],
    )
    {
        // Properties are set in constructor, this setter has custom functionality
        $this->setOptions($options);
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @param ?string $date
     */
    public function setDate(?string $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getDay(): string
    {
        return $this->day;
    }

    /**
     * @param ?string $day
     */
    public function setDay(?string $day): void
    {
        $this->day = $day;
    }

    /**
     * @return string
     */
    public function getDateFormatted(): string
    {
        return $this->dateFormatted;
    }

    /**
     * @param ?string $dateFormatted
     */
    public function setDateFormatted(?string $dateFormatted): void
    {
        $this->dateFormatted = $dateFormatted;
    }

    /**
     * @return ?string
     */
    public function getDateOnlyFormatted(): ?string
    {
        return $this->dateOnlyFormatted;
    }

    /**
     * @param ?string $dateOnlyFormatted
     */
    public function setDateOnlyFormatted(?string $dateOnlyFormatted): void
    {
        $this->dateOnlyFormatted = $dateOnlyFormatted;
    }

    /**
     * @return string
     */
    public function getMonth(): string
    {
        return $this->month;
    }

    /**
     * @param ?string $month
     */
    public function setMonth(?string $month): void
    {
        $this->month = $month;
    }

    public function setOptions(array $options): TimeFrame
    {
        $list = null;

        foreach ($options as $onr => $option) {
            $list[$onr] = new ShippingOption(
                $option->shipper,
                $option->code,
                $option->displayNameShort,
                $option->displayName,
                $option->from,
                $option->to,
                $option->deliveryType,
                $option->shippingType,
                $option->price,
                $option->priceFormatted,
                $option->discountPercentage,
                $option->isPreferred,
                $option->isSustainable,
                $option->deliveryOptions,
                $option->optionCodes,
                $option->shipperCodes
            );
        }

        $this->options = $list;
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
}
