<?php

namespace App\Models;

use App\Dtos\UpdateAuthsDto;
use App\Dtos\ExternalProcedureDto;

class Auth {
    private string $n_autoriza;

    /**Tiempo es el codigo de procedimiento */
    private string $tiempo;
    private string $procedim;
    private int $cantidad;
    private bool $cerrado;
    private string $especialidad;
    private string $f_vence;
    private string $f_inicial;
    private string $observa;
    private string $convenio;
    private string $cod_entidad;
    private int $total_programadas;
    private int $disponibles;
    private string $entidad;
    private bool $nueva;
    private array $especialidades = [];
    private bool $isInvoiced;
    private string $codeClient;
    private string $n_convenio;
    private int $days;
    private string $consecutive;
    private string $fechaAdd;
    private ?string $remitente;
    private ?string $nameRemitente;
    private ?int $id;

    public function __construct(
        string $n_autoriza,
        string $tiempo,
        string $procedim,
        int $cantidad,
        bool $cerrado,
        string $especialidad,
        string $f_vence,
        string $f_inicial,
        string $observa,
        string $convenio,
        string $cod_entidad,
        int $total_programadas,
        int $disponibles,
        string $entidad,
        bool $nueva,
        bool $isInvoiced,
        string $codeClient,
        string $n_convenio,
        int $days,
        string $consecutive,
        string $fechaAdd,
        ?string $remitente ,
        ?string $nameRemitente ,
        ?int $id = null
    ){
        $this->n_autoriza = $n_autoriza;
        $this->tiempo = $tiempo;
        $this->procedim = $procedim;
        $this->cantidad = $cantidad;
        $this->cerrado = $cerrado;
        $this->especialidad = $especialidad;
        $this->f_vence = $f_vence;
        $this->f_inicial = $f_inicial;
        $this->observa = $observa;
        $this->convenio = $convenio;
        $this->cod_entidad = $cod_entidad;
        $this->total_programadas = $total_programadas;
        $this->disponibles = $disponibles;
        $this->entidad = $entidad;
        $this->nueva = $nueva;
        $this->isInvoiced= $isInvoiced;
        $this->codeClient=$codeClient;
        $this->n_convenio = $n_convenio;
        $this->days = $days;
        $this->consecutive = $consecutive;
        $this->remitente = $remitente;
        $this->fechaAdd = $fechaAdd;
        $this->nameRemitente = $nameRemitente;
        $this->id = $id;
    }

    public static function fromArray(array $array): self
    {
        return new self(
            $array['n_autoriza'],
            $array['tiempo'],
            $array['procedim'],
            (int)$array['cantidad'],
            (bool)$array['cerrado'],
            $array['especialidad'] ?? 'No Encontrada',
            $array['f_vence'],
            $array['f_inicial'],
            $array['observa'] ?? '',
            $array['convenio'],
            $array['cod_entidad'],
            (int)$array['total_programadas'],
            (int)$array['disponibles'],
            $array['entidad'],
            (bool)$array['nueva'],
            (bool)$array['facturada'],
            $array['historia'],
            $array['n_convenio'],
            (int)$array['dias'],
            $array['nro'],
            $array['fecha'],
            $array['cod_remitente'],
            $array['nombre_remitente'],
            isset($array['id']) ? (int)$array['id'] : null
        );
    }

    public function toArray(): array
    {
        return [
            'n_autoriza' => $this->n_autoriza,
            'tiempo' => $this->tiempo,
            'procedim' => $this->procedim,
            'cantidad' => $this->cantidad,
            'cerrado' => $this->cerrado,
            'especialidad' => $this->especialidad,
            'f_vence' => $this->f_vence,
            'f_inicial' => $this->f_inicial,
            'observa' => $this->observa,
            'convenio' => $this->convenio,
            'cod_entidad' => $this->cod_entidad,
            'total_programadas' => $this->total_programadas,
            'disponibles' => $this->disponibles,
            'entidad' => $this->entidad,
            'nueva' => $this->nueva,
            'especialidades'=>$this->especialidades,
            'facturada'=>$this->isInvoiced,
            'nombre_convenio'=>$this->n_convenio,
            'dias'=>$this->days,
            'nro'=>$this->consecutive,
            'cod_remitente'=>$this->remitente,
            'nombre_remitente'=>$this->nameRemitente,
            'fecha'=>$this->fechaAdd,
            'id'=>$this->id
        ];
    }
    public function getAutoriza():string{
        return $this->n_autoriza;
    }
    public function getSpecialty():string{
        return $this->especialidad;
    }
    public function setSpecialties(array $new):void{
        $this->especialidades = $new;
    }
    public function getCodeClient():string{
        return $this->codeClient;
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getAuthCode(): string
    {
        return $this->n_autoriza;
    }   
    public function getClientCode(): string
    {
        return $this->codeClient;
    }
    public function getEntityCode(): string
    {
        return $this->cod_entidad;
    }
    public function isUpdatableAmmount(): bool
    {
        return $this->cantidad > 0 && !$this->cerrado && !$this->isInvoiced && !$this->isExpired();
    }
    public function isExpired(): bool
    {
        $currentDate = new \DateTime();
        $currentDate->setTime(0, 0, 0);
        $expirationDate = new \DateTime($this->f_vence);
        $expirationDate->setTime(0, 0, 0);
        return $currentDate > $expirationDate;
    }
    public function update(UpdateAuthsDto $dto): void
    {
        $this->f_inicial = $dto->getFrom();
        $this->f_vence = $dto->getTo();
        $this->days = $dto->getNumberDays();
        $this->codeClient = $dto->getClientCode();
        $this->cantidad = $this->resolveAmmountToUpdate($dto->getCups());
    }

    /**
     * @param ExternalProcedureDto[] $cupsDto
     */
    private function resolveAmmountToUpdate(array $cupsDto): int
    {
        if(!$this->isUpdatableAmmount()) {
            return $this->cantidad;
        }
        $found = array_values(array_filter($cupsDto, fn($cup) => $cup->getCode() === $this->tiempo))[0] ?? null;
        return $found ? $found->getQuantity() : $this->cantidad;
    }
    public function deleteLog(array $idsApposAsossiate, UserRequesting $user): string
    {
       return "el usuario {$user->getUsername()} ha eliminado la autorización con numero de autorizacion {$this->n_autoriza}
         y los siguientes las citas asociadas con ids : ". implode(", ", $idsApposAsossiate) . " el dia ". date("Y-m-d H:i:s");
    }

    

    
}