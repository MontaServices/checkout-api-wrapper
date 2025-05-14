<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 5/14/2025 14:08
 */
namespace Monta\CheckoutApiWrapper\Traits;

trait SystemInfo
{
    /** @var string - Identifies the system information in the body */
    public const SYSTEM_INFO_NAME = 'systemInformation';

    /** @var array */
    protected array $systemInfo = [];

    public function setSystemInfo(array $systemInfo): void
    {
        $this->systemInfo = $systemInfo;
    }

    public function getSystemInfo(): array
    {
        return $this->systemInfo;
    }
}
