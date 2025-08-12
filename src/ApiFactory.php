<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 12/08/2025 15:54
 */
namespace Monta\CheckoutApiWrapper;

use Monta\CheckoutApiWrapper\Objects\Settings;

class ApiFactory
{
    /** Factory method for getting an API instance
     *
     * @param Settings $settings - TODO avoid requiring Settings object
     * @param array $systemInfo
     * @return MontaPackingShipping
     */
    public function create(Settings $settings, array $systemInfo = []): MontaPackingShipping
    {
        $settings->setSystemInfo($systemInfo);
        return new MontaPackingShipping(settings: $settings);
    }
}
