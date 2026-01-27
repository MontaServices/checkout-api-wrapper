<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 12/1/2025 11:13
 */
namespace Monta\CheckoutApiWrapper\Traits;

use Monta\CheckoutApiWrapper\Objects\Option;
use Monta\CheckoutApiWrapper\Objects\PickupPoint;
use Monta\CheckoutApiWrapper\Objects\ShippingOption;
use Monta\CheckoutApiWrapper\Objects\TimeFrame;
use Monta\CheckoutApiWrapper\Service\Session;

trait CachedOptions
{
    /** @var string - Save options in cache under this path */
    protected const string CACHE_PREFIX = 'shipping-options';

    /** Retrieve the shipping options for this session, cached earlier
     *
     * @param string|null $item
     * @return array
     */
    protected function getCachedOptions(?string $item = null): array
    {
        $results = Session::get(self::CACHE_PREFIX);
        if ($results) {
            // Get specific item from array
            if ($item) {
                return $results[$item] ?? [];
            } else {
                return $results;
            }
        }
        return [];
    }

    /** Find the matching cached option by code
     *
     * @param Option $selected
     * @return Option|ShippingOption|PickupPoint|null
     */
    protected function retrieveOption(Option $selected): Option|ShippingOption|PickupPoint|null
    {
        $cachedOptions = $this->getCachedOptions();
        // If cached options exist and if option has Code to match on
        if ($cachedOptions && $this->getCode()) {
            switch ($selected->getShippingType()) {
                case self::DELIVERY_TYPE:
                    foreach ($cachedOptions[ShippingOption::SHIPPING_OPTIONS_KEY] as $cachedTimeframe) {
                        /** @var TimeFrame $cachedTimeframe */
                        foreach ($cachedTimeframe->options as $cachedOption) {
                            if ($cachedOption->getCode() == $this->getCode()) {
                                /** @var ShippingOption $cachedOption - This is the option we are looking for */

                                /** Copy shipper options manually */
                                $cachedOptionSelectedShipperOptions = [];
                                foreach ($this->getSelectedShipperOptions() as $shipperOption) {
                                    // Find cached shipper option by selected 'code', add that to array
                                    $cachedOptionSelectedShipperOptions[] = $cachedOption->getShipperOptionByCode($shipperOption['code']);
                                }
                                // Update selected Shipper options on the cached Option to match price calculation
                                $cachedOption->updateOriginalData(['selectedShipperOptions' => $cachedOptionSelectedShipperOptions]);
                                return $cachedOption;
                            }
                        }
                    }
                    break;
                case self::PICKUP_TYPE:
                    foreach ($cachedOptions[PickupPoint::PICKUP_OPTIONS_KEY] as $cachedPickupPoint) {
                        /** @var PickupPoint $cachedPickupPoint */
                        if ($cachedPickupPoint->getCode() == $this->getCode()) {
                            return $cachedPickupPoint;
                        }
                    }
                    break;
            }
        }
        return null;
    }

    /** Save shippingoptions result from API to session cache
     *
     * @param array $results
     * @return void
     */
    protected function saveResults(array $results): void
    {
        // Save the entire shippingOptions in session
        Session::save(self::CACHE_PREFIX, $results);
    }
}