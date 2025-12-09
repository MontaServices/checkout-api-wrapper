<?php

namespace Monta\CheckoutApiWrapper\Objects;

// alias for sibling must remain or not all autoloading will work
use Monta\CheckoutApiWrapper\Objects\Option as Option;

/**
 * Class PickupPoint
 *
 */
class PickupPoint extends Option
{
    public const string PICKUP_OPTIONS_KEY = 'PickupOptions';

    public const string PICKUP_STORE_KEY = 'StoreLocation';

    /** Properties must be public so they are added to JSON object
     *
     * @param string $displayName
     * @param string $shipperCode
     * @param string $code
     * @param float $distanceMeters
     * @param string $company
     * @param string $street
     * @param string|null $houseNumber
     * @param string $postalCode
     * @param string|null $district
     * @param string $city
     * @param string|null $state
     * @param string $countryCode
     * @param string|null $addressRemark
     * @param string|null $phone
     * @param float $longitude
     * @param float $latitude
     * @param string|null $imageUrl
     * @param float $price
     * @param string $priceFormatted
     * @param array $openingTimes
     * @param string $shipperOptionsWithValue
     * @param string $shipperGroupName
     * @param string|null $imageName
     * @param string|null $formattedAddress - Display value for address
     * @param string[] $position - Format according to Google Maps API
     */
    public function __construct(
        string $displayName,
        public string $shipperCode,
        string $code,
        public float $distanceMeters,
        // TODO maybe replace all these values with simply an Address object
        public string $company,
        public string $street,
        public ?string $houseNumber,
        public string $postalCode,
        public ?string $district,
        public string $city,
        public ?string $state,
        public string $countryCode,
        public ?string $addressRemark,
        public ?string $phone,
        public float $longitude,
        public float $latitude,
        ?string $imageUrl,
        float $price,
        string $priceFormatted,
        public array $openingTimes,
        public string $shipperOptionsWithValue,
        string $shipperGroupName = "",
        public ?string $imageName = null,
        public ?string $formattedAddress = null,
        public array $position = [],
    )
    {
        parent::__construct(
            code: $code,
            displayName: $displayName,
            price: $price,
            priceFormatted: $priceFormatted,
            imageUrl: $imageUrl,
            shipperGroupName: $shipperGroupName,
        );

        // Format address for display on frontend
        $this->formattedAddress = $this->street . ' ' . $this->houseNumber . ', ' . $this->postalCode . ' ' . $this->city;

        // Fill this property to use as Marker in Google Maps API
        $this->position = [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];

        // When image URL was not passed, construct it from here
        if (!$imageUrl) {
            // TODO use $this->shipperGroupName as soon as that's added to REST API output, instead of this temp "DHL" placeholder
            $this->setImageUrl($this->getImageUrl("DHL"));
        }
    }

    /**
     * @return string
     */
    public function getShipperCode(): string
    {
        return $this->shipperCode;
    }

    /**
     * @param string $shipperCode
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function setShipperCode(string $shipperCode): void
    {
        $this->shipperCode = $shipperCode;
    }

    /** Name is confusing, value is usually already in kilometers
     *
     * @return float
     */
    public function getDistanceMeters(): float
    {
        return $this->distanceMeters;
    }

    /**
     * @param float $distanceMeters
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function setDistanceMeters(float $distanceMeters): void
    {
        $this->distanceMeters = $distanceMeters;
    }

    /**
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * @param string $company
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function setCompany(string $company): void
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @param string $street
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }

    /**
     * @param string|null $houseNumber
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function setHouseNumber(?string $houseNumber): void
    {
        $this->houseNumber = $houseNumber;
    }

    /**
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function setPostalCode(string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function getDistrict(): string
    {
        return $this->district;
    }

    /**
     * @param string|null $district
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function setDistrict(?string $district): void
    {
        $this->district = $district;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string|null $state
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return string
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function getAddressRemark(): string
    {
        return $this->addressRemark;
    }

    /**
     * @param string|null $addressRemark
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function setAddressRemark(?string $addressRemark): void
    {
        $this->addressRemark = $addressRemark;
    }

    /**
     * @return string
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return float
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return float
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return array
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function getOpeningTimes(): array
    {
        return $this->openingTimes;
    }

    /**
     * @param array $openingTimes
     * @deprecated - No usage anywhere, functionally done by promoted property
     */
    public function setOpeningTimes(array $openingTimes): void
    {
        $this->openingTimes = $openingTimes;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->getDisplayName()
            // name is misleading, distance here is kilometers
            . ' | ' . $this->getDistanceMeters() . 'km';
    }

    /** TODO rename to a proper camelCase name
     * @return string
     */
    public function get_shipper_options_with_value(): string
    {
        return $this->shipperOptionsWithValue;
    }

    /**
     * @param string $shipperOptionsWithValue
     * @deprecated - No usage in wrapper or Magento module
     */
    public function set_shipper_options_with_value(string $shipperOptionsWithValue): void
    {
        $this->shipperOptionsWithValue = $shipperOptionsWithValue;
    }
}