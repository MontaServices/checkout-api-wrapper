<?php

namespace Monta\CheckoutApiWrapper\Abstract;

interface SystemInfoInterface
{
    /** @var string - Identifies the system information in the body */
    public const SYSTEM_INFO_NAME = 'systemInfo';

    /* Constants to determine the system info body. Reference these from client modules. */
    public const CORE_SOFTWARE = 'coreSoftware';

    public const CORE_VERSION = 'coreVersion';

    public const CHECKOUT_API_WRAPPER_VERSION = 'checkoutApiWrapperVersion';

    public const MODULE_NAME = 'moduleName';

    public const MODULE_VERSION = 'moduleVersion';

    public const PHP_VERSION = 'phpVersion';

    public const OPERATING_SYSTEM = 'operatingSystem';
}
