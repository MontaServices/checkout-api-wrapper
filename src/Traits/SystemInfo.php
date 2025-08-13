<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 5/14/2025 14:08
 */

namespace Monta\CheckoutApiWrapper\Traits;

trait SystemInfo
{
    /** @var string - Identifies the system information in the body */
    public const string SYSTEM_INFO_NAME = 'systemInfo';

    /* Constants to determine the system info body. Reference these from client modules. */
    public const string CORE_SOFTWARE = 'coreSoftware';

    public const string CORE_VERSION = 'coreVersion';

    public const string CHECKOUT_API_WRAPPER_VERSION = 'checkoutApiWrapperVersion';

    public const string MODULE_NAME = 'moduleName';

    public const string MODULE_VERSION = 'moduleVersion';

    public const string PHP_VERSION = 'phpVersion';

    public const string OPERATING_SYSTEM = 'operatingSystem';

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
