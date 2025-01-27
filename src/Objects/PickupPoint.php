<?php

namespace Monta\CheckoutApiWrapper\Objects;

use Monta\CheckoutApiWrapper\Objects\OpeningTime;

/**
 * Class PickupPoint
 *
 */
class PickupPoint
{
    public string $code;
    public float $distanceMeters;
    public string $company;
    public string $street;
    public ?string $houseNumber;
    public string $postalCode;
    public ?string $district;
    public string $city;
    public ?string $state;
    public string $countryCode;
    public ?string $addressRemark;
    public ?string $phone;
    public float $longitude;
    public float $latitude;
    public ?string $imageUrl;
    public float $price;
    public string $priceFormatted;
    public array $openingTimes;
    public string $shipperOptionsWithValue;

    /**
     * @param string $displayName
     * @param string $shipperCode
     * @param string $code
     * @param float $distanceMeters
     * @param string $company
     * @param string $street
     * @param ?string $houseNumber
     * @param string $postalCode
     * @param ?string $district
     * @param string $city
     * @param ?string $state
     * @param string $countryCode
     * @param ?string $addressRemark
     * @param ?string $phone
     * @param float $longitude
     * @param float $latitude
     * @param ?string $imageUrl
     * @param float $price
     * @param string $priceFormatted
     * @param array $openingTimes
     * @param string $shipperOptionsWithValue
     */
    public function __construct(string $displayName, string $shipperCode, string $code, float $distanceMeters, string $company, string $street, ?string $houseNumber, string $postalCode, ?string $district, string $city, ?string $state, string $countryCode, ?string $addressRemark, ?string $phone, float $longitude, float $latitude, ?string $imageUrl, float $price, string $priceFormatted, array $openingTimes, string $shipperOptionsWithValue)
    {
        $this->setDisplayName($displayName);
        $this->setShipperCode($shipperCode);
        $this->setCode($code);
        $this->setDistanceMeters($distanceMeters);
        $this->setDistrict($district);
        $this->setCompany($company);
        $this->setStreet($street);
        $this->setHouseNumber($houseNumber);
        $this->setPostalCode($postalCode);
        $this->setCity($city);
        $this->setState($state);
        $this->setCountryCode($countryCode);
        $this->setAddressRemark($addressRemark);
        $this->setPhone($phone);
        $this->setLongitude($longitude);
        $this->setLatitude($latitude);
        $this->setImageUrl($imageUrl);
        $this->setPrice($price);
        $this->setPriceFormatted($priceFormatted);
        $this->setOpeningTimes($openingTimes);
		$this->set_shipper_options_with_value($shipperOptionsWithValue);
    }

    public string $displayName;

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }

    /**
     * @var string
     */
    public string $shipperCode;

    /**
     * @return string
     */
    public function getShipperCode(): string
    {
        return $this->shipperCode;
    }

    /**
     * @param string $shipperCode
     */
    public function setShipperCode(string $shipperCode): void
    {
        $this->shipperCode = $shipperCode;
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
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
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
     * @return float
     */
    public function getDistanceMeters(): float
    {
        return $this->distanceMeters;
    }

    /**
     * @param float $distanceMeters
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
     * @param ?string $houseNumber
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
     */
    public function setPostalCode(string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getDistrict(): string
    {
        return $this->district;
    }

    /**
     * @param string|null $district
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
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string|null $state
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
     */
    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return string
     */
    public function getAddressRemark(): string
    {
        return $this->addressRemark;
    }

    /**
     * @param string|null $addressRemark
     */
    public function setAddressRemark(?string $addressRemark): void
    {
        $this->addressRemark = $addressRemark;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @param ?string $imageUrl
     */
    public function setImageUrl(?string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return array
     */
    public function getOpeningTimes(): array
    {
        return $this->openingTimes;
    }

    /**
     * @param array $openingTimes
     */
    public function setOpeningTimes(array $openingTimes): void
    {
        $list = [];
        foreach ($openingTimes as $option) {

            $list[] = new OpeningTime(
                $option->day,
                $option->from,
                $option->to,
            );
        }

        $this->openingTimes = $list;
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
	 * @return string
	 */
	public function get_shipper_options_with_value(): string {
		return $this->shipperOptionsWithValue;
	}

	/**
	 * @param string $shipperOptionsWithValue
	 */
	public function set_shipper_options_with_value( string $shipperOptionsWithValue ): void {
		$this->shipperOptionsWithValue = $shipperOptionsWithValue;
	}
}
