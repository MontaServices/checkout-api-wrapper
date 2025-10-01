<?php

namespace Monta\CheckoutApiWrapper\Objects;

use Monta\CheckoutApiWrapper\Traits\SystemInfo;

class Settings
{
    use SystemInfo;

    /** Construct Settings object.
     *
     * @param string $origin
     * @param string $user
     * @param string $password
     * @param bool $pickupPointsEnabled
     * @param int $maxPickupPoints
     * @param string $googleKey
     * @param float $defaultCosts
     * @param string|null $webshopLanguage
     * @param string $currency
     * @param bool $excludeShippingDiscount
     * @param bool $showZeroCostsAsFree
     * @param bool $hideDHLPackstations
     * @deprecated - Use the factory method instead
     */
    public function __construct(
        protected string $origin,
        protected string $user,
        #[\SensitiveParameter]
        protected string $password,
        protected bool $pickupPointsEnabled = false,
        protected int $maxPickupPoints = 4,
        #[\SensitiveParameter]
        protected string $googleKey = "",
        protected float $defaultCosts = 0,
        protected ?string $webshopLanguage = 'nl-NL',
        protected string $currency = '€',
        protected bool $excludeShippingDiscount = false,
        protected bool $showZeroCostsAsFree = false,
        protected bool $hideDHLPackstations = false,
    )
    {
    }

    /**
     * @return string
     */
    public function getOrigin(): string
    {
        return $this->origin;
    }

    /**
     * @param string $origin
     */
    public function setOrigin(string $origin): void
    {
        $this->origin = $origin;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser(string $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(#[\SensitiveParameter] string $password): void
    {
        $this->password = htmlspecialchars_decode($password);
    }

    /**
     * @return bool
     */
    public function getIsPickupPointsEnabled(): bool
    {
        return $this->pickupPointsEnabled;
    }

    /**
     * @param bool $pickupPointsEnabled
     */
    public function setPickupPointsEnabled(bool $pickupPointsEnabled): void
    {
        $this->pickupPointsEnabled = $pickupPointsEnabled;
    }

    /**
     * @return int
     */
    public function getMaxPickupPoints(): int
    {
        return $this->maxPickupPoints;
    }

    /**
     * @param int $maxPickupPoints
     */
    public function setMaxPickupPoints(int $maxPickupPoints): void
    {
        $this->maxPickupPoints = $maxPickupPoints;
    }

    /**
     * @return string
     */
    public function getGoogleKey(): string
    {
        return $this->googleKey;
    }

    /**
     * @param string $googleKey
     */
    public function setGoogleKey(#[\SensitiveParameter] string $googleKey): void
    {
        $this->googleKey = $googleKey;
    }

    /**
     * @return float
     */
    public function getDefaultCosts(): float
    {
        return $this->defaultCosts;
    }

    /**
     * @param float $defaultCosts
     */
    public function setDefaultCosts(float $defaultCosts): void
    {
        $this->defaultCosts = $defaultCosts;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getWebshopLanguage(): string
    {
        return $this->webshopLanguage;
    }

    /**
     * @param string $webshopLanguage
     */
    public function setWebshopLanguage(string $webshopLanguage): void
    {
        $this->webshopLanguage = $webshopLanguage;
    }

    /**
     * @return bool
     */
    public function getExcludeShippingDiscount(): bool
    {
        return $this->excludeShippingDiscount;
    }

    /**
     * @param bool $excludeShippingDiscount
     */
    public function setExcludeShippingDiscount(bool $excludeShippingDiscount): void
    {
        $this->excludeShippingDiscount = $excludeShippingDiscount;
    }

    public function getShowZeroCostsAsFree(): bool
    {
        return $this->showZeroCostsAsFree;
    }

    public function setShowZeroCostsAsFree(bool $showZeroCostsAsFree): void
    {
        $this->showZeroCostsAsFree = $showZeroCostsAsFree;
    }

    public function getHideDHLPackstations(): bool
    {
        return $this->hideDHLPackstations;
    }

    public function setHideDHLPackstations(bool $hideDHLPackstations): void
    {
        $this->hideDHLPackstations = $hideDHLPackstations;
    }
}