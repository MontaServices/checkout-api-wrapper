<?php

namespace Monta\CheckoutApiWrapper\Objects;

use Monta\CheckoutApiWrapper\Objects\ShippingOption as MontaCheckout_ShippingOption;

class TimeFrame
{
    /**
     * @var string|null
     */
    public ?string $date;

    /**
     * @var string|null
     */
    public ?string $day;

    /**
     * @var string|null
     */
    public ?string $month;

    /**
     * @var string
     */
    public string $dateFormatted;

    /**
     * @var array
     */
    public array $options;

    public function __construct(?string $date, ?string $day, ?string $month, string $dateFormatted, array $ShippingOptions)
    {
        $this->setDate($date);
        $this->setDay($day);
        $this->setMonth($month);
        $this->setDateFormatted($dateFormatted);
        $this->setOptions($ShippingOptions);
    }

    /**
     * @return string
     */
    public function getDate(): string
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
     * @return string
     */
    public function getDateFormatted(): string
    {
        return $this->dateFormatted;
    }

    /**
     * @param string $dateFormatted
     */
    public function setDateFormatted(string $dateFormatted): void
    {
        $this->dateFormatted = $dateFormatted;
    }

    /**
     * @return string
     */
    public function getMonth(): string
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

    public function setOptions(array $options): TimeFrame
    {
        $list = null;

        foreach ($options as $onr => $option) {
            $list[$onr] = new MontaCheckout_ShippingOption(
                $option->shipper,
                $option->code,
                $option->displayNameShort,
                $option->displayName,
                $option->deliveryType,
                $option->shippingType,
                $option->price,
                $option->priceFormatted,
                $option->discountPercentage,
                $option->isPreferred,
                $option->isSustainable,
                $option->deliveryOptions,
                $option->optionCodes
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
