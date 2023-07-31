<?php

namespace Monta\MontaProcessing;

use Monta\MontaProcessing\Objects\Address as MontaCheckout_Address;
use Monta\MontaProcessing\Objects\Order as MontaCheckout_Order;
use Monta\MontaProcessing\Objects\Product as MontaCheckout_Product;
use Monta\MontaProcessing\Objects\Settings;
use Monta\MontaProcessing\Objects\TimeFrame as MontaCheckout_TimeFrame;
use Monta\MontaProcessing\Objects\PickupPoint as MontaCheckout_PickupPoint;
use GuzzleHttp\Client;

/**
 * Class MontapackingShipping
 *
 */
class MontapackingShipping
{
    /**
     * @var Settings
     */
    private Settings $settings;

    /**
     * @var array
     */
    private $_basic = null;
    /**
     * @var null
     */
    private $_order = null;
    /**
     * @var null
     */
    private $_shippers = null;
    /**
     * @var null
     */
    private $_products = null;
//    /**
//     * @var null
//     */
//    private $_allowedshippers = null;

    /**
     * @var bool
     */
    private $_onstock = true;

    /**
     * @var null
     */
    public $address = null;

    /**
     * @var null
     */
    private $_logger = null;

    /**
     * MontapackingShipping constructor.
     *
     * @param Settings $settings
     * @param      $language
     * @param bool $test
     */
    public function __construct(Settings $settings, $language, $test = false)
    {
        $this->setSettings($settings);

        $this->_user = $this->getSettings()->getUser();
        $this->_pass = $this->getSettings()->getPassword();
        $this->_maxPickupPoints = $this->getSettings()->getMaxPickupPoints();
        $this->_googlekey = $this->getSettings()->getGoogleKey();

        $this->_basic = [
            'Origin' => $this->getSettings()->getOrigin(),
            'Currency' => 'EUR',
            'Language' => $language,
        ];




//        $this->_user = $user;
//        $this->_pass = $pass;
//        $this->_maxPickupPoints = $maxPickupPoints;
//        $this->_googlekey = $googlekey;
//
//        $this->_basic = [
//            'Origin' => $origin,
//            'Currency' => 'EUR',
//            'Language' => $language,
//        ];
    }

    /**
     * @param $logger
     */
    public function setLogger($logger)
    {
        $this->_logger = $logger;
    }

    /**
     * @return bool
     */
    public function checkConnection()
    {

        $result = $this->call('info', ['_basic']);

        if (null === $result) {
            return false;
        }
        return true;
    }

    /**
     * @param $value
     */
    public function setOnstock($value)
    {
        $this->_onstock = $value;
    }

    /**
     * @return bool
     */
    public function getOnstock()
    {
        return $this->_onstock;
    }

    /**
     * @param $total_incl
     * @param $total_excl
     */
    public function setOrder($total_incl, $total_excl)
    {

        $this->_order = new MontaCheckout_Order($total_incl, $total_excl);
    }

    /**
     * @param $street
     * @param $housenumber
     * @param $housenumberaddition
     * @param $postalcode
     * @param $city
     * @param $state
     * @param $countrycode
     */
    public function setAddress($street, $housenumber, $housenumberaddition, $postalcode, $city, $state, $countrycode)
    {

        $this->address = new MontaCheckout_Address(
            $street,
            $housenumber,
            $housenumberaddition,
            $postalcode,
            $city,
            $state,
            $countrycode,
            $this->_googlekey
        );
    }

    /**
     * @param null $shippers
     */
//    public function setShippers($shippers = null)
//    {
//
//        if (is_array($shippers)) {
//            $this->_shippers = $shippers;
//        } else {
//            $this->_shippers[] = $shippers;
//        }
//    }

    /**
     * @param     $sku
     * @param     $quantity
     * @param int $length
     * @param int $width
     * @param int $height
     * @param int $weight
     */
    public function addProduct($sku, $quantity, $lengthMm = 0, $widthMm = 0, $heightMm = 0, $weightGrammes = 0)
    {

        $this->_products['products'][] = new MontaCheckout_Product($sku, $lengthMm, $widthMm, $heightMm, $weightGrammes, $quantity);
    }

    // Todo: Cleanup
    /**
     * @return array|null
     */
//    public function getShippers()
//    {
//
//        $shippers = null;
//
//        $result = $this->call('info', ['_basic']);
//        if (isset($result->Origins)) {
//
//            $origins = is_array($result->Origins) ? $result->Origins : [$result->Origins];
//
//            // Array goedzetten
//            if (is_array($result->Origins)) {
//                $origins = $result->Origins;
//            } else {
//                $origins[] = $result->Origins;
//            }
//
//            // Shippers omzetten naar shipper object
//            foreach ($origins as $origin) {
//
//                // Check of shipper options object er is
//                if (isset($origin->ShipperOptions)) {
//
//                    foreach ($origin->ShipperOptions as $shipper) {
//
//                        $shippers[] = new Shipper(
//                                $shipper->ShipperDescription,
//                                $shipper->ShipperCode
//                        );
//
//                    }
//
//                }
//
//            }
//
//            return $origins;
//
//        }
//
//        return $shippers;
//    }

//    /**
//     * @param $sku
//     *
//     * @return bool
//     */
//    public function checkStock($sku)
//    {
//        $url = "https://api.montapacking.nl/rest/v5/";
//        $this->_pass = htmlspecialchars_decode($this->_pass);
//
//        $client = new Client([
//            // Base URI is used with relative requests
//            'base_uri' => $url,
//            // You can set any number of default request options.
//            'timeout' => 10.0,
//            'headers' => [
//                'Authorization' => 'Basic ' . base64_encode($this->_user . ":" . $this->_pass)
//            ]
//        ]);
//
//        $response = $client->get("product/" . $sku . "/stock");
//        $result = json_decode($response->getBody(), true);
//
//
//        if (null !== $result && property_exists($result, 'Message') && $result->Message == 'Zero products found with sku ' . $sku) {
//            return false;
//        } else if (null !== $result && property_exists($result, 'Stock') && $result->Stock->StockAvailable <= 0) {
//            return false;
//        } else {
//            return true;
//        }
//    }

    /**
     * @param bool $onstock
     * @param bool $mailbox
     * @param bool $mailboxfit
     * @param bool $trackingonly
     * @param bool $insurance
     *
     * @return array
     */

    /**
     * @param bool $onStock
     * @return array
     */
    public function getShippingOptions(bool $onStock = true)
    {
        $timeframes = [];
        $pickups = [];

        if (trim($this->address->postalcode) && (trim($this->address->housenumber) || trim($this->address->street))) {
            // Basis gegevens uitbreiden met shipping option specifieke data
            $this->_basic = array_merge(
                $this->_basic,
                [
                    'ProductsOnStock' => ($onStock) ? 'TRUE' : 'FALSE',
                ]
            );

            if(!$this->getSettings()->getIsPickupPointsEnabled())
            {
                $this->getSettings()->setMaxPickupPoints(0);
            }

//            if ($this->_carrierConfig->getDisablePickupPoints()) {
//                $this->_basic = array_merge(
//                    $this->_basic,
//                    [
//                        'MaxNumberOfPickupPoints' => 0
//                    ]
//                );
//            }

            // Timeframes omzetten naar bruikbaar object
            $result = $this->call('ShippingOptions', ['_basic', '_shippers', '_order', 'address', '_products']);

            if (isset($result->timeframes)) {
                // Shippers omzetten naar shipper object
                foreach ($result->timeframes as $timeframe) {
                    $timeframes[] = new MontaCheckout_TimeFrame(
                        $timeframe->date,
                        $timeframe->day,
                        $timeframe->month,
                        $timeframe->dateFormatted,
                        $timeframe->ShippingOptions
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
                        $pickup->openingTimes
                    );
                }
            }
        }

        return ['DeliveryOptions' => $timeframes, 'PickupOptions' => $pickups, 'CustomerLocation' => $this->address];
    }

    /**
     * @param      $method
     * @param null $send
     *
     * @return mixed
     */
    public function call($method, $send = null)
    {

        $request = '?';
        if ($send != null) {

            $requestBody = [];

            // Request needed data
            foreach ($send as $data) {

                if (isset($this->{$data}) && $this->{$data} != null) {

                    if (!is_array($this->{$data})) {

                        $request .= '&' . http_build_query($this->{$data}->toArray());
                        $requestBody[$data] = $this->{$data}->toArray();
                    } else {
                        $request .= '&' . http_build_query($this->{$data});
                        $requestBody[$data] = $this->{$data};

                    }

                }

            }
        }

        //$url = "https://api.montapacking.nl/rest/v5/";
        $url = "https://host.docker.internal:62884/selfhosted/";
        $this->_pass = htmlspecialchars_decode($this->_pass);

        $client = new Client([
            'verify' => false,
            'base_uri' => $url,
            'timeout' => 10.0,
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->_user . ":" . $this->_pass)
            ]
        ]);

        $method = strtolower($method);
        //$response = $client->get($method . '?' . $request);

        $jsonRequest = [
            'userName' => $this->getSettings()->getUser(),
            'password' => $this->getSettings()->getPassword(),
            'channel' => $this->getSettings()->getOrigin(),
            'googleAPIKey' => $this->getSettings()->getGoogleKey(),
//            'usePickupPoints' => $sdfs['_basic']['usePickupPoints'],
//            'useShipperOptions' => $sdfs['_basic']['useShipperOptions'],
//            'numberOfPickupPoints' => $sdfs['_basic']['numberOfPickupPoints'],
//            'defaultCosts' => $sdfs['_basic']['defaultCosts'],
//            'productsOnStock' => $sdfs['_basic']['productsOnStock'],


            'usePickupPoints' => $this->getSettings()->getIsPickupPointsEnabled(),
            'useShipperOptions' => true,
            'numberOfPickupPoints' => $this->getSettings()->getMaxPickupPoints(),
            'defaultCosts' => $this->getSettings()->getDefaultCosts(),
            'productsOnStock' => $this->getOnstock(),
            'streetaddress' => $this->address->street . ' ' . $this->address->housenumber . $this->address->housenumberaddition,
            'city' => $this->address->city,
            'postalcode' => $this->address->postalcode,
            'countrycode' => $this->address->countrycode,
//            'products' => $this->_products['products'],
        ];

        $response = $client->post($method, [
            'json' => $jsonRequest
        ]);
        $result = json_decode($response->getBody());

        if ($response->getStatusCode() != 200) {
            $error_msg = $response->getReasonPhrase() . ' : ' . $response->getBody();
            $logger = $this->_logger;
            $context = ['source' => 'Montapacking Checkout'];
            $logger->critical($error_msg . " (" . $url . ")", $context);
            $result = null;
        }

        if ($result == null) {

            sleep(1);
            $response = $client->get($method . '?' . $request);

            if ($response->getStatusCode() != 200) {
                $error_msg = $response->getReasonPhrase() . ' : ' . $response->getBody();
                $logger = $this->_logger;
                $context = ['source' => 'Montapacking Checkout'];
                $logger->critical($error_msg . " (" . $url . ")", $context);
                $result = null;
            }
        }

        $url = "https://api.montapacking.nl/rest/v5/" . $method . $request;

        if (null !== $this->_logger && null === $result) {
            $logger = $this->_logger;
            $context = ['source' => 'Monta Checkout'];
            $logger->critical("Webshop was unable to connect to Monta REST api. Please check your username and password. Otherwise please contact Montapacking (" . $url . ")", $context); //phpcs:ignore
        } elseif (null !== $this->_logger) {
            $logger = $this->_logger;
            $context = ['source' => 'Monta Checkout'];
            $logger->notice("Connection logged (" . $url . ")", $context);
        }

//        if ($this->_carrierConfig->getLogErrors()) {
//
//            if (null !== $this->_logger && isset($result->Warnings)) {
//
//                foreach ($result->Warnings as $warning) {
//
//                    $logger = $this->_logger;
//                    $context = ['source' => 'Montapacking Checkout'];
//
//                    if (null !== $warning->ShipperCode) {
//                        $logger->notice($warning->ShipperCode . " - " . $warning->Message, $context);
//                    } else {
//                        $logger->notice($warning->Message, $context);
//                    }
//
//                }
//            }
//
//            if (null !== $this->_logger && isset($result->Notices)) {
//
//                foreach ($result->Notices as $notice) {
//                    $logger = $this->_logger;
//                    $context = ['source' => 'Montapacking Checkout'];
//
//                    if (null !== $notice->ShipperCode) {
//                        $logger->notice($notice->ShipperCode . " - " . $notice->Message, $context);
//                    } else {
//                        $logger->notice($notice->Message, $context);
//                    }
//
//                }
//            }
//
//            if (null !== $this->_logger && isset($result->ImpossibleShipperOptions)) {
//
//                foreach ($result->ImpossibleShipperOptions as $impossibleoption) {
//                    foreach ($impossibleoption->Reasons as $reason) {
//
//                        $logger = $this->_logger;
//                        $context = ['source' => 'Montapacking Checkout'];
//                        $logger->notice($impossibleoption->ShipperCode . " - " . $reason->Code . " | " . $reason->Reason, $context); //phpcs:ignore
//                    }
//                }
//
//            }
//
//        }

        return $result;
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
}
