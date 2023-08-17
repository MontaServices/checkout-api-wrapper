<?php

namespace Monta\CheckoutApiWrapper\Objects;

class OpeningTime
{
    /**
     * @var string
     */
    public string $day;

    /**
     * @var string
     */
    public string $from;

    /**
     * @var string
     */
    public string $to;

    /**
     * @param $day
     * @param $from
     * @param $to
     */
    public function __construct($day, $from, $to)
    {
        $this->setDay($day);
        $this->setFrom($from);
        $this->setTo($to);
    }

    /**
     * @return string
     */
    public function getDay(): string
    {
        return $this->day;
    }

    /**
     * @param string $day
     */
    public function setDay(string $day): void
    {
        $this->day = $day;
    }

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @param string $from
     */
    public function setFrom(string $from): void
    {
        $this->from = $from;
    }

    /**
     * @return string
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * @param string $to
     */
    public function setTo(string $to): void
    {
        $this->to = $to;
    }
}
