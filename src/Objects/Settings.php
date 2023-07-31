<?php

namespace Monta\MontaProcessing\Objects;

class Settings
{
    public function __construct(string $origin, string $user, string $password, bool $pickupPointsEnabled, int $maxPickupPoints, string $googleKey, float $defaultCosts)
    {
        $this->setOrigin($origin);
        $this->setUser($user);
        $this->setPassword($password);
        $this->setPickupPointsEnabled($pickupPointsEnabled);
        $this->setMaxPickupPoints($maxPickupPoints);
        $this->setGoogleKey($googleKey);
        $this->setDefaultCosts($defaultCosts);
    }

    /**
     * @var string
     */
    private string $origin;

    /**
     * @var string
     */
    private string $user;

    /**
     * @var string
     */
    private string $password;

    /**
     * @var bool
     */
    private bool $pickupPointsEnabled = false;

    /**
     * @var int
     */
    private int $maxPickupPoints = 4;

    /**
     * @var string
     */
    private string $googleKey;

    /**
     * @var float
     */
    private float $defaultCosts;

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
    public function setPassword(string $password): void
    {
        $this->password = $password;
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
    public function setGoogleKey(string $googleKey): void
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
}