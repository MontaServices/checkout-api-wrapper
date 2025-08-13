<?php

namespace Monta\CheckoutApiWrapper;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Monta\CheckoutApiWrapper\Objects\Address as MontaCheckout_Address;
use Monta\CheckoutApiWrapper\Objects\Order as MontaCheckout_Order;
use Monta\CheckoutApiWrapper\Objects\PickupPoint as MontaCheckout_PickupPoint;
use Monta\CheckoutApiWrapper\Objects\Product as MontaCheckout_Product;
use Monta\CheckoutApiWrapper\Objects\Settings;
use Monta\CheckoutApiWrapper\Objects\ShippingOption as MontaCheckout_ShippingOption;
use Monta\CheckoutApiWrapper\Objects\TimeFrame as MontaCheckout_TimeFrame;

class MontapackingShipping
{
    /**
     * @var Settings
     */
    private Settings $settings;

    /**
     * @var MontaCheckout_Order
     * @deprecated - Property is written but never read
     */
    private ?MontaCheckout_Order $_order = null;

    /**
     * @var MontaCheckout_Product[]
     */
    private array $_products = [];

    /**
     * @var bool
     */
    private bool $_onStock = true;

    /** @var string|null $lastResponseCode - HTTP response code from latest request */
    protected ?string $lastResponseCode = null;

    /**
     * @var ?MontaCheckout_Address
     */
    public ?MontaCheckout_Address $address = null;

    /**
     * MontapackingShipping constructor.
     *
     * @param Settings $settings
     * @param string $language
     * @deprecated - Use ApiFactory method instead
     */
    public function __construct(Settings $settings, string $language)
    {
        $settings->setWebshopLanguage($language);

        $this->setSettings($settings);
    }

    /**
     * @param $value
     */
    public function setOnStock($value): void
    {
        $this->_onStock = $value;
    }

    /**
     * @return bool
     * @deprecated - Value is always default
     */
    public function getOnStock(): bool
    {
        return $this->_onStock;
    }

    /**
     * @param $total_incl
     * @param $total_excl
     * @deprecated - Property is set but never used
     */
    public function setOrder($total_incl, $total_excl): void
    {
        $this->_order = new MontaCheckout_Order($total_incl, $total_excl);
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
     */
    public function setAddress($street, $houseNumber, $houseNumberAddition, $postalCode, $city, $state, $countryCode): void
    {
        $this->address = new MontaCheckout_Address(
            $street,
            $houseNumber,
            $houseNumberAddition,
            $postalCode,
            $city,
            $state,
            $countryCode,
            $this->getSettings()->getGoogleKey()
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
     */
    public function addProduct(string $sku, int $quantity, int $lengthMm = 0, int $widthMm = 0, int $heightMm = 0, int $weightGrammes = 0, float $price = 0): void
    {
        $this->_products[] = new MontaCheckout_Product($sku, $lengthMm, $widthMm, $heightMm, $weightGrammes, $quantity, $price);
    }

    /**
     * @param bool $onstock @deprecated - Never used or called
     * @param bool $mailbox
     * @param bool $mailboxfit
     * @param bool $trackingonly
     * @param bool $insurance
     *
     * @return array
     */

    /**
     * @return array
     * @throws GuzzleException
     */
    public function getShippingOptions(): array
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
                    $timeframes[] = new MontaCheckout_TimeFrame(
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
                    $pickups[] = new MontaCheckout_PickupPoint($pickup->displayName,
                        $pickup->shipperCode,
                        $pickup->code,
                        $pickup->distanceMeters,
                        $pickup->company,
                        $pickup->street,
                        $pickup->houseNumber,
                        $pickup->postalCode,
                        $pickup->district,
                        $pickup->city, $pickup->state,
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
                $standardShipper = new MontaCheckout_ShippingOption(
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
                $storeLocation = new MontaCheckout_PickupPoint($result->store_location->displayName,
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

        return ['DeliveryOptions' => $timeframes, 'PickupOptions' => $pickups, 'StandardShipper' => $standardShipper, 'CustomerLocation' => $this->address, 'StoreLocation' => $storeLocation];
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
                // TODO this URl is used by Shopware connection test, what about `api-gateway.monta.nl`?
                url: 'https://api-v6.monta.nl/',
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
    public function call(string $method, string $url = "https://api-gateway.monta.nl/selfhosted/checkout/", array $parameters = [], string $httpMethod = "POST"): mixed
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
            'products' => $this->_products,
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
     * @return MontaCheckout_TimeFrame
     */
    private function getFallbackTimeframe(): MontaCheckout_TimeFrame
    {
        return new MontaCheckout_TimeFrame(
            dateOnlyFormatted: "Unknown",
            options: [new MontaCheckout_ShippingOption(
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
            'products' => $this->_products,
            'excludeShippingDiscount' => $this->getSettings()->getExcludeShippingDiscount(),
            'showZeroCostsAsFree' => $this->getSettings()->getShowZeroCostsAsFree(),
            'currencySymbol' => $this->getSettings()->getCurrency(),
            'hideDHLPackstations ' => $this->getSettings()->getHideDHLPackstations()
        ];

        return json_encode($jsonRequest);
    }
}
