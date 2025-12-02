<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 12/1/2025 11:13
 */
namespace Monta\CheckoutApiWrapper\Traits;

use Monta\CheckoutApiWrapper\Objects\Option;
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
    protected function getCachedOptions(string $item = null): array
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

    /** Find the matching cached Option
     *
     * @param Option $selected
     * @return void
     * @throws \Exception
     */

    /** Find selected option in cache by code
     * @param Option $selected
     * @return Option|null
     */
    public function retrieveOption(Option $selected): Option|null
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
                                $cachedOptionsShipperOptions = [];
                                foreach ($this->getShipperOptions() as $shipperOption) {
                                    // Find cached shipper option by selected 'code', add that to array
                                    $cachedOptionsShipperOptions[] = $cachedOption->getShipperOptionByCode($shipperOption['code']);
                                }
                                $cachedOption->setShipperOptions($cachedOptionsShipperOptions);

                                return $cachedOption;
                            }
                        }
                    }
                    break;
                case self::PICKUP_TYPE:
                    // TODO implement pickup type
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