<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 12/08/2025 15:54
 */
namespace Monta\CheckoutApiWrapper\Service;

use Monta\CheckoutApiWrapper\MontapackingShipping as Api;
use Monta\CheckoutApiWrapper\Objects\Settings;

class ApiFactory
{
    /** Factory method for getting an API instance
     *
     * @param Settings $settings - TODO avoid requiring Settings object in the future
     * @param array $systemInfo
     * @return Api
     */
    public function create(Settings $settings, array $systemInfo = []): Api
    {
        if ($systemInfo) {
            $settings->setSystemInfo($systemInfo);
        }
        return new Api(settings: $settings, language: $settings->getWebshopLanguage());
    }

    /** Factory method for getting an API instance from array
     *
     * @param array $settings - Array with the exact named parameters of the Settings object
     * @param array $systemInfo - Pass along system info
     * @return Api
     * @deprecated - Splatting array into constructor parameters is bad design
     */
    public function createFromSettings(array $settings = [], array $systemInfo = []): Api
    {
        // Splat array into constructor parameters; Clever code but not future-proof
        return $this->create(new Settings(...$settings), $systemInfo);
    }
}