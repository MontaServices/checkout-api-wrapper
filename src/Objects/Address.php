<?php

namespace Monta\CheckoutApiWrapper\Objects;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Address
{
    /**
     * @var float
     * TODO can these be made protected?
     */
    public float $longitude = 0;

    /**
     * @var float
     */
    public float $latitude = 0;

    /**
     * @param string $street
     * @param string|null $houseNumber
     * @param string|null $houseNumberAddition
     * @param string $postalCode
     * @param string $city
     * @param ?string $state
     * @param string $countryCode
     * @param string $googleApiKey @deprecated - does not belong in Address object
     * @throws GuzzleException
     */
    public function __construct(
        public string $street,
        public ?string $houseNumber,
        public ?string $houseNumberAddition,
        public string $postalCode,
        public string $city,
        public ?string $state,
        public string $countryCode,
        public ?string $googleApiKey = null,
    )
    {
        // Properties are set in constructor, this setter has custom functionality
        if ($googleApiKey) {
            $this->setGoogleApiKey(trim($googleApiKey));
        }

        // Calculate coordinates based on address using Google Maps API
        $this->setLongLat();
    }

    /** Geocode address to validate and retrieve coordinates
     *
     * @return void
     * @throws GuzzleException
     */
    public function setLongLat(): void
    {
        // Get lat and long by address
        $address = $this->houseNumber . ' ' . $this->houseNumberAddition . ', ' . $this->postalCode . ' ' . $this->countryCode; // Google HQ
        // Add city, or it will always return "ZERO RESULTS" for Belgian zipcodes
        // Google appears to ignore the city for other countries, only looks at zipcode. Yet it must be in the request
        $prepAddr = $this->city . str_replace('  ', ' ', $address);
        $prepAddr = str_replace(' ', '+', $prepAddr);
        // TODO deprecated, use maps.googleapis.com which is the V3 standard
        $google_maps_url = "https://maps.google.com/maps/api/geocode/json?" . http_build_query([
                'address' => $prepAddr,
                'sensor' => false,
                'key' => $this->googleApiKey,
            ]);

        try {
            $client = new Client([
                'timeout' => 1.0
            ]);

            $response = $client->get($google_maps_url);

            $output = json_decode($response->getBody());

            $result = end($output->results);

            if (isset($result->geometry)) {
                $latitude = $result->geometry->location->lat;
                $longitude = $result->geometry->location->lng;
            } else {
                // Without geometry, Google Maps will not initalize. Pickup locations will be a plain list.
                $latitude = 0;
                $longitude = 0;
            }
        } catch (Exception) {
            $latitude = 0;
            $longitude = 0;
        }

        $this->longitude = $longitude;
        $this->latitude = $latitude;
    }

    /**
     * @param $street
     *
     * @return $this
     */
    public function setStreet($street): Address
    {
        $this->street = $street;

        return $this;
    }

    /**
     * @param $houseNumber
     *
     * @return $this
     */
    public function setHouseNumber($houseNumber): Address
    {
        $this->houseNumber = $houseNumber;

        return $this;
    }

    /**
     * @param $houseNumberAddition
     *
     * @return $this
     */
    public function setHouseNumberAddition($houseNumberAddition): Address
    {
        $this->houseNumberAddition = $houseNumberAddition;

        return $this;
    }

    /**
     * @param $postalCode
     *
     * @return $this
     */
    public function setPostalCode($postalCode): Address
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @param $city
     *
     * @return $this
     */
    public function setCity($city): Address
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @param $state
     *
     * @return $this
     */
    public function setState($state): Address
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @param $country
     *
     * @return $this
     */
    public function setCountry($country): Address
    {
        $this->countryCode = $country;

        return $this;
    }

    /**
     * @param $googleApiKey
     *
     * @return $this
     */
    public function setGoogleApiKey($googleApiKey): Address
    {
        $this->googleApiKey = $googleApiKey;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'Address.Street' => $this->street,
            'Address.HouseNumber' => $this->houseNumber,
            'Address.HouseNumberAddition' => $this->houseNumberAddition,
            'Address.PostalCode' => $this->postalCode,
            'Address.City' => $this->city,
            'Address.State' => $this->state,
            'Address.CountryCode' => $this->countryCode,
            'Address.Latitude' => $this->latitude,
            'Address.Longitude' => $this->longitude,
        ];
    }
}
