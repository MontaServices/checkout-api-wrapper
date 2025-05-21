<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 5/14/2025 14:08
 */

namespace Monta\CheckoutApiWrapper\Traits;

trait SystemInfo
{
    /** @var array - Initialize with some global defaults */
    protected array $systemInfo = [
        self::PHP_VERSION => PHP_VERSION,
        self::OPERATING_SYSTEM => PHP_OS
    ];

    /**
     * @param array $systemInfo
     * @return void
     */
    public function setSystemInfo(array $systemInfo): void
    {
        // merge arrays (prioritize parameter on duplicate key)
        $this->systemInfo = array_merge($this->systemInfo, $systemInfo);
    }

    /**
     * @return array
     */
    public function getSystemInfo(): array
    {
        return $this->systemInfo;
    }
}
