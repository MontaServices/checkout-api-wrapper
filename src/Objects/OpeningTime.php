<?php

namespace Monta\CheckoutApiWrapper\Objects;

class OpeningTime
{
    /**
     * @param string $day
     * @param string $from
     * @param string $to
     */
    public function __construct(public string $day, public string $from, public string $to)
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
