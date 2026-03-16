<?php

namespace App\Dtos;

use App\Interfaces\Persistable;
use App\Requests\CitasRequests;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CreateCitasDto implements Persistable
{
    private string $userCedula;
    private string $user;

    private string $profesional;
    private string $cedula;

    private string $historia;
    private string $client;

    private string $auth;
    private string $cupCode;
    private string $procedure;
    private Carbon $expiratioDate;
    private string $epsCode;
    private string $covenantCode;

    private string $procedipro;
    private bool $whatsappRemember;
    private int $duration;

    private ?int $observaId;
    private ?int $copago;
    private Carbon $startDate;
    private array $daysWeek;

    private int $citasTotal;


    private ?string $date;
    private ?string $hour;
    private ?string $direction;
    private ?string $sede;
    private ?string $dateCreation;
    private ?string $familyId = null;

    public function __construct(array $data)
    {
        $this->validateRequest($data);

        $this->userCedula = $data['ced_usu'];
        $this->user       = trim($data['registro']);

        $this->profesional = trim($data['profesional']);
        $this->cedula      = $data['cedProf'];

        $this->historia  = $data['nro_hist'];
        $this->client    = trim($data['clientName']);

        $this->auth      = $data['n_autoriza'];
        $this->cupCode   = $data['tiempo'];
        $this->procedure = $data['procedim'];
        $this->expiratioDate = Carbon::parse($data['fecha_vencimiento']);
        $this->epsCode       = $data['codent'];
        $this->covenantCode  = $data['codent2'];

        $this->procedipro       = $data['procedipro'];
        $this->whatsappRemember = (bool) $data['recordatorio_wsp'];
        $this->duration         = (int) $data['duration_session'];

        $this->observaId = (int) $data['regobserva']??null;
        $this->copago    = (int) $data['copago']??null;
        $this->startDate = Carbon::parse($data['start_date']);
        $this->daysWeek  = $data['week_days']??[];
    


        $this->citasTotal = (int) $data['num_sessions_total']??0;


    }

    public static function fromRequest(Request $request): self
    {
        return new self($request->all());
    }
    public function getProcedipro():string{
        return $this->procedipro;
    }
    public function getStartDate():Carbon{
        return $this->startDate;
    }
    public function getDaysWeek():array{
        return $this->daysWeek;
    }
    public function getDaysWeekNames():array{
        return array_keys($this->daysWeek);
    }
    public function getAutoriz():string{
        return $this->auth;
    }
    public function getCodCup():string{
        return $this->cupCode;
    }
    public function getDuractionAppo():string{
        return $this->duration;
    }
    public function getHistCode():string{
        return $this->historia;
    }
    public function getTotalCitas():int{
        return $this->citasTotal;
    }
    public function isWhatsappNotificable():bool{
        return $this->whatsappRemember;
    }
    public function getObservaId():?int{
        return $this->observaId;
    }
    public function getExpireDateAuth():Carbon{
        return $this->expiratioDate;
    }
    public function getCedula():string{
        return $this->cedula;
    }
    public function getUser():string{
        return $this->user;
    }
    public function getClient():string{
        return $this->client;
    }
    public function getProfesional():string{
        return $this->profesional;
    }
    public function getUserCedula():string{
        return $this->userCedula;
    }


    public function isSingleAppo(){
        return empty($this->daysWeek) && $this->citasTotal == 0 ;
    }

    public function setDaysWeek(array $new):void{
        $this->daysWeek=$new;
    }
    public function setTotalCitas(int $total):void{
        $this->citasTotal=$total;
    }

    public function setDate(string $newDate):void{
        $this->date=$newDate;
    }
    public function setHour(string $newHour):void{
        $this->hour=$newHour;
    }
    public function setDateCreation($newDate):void{
        $this->dateCreation=$newDate;
    }
    public function setDirection(string $new):void{
        $this->direction = $new;
    }
    public function setHeadQuarter(string $new):void{
        $this->sede=$new;
    }
    public function setFamilyId(string $id):void{
        $this->familyId=$id;
    }

    public function copy():self{
        return clone $this;
    }
    public function toPersistenceArray(): array
    {
        return [
            'nro_hist'              => $this->historia,
            'cedprof'               => $this->cedula,
            'ced_usu'               => $this->userCedula,
            'registro'              =>$this->user,
            'sede'                  =>$this->sede,
            'observaciones_mc'      =>$this->observaId,
            'codent'                =>$this->epsCode,
            'codent2'               =>$this->covenantCode,
            'tiempo'                =>$this->cupCode,
            'direccion_cita'        =>$this->direction,
            'procedim'              =>$this->procedure,
            'procedipro'            =>$this->procedipro,
            'autoriz'               =>$this->auth,
            'sdt_fecha'             =>$this->date,
            'hora'                  =>$this->hour,
            'sdt_fec_hora'          =>$this->dateCreation,
            'recordatorio_wsp'      =>$this->whatsappRemember?'1':'0',
            'copago'                =>$this->copago??'No Aplica',
            'grupo_citas'           =>$this->familyId

        ];
    }

    private function validateRequest(array $data): void
    {
        CitasRequests::validateCreateApposRequest(data:$data);
    }
}
