<?php

namespace Monta\CheckoutApiWrapper;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Monta\CheckoutApiWrapper\Objects\Address;
use Monta\CheckoutApiWrapper\Objects\Order;
use Monta\CheckoutApiWrapper\Objects\PickupPoint;
use Monta\CheckoutApiWrapper\Objects\Product;
use Monta\CheckoutApiWrapper\Objects\Settings;
use Monta\CheckoutApiWrapper\Objects\ShippingOption;
use Monta\CheckoutApiWrapper\Objects\TimeFrame;
use Monta\CheckoutApiWrapper\Service\Address as AddressHelper;

class MontapackingShipping
{
    /** @var string - URI for deliveryoptions etc. */
    protected const string MONTA_REST_CHECKOUT_URI = 'https://api-gateway.monta.nl/selfhosted/checkout/';

    /** @var string - URI for testing info TODO why are these separate? Tested both on both and they work either way */
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
     * MontapackingShipping constructor.
     *
     * @param Settings $settings
     * @param string $language
     * @deprecated - Use ApiFactory method instead
     */
    public function __construct(
        protected Settings $settings,
        string $language)
    {
        $settings->setWebshopLanguage($language);

        $this->setSettings($settings);
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
     * @return void
     * @throws GuzzleException
     */
    public function setAddressFromArray(array $address): void
    {
        $this->address = AddressHelper::convertAddress($address);
    }

    /**
     * @param $street
     * @param $houseNumber
     * @param $houseNumberAddition
     * @param $postalCode
     * @param $city
     * @param $state
     * @param $countryCode
     * @throws GuzzleException
     * @deprecated - Use setAddressFromArray instead
     */
    public function setAddress(
        $street,
        $houseNumber,
        $houseNumberAddition,
        $postalCode,
        $city,
        $state,
        $countryCode
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
    public function addProduct(
        string $sku,
        int $quantity,
        int $lengthMm = 0,
        int $widthMm = 0,
        int $heightMm = 0,
        int $weightGrammes = 0,
        float $price = 0
    ): void
    {
        // Pass along arguments as named arguments
        $this->products[] = new Product(...func_get_args());
    }

    /**
     * @param bool $computeKm - Distance is received in meters, return as kilometers?
     * @return array
     * @throws GuzzleException
     */
    public function getShippingOptions(bool $computeKm = false): array
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

            $result = $this->call('shippingrates');

            if (isset($result->timeframes)) {
                foreach ($result->timeframes as $timeframe) {
                    $timeframes[] = new TimeFrame(
                        $timeframe->date,
                        $timeframe->day,
                        $timeframe->month,
                        $timeframe->dateFormatted,
                        $timeframe->dateOnlyFormatted,
                        $timeframe->ShippingOptions ?? $timeframe->options ?? []
                    );
                }
            }

            if (isset($result->pickup_locations)) {
                foreach ($result->pickup_locations as $pickup) {
                    $distance = $pickup->distanceMeters;
                    // Recompute meters into kilometers
                    if ($computeKm) {
                        $distance = round(num: $distance / 1000, precision: 2);
                    }
                    $pickups[] = new PickupPoint(
                        $pickup->displayName,
                        $pickup->shipperCode,
                        $pickup->code,
                        $distance,
                        $pickup->company,
                        $pickup->street,
                        $pickup->houseNumber,
                        $pickup->postalCode,
                        $pickup->district,
                        $pickup->city,
                        $pickup->state,
                        $pickup->countryCode,
                        $pickup->addressRemark,
                        $pickup->phone,
                        $pickup->longitude,
                        $pickup->latitude,
                        $pickup->imageUrl,
                        $pickup->price,
                        $pickup->priceFormatted,
                        $pickup->openingTimes,
                        $pickup->shipperOptionsWithValue
                    );
                }
            }

            if (isset($result->standard_shipper)) {
                $standardShipper = new ShippingOption(
                    $result->standard_shipper->shipper,
                    $result->standard_shipper->code,
                    $result->standard_shipper->displayNameShort,
                    $result->standard_shipper->displayName,
                    $result->standard_shipper->from,
                    $result->standard_shipper->to,
                    $result->standard_shipper->deliveryType,
                    $result->standard_shipper->shippingType,
                    $result->standard_shipper->price,
                    $result->standard_shipper->priceFormatted,
                    $result->standard_shipper->discountPercentage,
                    $result->standard_shipper->isPreferred,
                    $result->standard_shipper->isSustainable,
                    $result->standard_shipper->deliveryOptions,
                    $result->standard_shipper->optionCodes,
                    $result->standard_shipper->shipperCodes
                );
            }

            if (isset($result->store_location)) {
                $storeLocation = new PickupPoint($result->store_location->displayName,
                    $result->store_location->shipperCode,
                    $result->store_location->code,
                    $result->store_location->distanceMeters,
                    $result->store_location->company,
                    $result->store_location->street,
                    $result->store_location->houseNumber,
                    $result->store_location->postalCode,
                    $result->store_location->district,
                    $result->store_location->city, $result->store_location->state,
                    $result->store_location->countryCode,
                    $result->store_location->addressRemark,
                    $result->store_location->phone,
                    $result->store_location->longitude,
                    $result->store_location->latitude,
                    $result->store_location->imageUrl,
                    $result->store_location->price,
                    $result->store_location->priceFormatted,
                    $result->store_location->openingTimes,
                    $result->store_location->shipperOptionsWithValue
                );
            }
        }

        return [
            'DeliveryOptions' => $timeframes,
            PickupPoint::PICKUP_OPTIONS_KEY => $pickups,
            'StandardShipper' => $standardShipper,
            'CustomerLocation' => $this->address,
            'StoreLocation' => $storeLocation,
        ];
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
                // query parameters
                parameters: [
                    // TODO this value is irrelevant for the `info` endpoint, output is the identical regardless
                    'origin' => $this->getSettings()->getOrigin()
                ],
                httpMethod: "GET",
            );
            // Succesful info test returns some Origins (based on existing Shopware test functionality)
            if ($this->getLastResponse() == 200 && !empty($response->Origins)) {
                $success = true;
            }
        } catch (GuzzleException $e) {
            // TODO log erorr, success remains false.
        }
        return $success;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $parameters
     * @param string $httpMethod
     * @return mixed
     * @throws GuzzleException
     */
    public function call(string $method, string $url = self::MONTA_REST_CHECKOUT_URI, array $parameters = [], string $httpMethod = "POST"): mixed
    {
//        $url = "https://host.docker.internal:52668/selfhosted/";

        $client = new Client([
            'verify' => false,
            'base_uri' => $url,
            'timeout' => 10.0,
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->getSettings()->getUser() . ":" . $this->getSettings()->getPassword())
            ]
        ]);

        $method = strtolower($method);
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
            Settings::SYSTEM_INFO_NAME => $this->getSettings()->getSystemInfo(),
            'showZeroCostsAsFree' => $this->getSettings()->getShowZeroCostsAsFree(),
            'currencySymbol' => $this->getSettings()->getCurrency(),
            'hideDHLPackstations' => $this->getSettings()->getHideDHLPackstations()
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

        $response = null;
        $result = (object)[];
        try {
            switch ($httpMethod) {
                case "POST":
                    $response = $client->post($method, [
                        'json' => $jsonRequest
                    ]);
                    break;
                case "GET":
                    if ($parameters) {
                        $method .= "?" . http_build_query($parameters);
                    }
                    $response = $client->get($method);
                    break;
                default:
                    throw new \Exception("Unsupported HTTP method: " . $httpMethod);
            }
            $this->lastResponseCode = $response->getStatusCode();
        } catch (\Exception $exception) {
            $this->lastResponseCode = 404;
            if ($response != null) {
                // Create abstract logger here later that logs to local file storage
                $error_msg = $response->getReasonPhrase() . ' : ' . $response->getBody();
            }
        }

        if ($response == null || $response->getStatusCode() != 200) {
//            $context = ['source' => 'Montapacking Checkout'];
            $result->timeframes = [self::getFallbackTimeframe()];

            return $result;
        }

        return json_decode($response->getBody());
    }

    /**
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
            dateOnlyFormatted: "Unknown",
            options: [new ShippingOption(
                shipper: 'Standard Shipper',
                code: 'montapacking_standard',
                displayNameShort: 'Standard Shipper',
                displayName: 'Standard Shipper',
                deliveryType: 'Unknown',
                shippingType: "DeliveryTimeframeType",
                price: $this->getSettings()->getDefaultCosts(),
                priceFormatted: $this->getSettings()->getCurrency() . $this->getSettings()->getDefaultCosts(),
                shipperCodes: ["MultipleShipper_ShippingDayUnknown"]
            )],
        );
    }

    /**
     * @return Settings
     */
    public function getSettings(): Settings
    {
        return $this->settings;
    }

    /**
     * @param Settings $settings
     */
    public function setSettings(Settings $settings): void
    {
        $this->settings = $settings;
    }

    public function GetDebugPostBodyJson(): string
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
            'streetaddress' => $this->address->street . ' ' . $this->address->houseNumber . $this->address->houseNumberAddition,
            'city' => $this->address->city,
            'postalcode' => $this->address->postalCode,
            'countrycode' => $this->address->countryCode,
            'products' => $this->products,
            'excludeShippingDiscount' => $this->getSettings()->getExcludeShippingDiscount(),
            'showZeroCostsAsFree' => $this->getSettings()->getShowZeroCostsAsFree(),
            'currencySymbol' => $this->getSettings()->getCurrency(),
            'hideDHLPackstations ' => $this->getSettings()->getHideDHLPackstations()
        ];

        return json_encode($jsonRequest);
    }
}
