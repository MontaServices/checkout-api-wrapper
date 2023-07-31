<?php

namespace Monta\MontaProcessing\Objects;

use Monta\MontaProcessing\Objects\ShippingOption as MontaCheckout_ShippingOption;

/**
 * Class TimeFrame
 *
 */
class TimeFrame
{
    public function __construct($date, $day, $month, $dateFormatted, $ShippingOptions)
    {
        $this->setDate($date);
        $this->setDay($day);
        $this->setMonth($month);
        $this->setDateFormatted($dateFormatted);
        $this->setOptions($ShippingOptions);
    }

    public $date;

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    public $day;

    /**
     * @return mixed
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param mixed $day
     */
    public function setDay($day): void
    {
        $this->day = $day;
    }
    public $month;

    public $dateFormatted;

    /**
     * @return mixed
     */
    public function getDateFormatted()
    {
        return $this->dateFormatted;
    }

    /**
     * @param mixed $dateFormatted
     */
    public function setDateFormatted($dateFormatted): void
    {
        $this->dateFormatted = $dateFormatted;
    }

    /**
     * @return mixed
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @param mixed $month
     */
    public function setMonth($month): void
    {
        $this->month = $month;
    }

    public function setOptions($options)
    {
        $list = null;

        if (is_array($options)) {

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
        }

        $this->options = $list;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {

        $option = null;
        foreach ($this as $key => $value) {
            $option[$key] = $value;
        }

        return $option;
    }
}
