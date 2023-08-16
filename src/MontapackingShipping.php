<?php

namespace Monta\CheckoutApiWrapper;

use GuzzleHttp\Exception\GuzzleException;
use Monta\CheckoutApiWrapper\Objects\Address as MontaCheckout_Address;
use Monta\CheckoutApiWrapper\Objects\Order as MontaCheckout_Order;
use Monta\CheckoutApiWrapper\Objects\Product as MontaCheckout_Product;
use Monta\CheckoutApiWrapper\Objects\Settings;
use Monta\CheckoutApiWrapper\Objects\ShippingOption as MontaCheckout_ShippingOption;
use Monta\CheckoutApiWrapper\Objects\TimeFrame as MontaCheckout_TimeFrame;
use Monta\CheckoutApiWrapper\Objects\PickupPoint as MontaCheckout_PickupPoint;
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
     * @param     $sku
     * @param     $quantity
     * @param int $lengthMm
     * @param int $widthMm
     * @param int $heightMm
     * @param int $weightGrammes
     */
    public function addProduct($sku, $quantity, int $lengthMm = 0, int $widthMm = 0, int $heightMm = 0, int $weightGrammes = 0) : void
    {
        $this->_products['products'][] = new MontaCheckout_Product($sku, $lengthMm, $widthMm, $heightMm, $weightGrammes, $quantity);
    }

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
        $standardShipper = null;

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

            if (isset($result->standard_shipper)) {
                $standardShipper = new MontaCheckout_ShippingOption(
                    $result->standard_shipper->shipper,
                    $result->standard_shipper->code,
                    $result->standard_shipper->displayNameShort,
                    $result->standard_shipper->displayName,
                    $result->standard_shipper->deliveryType,
                    $result->standard_shipper->shippingType,
                    $result->standard_shipper->price,
                    $result->standard_shipper->priceFormatted,
                    $result->standard_shipper->discountPercentage,
                    $result->standard_shipper->isPreferred,
                    $result->standard_shipper->isSustainable,
                    $result->standard_shipper->deliveryOptions,
                    $result->standard_shipper->optionCodes
                );
            }
        }

        return ['DeliveryOptions' => $timeframes, 'PickupOptions' => $pickups, 'StandardShipper' => $standardShipper, 'CustomerLocation' => $this->address];
    }

    /**
     * @param      $method
     * @param null $send
     *
     * @return mixed
     * @throws GuzzleException
     */
    public function call($method, $send = null): mixed
    {
        $request = '?';
        if ($send != null) {

            $requestBody = [];

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
                'Authorization' => 'Basic ' . base64_encode($this->getSettings()->getUser() . ":" . $this->getSettings()->getPassword())
            ]
        ]);

        $method = strtolower($method);
        $jsonRequest = [
            'userName' => $this->getSettings()->getUser(),
            'password' => $this->getSettings()->getPassword(),
            'channel' => $this->getSettings()->getOrigin(),
            'googleAPIKey' => $this->getSettings()->getGoogleKey(),
            'usePickupPoints' => $this->getSettings()->getIsPickupPointsEnabled(),
            'useShipperOptions' => true,
            'numberOfPickupPoints' => $this->getSettings()->getMaxPickupPoints(),
            'defaultCosts' => $this->getSettings()->getDefaultCosts(),
            'streetaddress' => $this->address->street . ' ' . $this->address->housenumber . $this->address->housenumberaddition,
            'city' => $this->address->city,
            'postalcode' => $this->address->postalcode,
            'countrycode' => $this->address->countrycode,
            'products' => $this->_products['products'],
        ];

        if($this->getOnstock()) {
            $jsonRequest['productsOnStock'] = true;
        }

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
