<?php

namespace Monta\CheckoutApiWrapper\Objects;

use DateTimeImmutable;
use IntlDateFormatter;

// alias for sibling must remain or not all autoloading will work
use Monta\CheckoutApiWrapper\Objects\Objectable as Objectable;
use Monta\CheckoutApiWrapper\Objects\ShippingOption as ShippingOption;

class TimeFrame extends Objectable
{
    public const string FALLBACK_DATEONLY_CODE = 'Unknown';

    /** Constructor with promoted properties
     *
     * @param string|null $date - System date (1970-01-01)
     * @param string|null $day - "dinsdag"
     * @param string|null $month - "januari"
     * @param string|null $dateFormatted - Full date and day formatted locally: "dinsdag 1 januari 1970"
     * @param string|null $dateOnlyFormatted - Short date formatted locally: "01-01-1970"
     * @param ShippingOption[]|null $options - Converted in setter but argument is received as stdClass[]
     */
    public function __construct(
        public ?string $date = null,
        public ?string $day = null,
        public ?string $month = null,
        public ?string $dateFormatted = null,
        public ?string $dateOnlyFormatted = null,
        public ?array $options = [],
        public ?string $locale = null,
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
     * @return string
     */
    public function getDay(): string
    {
        return $this->formatDatePart('EEEE')
            ?? $this->day
            ?? "";
    }

    /**
     * @param string|null $day
     */
    public function setDay(?string $day): void
    {
        $this->day = $day;
    }

    /**
     * @param bool $strip - Strip formatted into clean short format: "1 januari"
     * @return string|null
     */
    public function getDateFormatted(bool $strip = false): ?string
    {
        if ($date = $this->getDateObject()) {
            $day = (int) $date->format('j');
            $month = $this->formatDatePart('MMMM');

            if ($strip) {
                return $month ? sprintf('%d %s', $day, $month) : null;
            }

            $weekday = $this->getDay();
            return $weekday && $month
                ? sprintf('%s %d %s %d', $weekday, $day, $month, (int) $date->format('Y'))
                : null;
        }

        if ($strip) {
            // remove weekday from formatted date (both are determined by API)
            return trim(str_replace(search: $this->getDay(), replace: "",
                // remove current year from formatted date, automatically works through New Year's Eve!
                subject: str_replace(search: date("Y"), replace: "", subject: $this->dateFormatted ?? "")));
        } else {
            // Oterwise just return whatever the API set
            return $this->dateFormatted;
        }
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
        return $this->formatDatePart('MMMM')
            ?? $this->month;
    }

    /**
     * @param string|null $month
     */
    public function setMonth(?string $month): void
    {
        $this->month = $month;
    }

    public function getLocale(): string
    {
        return str_replace('_', '-', $this->locale ?: 'nl-NL');
    }

    public function setLocale(?string $locale): static
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFallbackShipper(): bool
    {
        return $this->getDateOnlyFormatted() == self::FALLBACK_DATEONLY_CODE;
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

    protected function getDateObject(): ?DateTimeImmutable
    {
        if (!$this->date) {
            return null;
        }

        try {
            return new DateTimeImmutable($this->date);
        } catch (\Exception) {
            return null;
        }
    }

    protected function formatDatePart(string $pattern): ?string
    {
        if (!class_exists(IntlDateFormatter::class) || !$this->getDateObject()) {
            return null;
        }

        $formatter = new IntlDateFormatter(
            $this->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            date_default_timezone_get(),
            null,
            $pattern,
        );

        $formatted = $formatter->format($this->getDateObject());
        return is_string($formatted) ? trim($formatted) : null;
    }

}
