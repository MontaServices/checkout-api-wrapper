<?php

namespace Monta\MontaProcessing\Objects;

use Monta\MontaProcessing\Objects\Option as MontaCheckout_Option;

/**
 * Class ShippingOption
 *
 * @package Montapacking\MontaCheckout\Objects
 */
class ShippingOption
{
    public string $shipper;
    public int $discountPercentage;
    public bool $isPreferred;
    public bool $isSustainable;

    public string $displayName;
    public string $deliveryType;
    public string $shippingType;
    public float $price;
    public string $priceFormatted;
    public array $deliveryOptions;
    public string $optionCodes;

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
     * @param $shipper
     * @param $code
     * @param $displayNameShort
     * @param $displayName
     * @param $deliveryType
     * @param $shippingType
     * @param $price
     * @param $priceFormatted
     * @param $discountPercentage
     * @param $isPreferred
     * @param $isSustainable
     * @param $deliveryOptions
     * @param $optionCodes
     */
    public function __construct($shipper, $code, $displayNameShort, $displayName, $deliveryType, $shippingType, $price, $priceFormatted, $discountPercentage, $isPreferred, $isSustainable, $deliveryOptions, $optionCodes)
    {
        $this->setShipper($shipper);
        $this->setCode($code);
        $this->setDisplayNameShort($displayNameShort);
        $this->setDisplayName($displayName);
        $this->setDeliveryType($deliveryType);
        $this->setShippingType($shippingType);
        $this->setPrice($price);
        $this->setPriceFormatted($priceFormatted);
        $this->setDiscountPercentage($discountPercentage);
        $this->setIsPreferred($isPreferred);
        $this->setIsSustainable($isSustainable);
        $this->setDeliveryOptions($deliveryOptions);
        $this->setOptionCodes($optionCodes);
    }

    /**
     * @return mixed
     */
    public function getShipper()
    {
        return $this->shipper;
    }

    /**
     * @param mixed $shipper
     * @return ShippingOption
     */
    public function setShipper($shipper)
    {
        $this->shipper = $shipper;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     * @return ShippingOption
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param mixed $displayName
     * @return ShippingOption
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeliveryType()
    {
        return $this->deliveryType;
    }

    /**
     * @param mixed $deliveryType
     * @return ShippingOption
     */
    public function setDeliveryType($deliveryType)
    {
        $this->deliveryType = $deliveryType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShippingType()
    {
        return $this->shippingType;
    }

    /**
     * @param mixed $shippingType
     * @return ShippingOption
     */
    public function setShippingType($shippingType)
    {
        $this->shippingType = $shippingType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     * @return ShippingOption
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDiscountPercentage()
    {
        return $this->discountPercentage;
    }

    /**
     * @param mixed $discountPercentage
     * @return ShippingOption
     */
    public function setDiscountPercentage($discountPercentage)
    {
        $this->discountPercentage = $discountPercentage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsPreferred()
    {
        return $this->isPreferred;
    }

    /**
     * @param mixed $isPreferred
     * @return ShippingOption
     */
    public function setIsPreferred($isPreferred)
    {
        $this->isPreferred = $isPreferred;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsSustainable()
    {
        return $this->isSustainable;
    }

    /**
     * @param mixed $isSustainable
     * @return ShippingOption
     */
    public function setIsSustainable($isSustainable)
    {
        $this->isSustainable = $isSustainable;
        return $this;
    }
    public $code;
    public $displayNameShort;

    /**
     * @return mixed
     */
    public function getDisplayNameShort()
    {
        return $this->displayNameShort;
    }

    /**
     * @param mixed $displayNameShort
     */
    public function setDisplayNameShort($displayNameShort): void
    {
        $this->displayNameShort = $displayNameShort;
    }


    /**
     * @return mixed
     */
    public function getPriceFormatted()
    {
        return $this->priceFormatted;
    }

    /**
     * @param mixed $priceFormatted
     */
    public function setPriceFormatted($priceFormatted): void
    {
        $this->priceFormatted = $priceFormatted;
    }

    /**
     * @return mixed
     */
    public function getDeliveryOptions()
    {
        return $this->deliveryOptions;
    }

    /**
     * @param mixed $deliveryOptions
     */
    public function setDeliveryOptions($deliveryOptions): ShippingOption
    {
        $list = [];

        if (is_array($deliveryOptions)) {

            foreach ($deliveryOptions as $onr => $option) {

                $list[$onr] = new MontaCheckout_Option($option->code, $option->description, $option->price, $option->priceFormatted);
            }
        }

        $this->deliveryOptions = $list;

        return $this;
    }


















































//
//    /**
//     * @var
//     */
//    public $code;
//    /**
//     * @var
//     */
//    public $codes;
//    /**
//     * @var
//     */
//    public $optioncodes;
//    /**
//     * @var
//     */
//    public $optionsWithValue;
//    /**
//     * @var
//     */
//    public $description;
//    /**
//     * @var
//     */
//    public $mailbox;
//    /**
//     * @var
//     */
//    public $price;
//    /**
//    /**
//     * @var
//     */
//    public $price_raw;
//    /**
//     * @var
//     */
//    public $currency;
//    /**
//     * @var
//     */
//    public $from;
//    /**
//     * @var
//     */
//    public $to;
//    /**
//     * @var
//     */
//    public $extras;
//    /**
//     * @var
//     */
//    public $date;
//     /**
//     * @var
//     */
//    public $isPreferred;
//     /**
//     * @var
//     */
//    public $isSustainable;
//    /**
//     * @var
//     */
//    public $displayName;
//    /**
//     * @var
//     */
//    public $discountPercentage;
//
//    /**
//     * ShippingOption constructor.
//     *
//     * @param $code
//     * @param $codes
//     * @param $optioncodes
//     * @param $optionsWithValue
//     * @param $description
//     * @param $mailbox
//     * @param $price
//     * @param $price_raw
//     * @param $currency
//     * @param $from
//     * @param $to
//     * @param $extras
//     * @param $date
//     * @param $isPreferred
//     */
//    public function __construct($code, $codes, $optioncodes, $optionsWithValue, $description, $mailbox, $price, $price_raw, $currency, $from, $to, $extras, $date, $isPreferred, $displayName, $isSustainable, $discountPercentage) //phpcs:ignore
//    {
//        $this->setCode($code);
//        $this->setCodes($codes);
//        $this->setOptionCodes($optioncodes);
//        $this->setOptionsWithValue($optionsWithValue);
//        $this->setDescription($description);
//        $this->setMailbox($mailbox);
//        $this->setPrice($price);
//        $this->setPriceRaw($price_raw);
//        $this->setCurrency($currency);
//        $this->setFrom($from);
//        $this->setTo($to);
//        $this->setExtras($extras);
//        $this->setDate($date);
//        $this->setIsPreferred($isPreferred);
//        $this->setIsSustainable($isSustainable);
//        $this->setDisplayName($displayName);
//        $this->setDiscountPercentage($discountPercentage);
//    }
//
//     /**
//     * @param $code
//     *
//     * @return $this
//     */
//    public function setDiscountPercentage($discountPercentage)
//    {
//        $this->discountPercentage = $discountPercentage;
//        return $this;
//    }
//
//    /**
//     * @param $code
//     *
//     * @return $this
//     */
//    public function setCode($code)
//    {
//        $this->code = $code;
//        return $this;
//    }
//
//    /**
//     * @param $codes
//     *
//     * @return $this
//     */
//    public function setCodes($codes)
//    {
//        $this->codes = $codes;
//        return $this;
//    }
//
//    /**
//     * @param $optioncodes
//     *
//     * @return $this
//     */
//    public function setOptionCodes($optioncodes)
//    {
//        $this->optioncodes = $optioncodes;
//        return $this;
//    }
//
//    /**
//     * @param $optionsWithValue
//     *
//     * @return $this
//     */
//    public function setOptionsWithValue($optionsWithValue)
//    {
//        $this->optionsWithValue = $optionsWithValue;
//        return $this;
//    }
//
//    /**
//     * @param $description
//     *
//     * @return $this
//     */
//    public function setDescription($description)
//    {
//        $this->description = $description;
//        return $this;
//    }
//
//    /**
//     * @param $mailbox
//     *
//     * @return $this
//     */
//    public function setMailbox($mailbox)
//    {
//        $this->mailbox = $mailbox;
//        return $this;
//    }
//
//    /**
//     * @param $price
//     *
//     * @return $this
//     */
//    public function setPrice($price)
//    {
//        $this->price = $price;
//        return $this;
//    }
//
//    /**
//     * @param $price_raw
//     *
//     * @return $this
//     */
//    public function setPriceRaw($price_raw)
//    {
//        $this->price_raw = $price_raw;
//        return $this;
//    }
//
//    /**
//     * @param $currency
//     *
//     * @return $this
//     */
//    public function setCurrency($currency)
//    {
//        $this->currency = $currency;
//        return $this;
//    }
//
//    /**
//     * @param $from
//     *
//     * @return $this
//     */
//    public function setFrom($from)
//    {
//        $this->from = $from;
//        return $this;
//    }
//
//    /**
//     * @param $to
//     *
//     * @return $this
//     */
//    public function setTo($to)
//    {
//        $this->to = $to;
//        return $this;
//    }
//
//     /**
//     * @param $isPreferred
//     *
//     * @return $this
//     */
//    public function setIsPreferred($isPreferred)
//    {
//        $this->isPreferred = $isPreferred;
//        return $this;
//    }
//
//     /**
//     * @param $isSustainable
//     *
//     * @return $this
//     */
//    public function setIsSustainable($isSustainable)
//    {
//        $this->isSustainable = $isSustainable;
//        return $this;
//    }
//
//      /**
//     * @param $displayName
//     *
//     * @return $this
//     */
//    public function setDisplayName($displayName)
//    {
//        $this->displayName = $displayName;
//        return $this;
//    }
//
//    /**
//     * @param $extras
//     *
//     * @return $this
//     */
//    public function setExtras($extras)
//    {
//
//        $list = null;
//
//        if (is_array($extras)) {
//
//            foreach ($extras as $extra) {
//
//                $list[] = new MontaCheckout_Option(
//                    $extra->Code,
//                    $extra->Description,
//                    $extra->SellPrice,
//                    $extra->SellPriceCurrency
//                );
//
//            }
//
//        }
//
//        $this->extras = $list;
//        return $this;
//    }
//
//    /**
//     * @param $date
//     */
//    public function setDate($date)
//    {
//        if ($date != null) {
//            $this->date = date('Y-m-d H:i:s', strtotime($date));
//        }
//    }

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
