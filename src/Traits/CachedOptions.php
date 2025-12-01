<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 12/1/2025 11:13
 */
namespace Monta\CheckoutApiWrapper\Traits;

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
    public function getCachedOptions(string $item = null): array
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