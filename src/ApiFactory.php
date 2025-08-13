<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 12/08/2025 15:54
 */
namespace Monta\CheckoutApiWrapper;

use Monta\CheckoutApiWrapper\MontapackingShipping as Api;
use Monta\CheckoutApiWrapper\Objects\Settings;

class ApiFactory
{
    /** Factory method for getting an API instance
     *
     * @param Settings $settings - TODO avoid requiring Settings object in the future
     * @param array $systemInfo
     * @return MontaPackingShipping
     */
    public function create(Settings $settings, array $systemInfo = []): Api
    {
        if ($systemInfo) {
            $settings->setSystemInfo($systemInfo);
        }
        return new Api(settings: $settings, language: $settings->getWebshopLanguage());
    }

    /**
     * @param array $settings
     * @param array $systemInfo
     * @return MontapackingShipping
     */
    public function createFromSettings(array $settings = [], array $systemInfo = []): Api
    {
        return $this->create(new Settings(...$settings), $systemInfo);
    }
}
