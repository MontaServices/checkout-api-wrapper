<?php

namespace Monta\CheckoutApiWrapper\Objects;

// alias for sibling must remain or not all autoloading will work
use Monta\CheckoutApiWrapper\Objects\Objectable as Objectable;
use Monta\CheckoutApiWrapper\Objects\ShippingOption as ShippingOption;

class TimeFrame extends Objectable
{

    /** Constructor with promoted properties
     *
     * @param string|null $date
     * @param string|null $day
     * @param string|null $month
     * @param string|null $dateFormatted
     * @param string|null $dateOnlyFormatted
     * @param ShippingOption[]|null $options - converted into object in setter
     */
    public function __construct(
        public ?string $date = null,
        public ?string $day = null,
        public ?string $month = null,
        public ?string $dateFormatted = null,
        public ?string $dateOnlyFormatted = null,
        public ?array $options = [],
    )
    {
        // Properties are set in constructor, this setter has custom functionality
        if ($options) {
            $this->setOptions($options);
        }
    }

    /**
     * @return string|null
     */
    public function getDate(): ?string
    {
        return $this->date;
    }

    /**
     * @param string|null $date
     */
    public function setDate(?string $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string|null
     */
    public function getDay(): ?string
    {
        return $this->day;
    }

    /**
     * @param string|null $day
     */
    public function setDay(?string $day): void
    {
        $this->day = $day;
    }

    /**
     * @return string|null
     */
    public function getDateFormatted(): ?string
    {
        return $this->dateFormatted;
    }

    /**
     * @param string|null $dateFormatted
     */
    public function setDateFormatted(?string $dateFormatted): void
    {
        $this->dateFormatted = $dateFormatted;
    }

    /**
     * @return string|null
     */
    public function getDateOnlyFormatted(): ?string
    {
        return $this->dateOnlyFormatted;
    }

    /**
     * @param string|null $dateOnlyFormatted
     */
    public function setDateOnlyFormatted(?string $dateOnlyFormatted): void
    {
        $this->dateOnlyFormatted = $dateOnlyFormatted;
    }

    /**
     * @return string|null
     */
    public function getMonth(): ?string
    {
        return $this->month;
    }

    /**
     * @param string|null $month
     */
    public function setMonth(?string $month): void
    {
        $this->month = $month;
    }

    /** Set ShippingOptions to Timeframe
     *
     * @param \stdClass[]|array[] $options - Array of stdClasses (from API) or array of arrays (from JSON)
     * @return $this
     */
    public function setOptions(array $options): TimeFrame
    {
        $list = null;

        foreach ($options as $onr => $option) {
            // Cast to array
            $option = (array)$option;
            // Copy date from TimeFrame to ShippingOption (required later as desired delivery date)
            $option['date'] = $this->getDate();

            // Convert each stdClass into ShippingOption object
            $list[$onr] = ShippingOption::construct($option);
        }
        // Overwrite property which was set as promoted property by constructor
        $this->options = $list;
        return $this;
    }

}