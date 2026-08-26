<?php

namespace App\Domain;
use App\Domain\KeyAuthCup;
use App\utils\DateManager;
use Carbon\Carbon;
class ReplicateDispInfo
{
    public function __construct(
        private KeyAuthCup $keyAuthCup,
        private int $cantidad,
        private string $f_vence,
        private int $total_programadas,
        private int $disponibles
    )
    {
    }
    public static function fromArray(array $data): self
    {
        return new self(
            KeyAuthCup::fromKey($data['autorizacion']),
            $data['cantidad'],
            $data['f_vence'],
            $data['total_programadas'],
            $data['disponibles']
        );
    }
    public static function fromStdClass(\stdClass $data): self
    {
        return self::fromArray((array)$data);
    }
    public function decrementDisponibility(int $amount = 1): void{
        $this->disponibles -= $amount;
        if ($this->disponibles < 0) {
            $this->disponibles = 0;
        }
    }
    public function validateDisponibility(Carbon $dateNewAppoiment): bool
    {

            if (DateManager::isHoliday($dateNewAppoiment)) {
                return false;
            } 
            if ($this->disponibles <= 0) {
                return false;
            }
            $dateExpireAuth = Carbon::parse($this->f_vence)->addDay();
            if ($dateNewAppoiment->greaterThan($dateExpireAuth)) {
                return false;
            }
            return true;
    }
    public function getKeyAuthCup(): KeyAuthCup
    {
        return $this->keyAuthCup;
    }
    
}

