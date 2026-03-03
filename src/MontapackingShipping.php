<?php

namespace Monta\CheckoutApiWrapper;

use GuzzleHttp\Exception\GuzzleException;
use Monta\CheckoutApiWrapper\Objects\Address;
use Monta\CheckoutApiWrapper\Objects\Order;
use Monta\CheckoutApiWrapper\Objects\PickupPoint;
use Monta\CheckoutApiWrapper\Objects\Product;
use Monta\CheckoutApiWrapper\Objects\Settings;
use Monta\CheckoutApiWrapper\Objects\ShippingOption;
use Monta\CheckoutApiWrapper\Objects\TimeFrame;
use Monta\CheckoutApiWrapper\Service\Address as AddressHelper;
use Monta\CheckoutApiWrapper\Service\Guzzle;
use Monta\CheckoutApiWrapper\Traits\CachedOptions;

class MontapackingShipping
{
    use CachedOptions;

    /** @var string - URI of CheckoutService for shipping options */
    protected const string MONTA_REST_CHECKOUT_URI = 'https://api-gateway.monta.nl/selfhosted/checkout/';

    /** @var string - URI of API for testing info TODO use gateway URI once CheckoutService adds /info endpoint */
    protected const string MONTA_REST_INFO_URI = 'https://api-v6.monta.nl/';

    /**
     * @var ?Order
     * @deprecated - Property is written but never read
     */
    private ?Order $order = null;

    /**
     * @var Product[]
     */
    protected array $products = [];

    /**
     * @var bool
     */
    protected bool $onStock = true;

    /** @var string|null $lastResponseCode - HTTP response code from latest request */
    protected ?string $lastResponseCode = null;

    /**
     * @var ?Address
     */
    public ?Address $address = null;

    /**
     * @param Settings $settings - Set in constructor
     * @param string $language
     * @deprecated - Use ApiFactory method instead
     */
    public function __construct(
        protected readonly Settings $settings,
        string $language,
    )
    {
        $this->settings->setWebshopLanguage($language);
    }

    /**
     * @param $value
     */
    public function setOnStock($value): void
    {
        $this->onStock = $value;
    }

    /**
     * @return bool
     * @deprecated - Value is always default
     */
    public function getOnStock(): bool
    {
        return $this->onStock;
    }

    /**
     * @param $total_incl
     * @param $total_excl
     * @deprecated - Property is set but never used
     */
    public function setOrder($total_incl, $total_excl): void
    {
        $this->order = new Order($total_incl, $total_excl);
    }

    /** Generic address setter from array
     *
     * @param array $address
     */
    public function setAddressFromArray(array $address): void
    {
        $this->address = AddressHelper::convertAddress(
            address: $address,
            // Pass along api key if available
            googleApiKey: $this->getSettings()->getGoogleKey(),
        );
    }

    /**
     * @param $street
     * @param $houseNumber
     * @param $houseNumberAddition
     * @param $postalCode
     * @param $city
     * @param $state
     * @param $countryCode
     * @deprecated - Use setAddressFromArray instead
     */
    protected function setAddress(
        $street,
        $houseNumber,
        $houseNumberAddition,
        $postalCode,
        $city,
        $state,
        $countryCode,
    ): void
    {
        $args = func_get_args();
        // Add GoogleKey to address as well
        $args[] = $this->getSettings()->getGoogleKey();
        // Splat parameters to pass along to method
        $this->address = new Address(...$args);
    }

    /** Separate method for adding as simple array
     *
     * @param array $cartItem
     * @param string $inputMeasures
     * @return void
     */
    public function addProductFromArray(array $cartItem, string $inputMeasures = 'kilo'): void
    {
        // Normalize weight
        $weight = $cartItem['weight'] ?? 0;
        switch ($inputMeasures) {
            case 'kilo':
                // When input is in kg, convert to gram
                $weight = $weight * 1000;
                break;
        }

        $this->addProduct(
            sku: $cartItem['sku'],
            // Various systems might pass quantity in either key
            quantity: $cartItem['qty'] ?? $cartItem['quantity'],
            lengthMm: $cartItem['length'] ?? 0,
            widthMm: $cartItem['width'] ?? 0,
            heightMm: $cartItem['height'] ?? 0,
            weightGrammes: $weight,
            price: $cartItem['price'] ?? $cartItem['final_price'],
        );
    }

    /**
     * @param string $sku
     * @param int $quantity
     * @param int $lengthMm
     * @param int $widthMm
     * @param int $heightMm
     * @param int $weightGrammes
     * @param float $price
     * @return void
     */
    protected function addProduct(
        string $sku,
        int $quantity,
        int $lengthMm = 0,
        int $widthMm = 0,
        int $heightMm = 0,
        int $weightGrammes = 0,
        float $price = 0,
    ): void
    {
        // Pass along arguments as named arguments
        $this->products[] = new Product(...func_get_args());
    }

    /**
     * @param bool $computeKm - Distance is received in meters, return as kilometers
     * @param bool $cacheResults - Keep response in cache for later use
     * @return array
     * @throws GuzzleException
     */
    public function getShippingOptions(bool $computeKm = false, bool $cacheResults = false): array
    {
        $timeframes = [];
        $pickups = [];
        $standardShipper = null;
        $storeLocation = null;

        // Postal code must be set
        if (trim($this->address->postalCode ?? '')
            // and either housenumber or street
            && (trim($this->address->houseNumber ?? '') || trim($this->address->street ?? ''))
        ) {
            if (!$this->getSettings()->getIsPickupPointsEnabled()) {
                $this->getSettings()->setMaxPickupPoints(0);
            }

            /** @var object $result - Call REST API, get arrays of stdClass objects */
            $result = $this->call(method: 'shippingrates', parameters: $this->getJsonRequest());

            // When API had a failure, use fallback
            if (!$result || $this->lastResponseCode != 200) {
                $result = (object)[];
                $result->timeframes = [self::getFallbackTimeframe()];
            }

            if (isset($result->timeframes)) {
                foreach ($result->timeframes as $stdTimeframe) {
                    // Convert stdClass into TimeFrame class
                    $timeframe = TimeFrame::construct((array)$stdTimeframe);
                    // Options in result might be in different keys, try both
                    $timeframe->setOptions($stdTimeframe->ShippingOptions ?? $stdTimeframe->options ?? []);
                    $timeframes[] = $timeframe;
                }
            }

            if (isset($result->pickup_locations)) {
                foreach ($result->pickup_locations as $stdPickup) {
                    // PickupPoints could be missing a Code, rare but property is required by code and logic
                    if ($stdPickup->code) {
                        if ($computeKm) {
                            // Recompute meters into kilometers (API passes meters)
                            $stdPickup->distanceMeters = round(num: $stdPickup->distanceMeters / 1000, precision: 2);
                        }
                        $pickups[] = PickupPoint::construct((array)$stdPickup);
                    }
                }
            }

            if (isset($result->standard_shipper)) {
                $standardShipper = ShippingOption::construct((array)$result->standard_shipper);
            }

            if (isset($result->store_location)) {
                $storeLocation = PickupPoint::construct((array)$result->store_location);
            }
        }

        $results = [
            ShippingOption::SHIPPING_OPTIONS_KEY => $timeframes,
            PickupPoint::PICKUP_OPTIONS_KEY => $pickups,
            ShippingOption::SHIPPING_STANDARD_KEY => $standardShipper,
            'CustomerLocation' => $this->address,
            PickupPoint::PICKUP_STORE_KEY => $storeLocation,
        ];

        // Keep in cache for later checking
        if ($cacheResults) {
            $this->saveResults($results);
        }
        return $results;
    }

    /** Check if connection and credentials are correct
     *
     * @return bool
     */
    public function testConnection(): bool
    {
        $success = false;
        try {
            $response = $this->call(
                method: "info",
                url: self::MONTA_REST_INFO_URI,
                httpMethod: "GET",
            );
            // Successful info test returns some Origins (according to donor Shopware test functionality)
            if ($this->getLastResponse() == 200 && !empty($response->Origins)) {
                $success = true;
            }
        } catch (GuzzleException $e) {
            // Catch and ignore, success is false
        }
        return $success;
    }

    /**
     * @param string $method
     * @param string $url - URI for the CheckoutService gateway
     * @param array $parameters
     * @param string $httpMethod
     * @return mixed
     * @throws GuzzleException
     */
    protected function call(
        string $method,
        string $url = self::MONTA_REST_CHECKOUT_URI,
        array $parameters = [],
        string $httpMethod = "POST",
    ): mixed
    {
        // Activate for connecting to locally running CheckoutService (WSL/DDEV)
//        $url = "https://host.docker.internal:53707/selfhosted/";

        $headers = [
            'Authorization' => 'Basic ' . base64_encode(
                    $this->getSettings()->getUser() . ":" . $this->getSettings()->getPassword(),
                ),
        ];

        $method = strtolower($method);

        $response = null;
        $result = (object)[];
        try {
            $response = Guzzle::call(
                route: $method,
                baseUri: $url,
                httpMethod: $httpMethod,
                parameters: $parameters,
                headers: $headers,
            );
            $this->lastResponseCode = $response->getStatusCode();
        } catch (\Exception $exception) {
            $this->lastResponseCode = 404;
            if ($response != null) {
                // TODO how can CheckoutApiWrapper log when it has no DB and no filesystem?
                $error_msg = $response->getReasonPhrase() . ' : ' . $response->getBody();
            }
        }

        // If response was not empty, decode and return
        return $response ? json_decode($response->getBody()) : [];
    }

    /** Get HTTP Response code of most recent
     *
     * @return string
     */
    protected function getLastResponse(): string
    {
        return $this->lastResponseCode;
    }

    /**
     * @return TimeFrame
     */
    private function getFallbackTimeframe(): TimeFrame
    {
        return new TimeFrame(
            dateOnlyFormatted: TimeFrame::FALLBACK_DATEONLY_CODE,
            options: [
                new ShippingOption(
                    shipper: 'Standard Shipper',
                    code: 'montapacking_standard',
                    displayNameShort: 'Standard Shipper',
                    displayName: 'Standard Shipper',
                    deliveryType: 'Unknown',
                    shippingType: "DeliveryTimeframeType",
                    price: $this->getSettings()->getDefaultCosts(),
                    priceFormatted: $this->getSettings()->getCurrency() . " " . $this->getSettings()->getDefaultCosts(),
                    shipperCodes: ["MultipleShipper_ShippingDayUnknown"],
                    // Explicitly hide image for fallback
                    imageUrl: false,
                ),
            ],
        );
    }

    /**
     * @return Settings
     */
    protected function getSettings(): Settings
    {
        return $this->settings;
    }

    /** Backwards compatible alias for that method
     *
     * @return string
     * @deprecated - TODO Is this ever used??
     */
    public function GetDebugPostBodyJson(): string
    {
        return json_encode($this->getJsonRequest());
    }

    /** Pack all data into JSON request body
     *
     * @return array - Encoded JSON string or associative array
     */
    protected function getJsonRequest(): array
    {
        $jsonRequest = [
            'userName' => $this->getSettings()->getUser(),
            'password' => $this->getSettings()->getPassword(),
            'channel' => $this->getSettings()->getOrigin(),
            'webshopLanguage' => $this->getSettings()->getWebshopLanguage(),
            'googleAPIKey' => $this->getSettings()->getGoogleKey(),
            'usePickupPoints' => $this->getSettings()->getIsPickupPointsEnabled(),
            'useShipperOptions' => true,
            'numberOfPickupPoints' => $this->getSettings()->getMaxPickupPoints(),
            'defaultCosts' => $this->getSettings()->getDefaultCosts(),
            'products' => $this->products,
            'excludeShippingDiscount' => $this->getSettings()->getExcludeShippingDiscount(),
            'showZeroCostsAsFree' => $this->getSettings()->getShowZeroCostsAsFree(),
            'currencySymbol' => $this->getSettings()->getCurrency(),
            Settings::SYSTEM_INFO_NAME => $this->getSettings()->getSystemInfo(),
        ];

        // Add address to request when set
        if ($this->address) {
            // Merge arrays, give preference to the actual address object
            $jsonRequest = array_merge($jsonRequest, [
                'streetaddress' => $this->address->street . ' ' . $this->address->houseNumber . $this->address->houseNumberAddition,
                'city' => $this->address->city,
                'postalcode' => $this->address->postalCode,
                'countrycode' => $this->address->countryCode,
            ]);
        }

        if ($this->getOnStock()) {
            $jsonRequest['productsOnStock'] = true;
        }

        return $jsonRequest;
    }
}