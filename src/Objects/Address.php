<?php

namespace Monta\CheckoutApiWrapper\Objects;

// alias for sibling must remain or not all autoloading will work
use GuzzleHttp\Exception\GuzzleException;
use Monta\CheckoutApiWrapper\Objects\Objectable as Objectable;
use Monta\CheckoutApiWrapper\Service\Guzzle;
use Monta\CheckoutApiWrapper\Service\Session;

class Address extends Objectable
{
    /** @var float|null $longitude - Public to use on frontend */
    public ?float $longitude = 0.0;

    /** @var float|null $latitude */
    public ?float $latitude = 0.0;

    /**
     * @param string $street
     * @param string|null $houseNumber
     * @param string|null $houseNumberAddition
     * @param string $postalCode
     * @param string $city
     * @param string|null $state
     * @param string $countryCode
     * @param string|null $googleApiKey @deprecated - duplicate with Settings.googleKey property
     */
    public function __construct(
        public string $street,
        public ?string $houseNumber,
        public ?string $houseNumberAddition,
        public string $postalCode,
        public string $city,
        public ?string $state,
        public string $countryCode,
        #[\SensitiveParameter]
        protected ?string $googleApiKey = null,
    )
    {
        // Constructor sets elevated properties, this specific one has custom functionality in setter
        $this->setGoogleApiKey($googleApiKey);
    }

    /** Geocode address to validate and retrieve coordinates
     *
     */
    public function setLongLat(): void
    {
        $prepAddr = $this->getPrepareAddress();
        $sessionPath = $prepAddr . "-coordinates";

        // Get address from cache if already there
        $coords = Session::get($sessionPath);
        // If not, retrieve from API and write into cache
        if (!$coords) {
            try {
                $response = Guzzle::call(
                    route: "maps/api/geocode/json",
                    baseUri: "https://maps.googleapis.com", // V3 standard
                    httpMethod: "GET",
                    parameters: [
                        'address' => $prepAddr,
                        'sensor' => false,
                        'key' => $this->googleApiKey,
                    ],
                );

                $output = json_decode($response->getBody());

                // Pluck single result from array of one
                $result = end($output->results);

                // Without geometry, Google Maps will not initalize. Pickup locations will be a plain list.
                if (isset($result->geometry)) {
                    $coords = [
                        $result->geometry->location->lat,
                        $result->geometry->location->lng,
                    ];

                    // Save this result in cache, avoid multiple duplicate API calls
                    Session::save($sessionPath, $coords);
                }
            } catch (GuzzleException $ge) {
            } catch (\Exception $e) {
                // Catch and ignore Exceptions, coordinates remain zero
            }
        }

        // Whether retrieved from cache or from API, assign both variables here
        [$this->latitude, $this->longitude] = $coords;
    }

    /**
     * @return string
     */
    public function getPrepareAddress(): string
    {
        // Get lat and long by address
        $address = $this->houseNumber . ' ' . $this->houseNumberAddition . ', ' . $this->postalCode . ' ' . $this->countryCode;
        // Add city, or it will always return "ZERO RESULTS" for Belgian zipcodes
        // Google appears to ignore the city for other countries, only looks at zipcode. Yet it must be in the request
        $prepAddress = $this->city . str_replace('  ', ' ', $address);

        // Replace spaces with pluses to make it Google-friendly
        return str_replace(' ', '+', $prepAddress);
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
     * @param string|null $googleApiKey
     *
     * @return $this
     */
    public function setGoogleApiKey(#[\SensitiveParameter] ?string $googleApiKey): Address
    {
        if ($googleApiKey) {
            $this->googleApiKey = trim($googleApiKey);

            // After setting Google Key, coordinates can be calculated
            $this->setLongLat();
        }

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