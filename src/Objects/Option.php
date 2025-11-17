<?php

namespace Monta\CheckoutApiWrapper\Objects;

class Option
{
    /**
     * @param string $code
     * @param string $description
     * @param float|null $price
     * @param string|null $priceFormatted
     * @param array $additionalData
     */
    public function __construct(
        public string $code,
        public string $description = "",
        public ?float $price = null,
        public ?string $priceFormatted = null,
        // The entire rest of the data
        protected array $additionalData = [],
    )
    {
    }

    /**
     * @param string $key
     * @return mixed
     */
    protected function getAdditionalData(string $key): mixed
    {
        return $this->additionalData[$key] ?? null;
    }

    /**
     * @return string
     */
    public function getPriceFormatted(): string
    {
        return $this->priceFormatted;
    }

    /**
     * @param $priceFormatted
     */
    public function setPriceFormatted($priceFormatted): void
    {
        $this->priceFormatted = $priceFormatted;
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
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
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

    /** Convert selected Option to JSON in proper structure.
     * Output format determined by old Montapacking module output for backwards compatibility
     *
     * @return string - JSON string with all it's data ready for DB writing or API output
     */
    public function toJson(): string
    {
        return json_encode([
            'type' => $this->getShippingType(),
            'details' => [
                'short_code' => $this->getAdditionalData('shipper'),
                'options' => [], // TODO shipper options
            ],
            'additional_info' => [
                [
                    'code' => $this->getCode(),
                    'name' => $this->getAdditionalData('displayName'),
                    'date' => date("Y-m-d H:i:s"), // TODO get desired delivery datetime
                    'time' => date("H:i - H:i"), // TODO desired delivery time slot
                    'price' => $this->getPrice(), // TODO shipper price only
                    'total_price' => $this->getPrice(), // TODO shipper price + shipper options
                ]
            ]]);
    }

    /** Determine shipping type
     * @return string
     */
    protected function getShippingType(): string
    {
        $type = 'delivery';

        // Pickup point has no delivery type but has a postal code
        if (!$this->getAdditionalData('deliveryType') && $this->getAdditionalData('postalCode')) {
            $type = 'pickup';
        }
        return $type;
    }
}