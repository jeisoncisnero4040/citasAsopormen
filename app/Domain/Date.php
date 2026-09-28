<?php

namespace App\Domain;
use Carbon\Carbon;
class Date
{
    public function __construct(
        private Carbon $date,
    ){}
    public static function create(string $date): self
    {
        return new self(Carbon::parse($date));
    }
    public static function now(): self
    {
        return new self(Carbon::now());
    }
    public static function createFromTimestampMs(int $timestamp): self
    {
        return new self(Carbon::createFromTimestampMs($timestamp));
    }
    public function getDate(): string
    {
        return $this->date->format('Y-m-d H:i:s');
    }
    public function getShort(): string
    {
        return $this->date->format('Ydm');
    }
    public function getYear(): string
    {
        return $this->date->format('Y');
    }
    public function getMonth(): string
    {
        return $this->date->format('m');
    }
    public function getDay(): string
    {
        return $this->date->format('d');
    }
    public function getDayOfWeek(): string
    {
        return $this->date->format('l');
    }
    public function getDayOfYear(): string
    {
        return $this->date->format('z');
    }
    public function getWeekOfYear(): string
    {
        return $this->date->format('W');
    }
    public function getTimestamp(): string
    {
        return $this->date->format('U');
    }
    public function getDayOfMonth(): string
    {
        return $this->date->format('j');
    }
    public function getFullDate(): string
    {
        return $this->date->format('Y-d-m H:i:s');
    }
}