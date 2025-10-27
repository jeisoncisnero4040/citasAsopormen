<?php

namespace App\Dtos;

use App\Requests\AppoimentsRequests;
use Illuminate\Http\Request;


class BasicEvoDto
{
    protected string|null $companion;
    protected string $place;
    protected string $kinred;

    protected string $entryRoute;
    protected string $externalCause;
    protected string $purpose;
    protected string $entry;
    protected string $typeDiagnosisEntry;
    protected string|null $out;
    protected string|null $typeDiagnosisOutPut;

    protected bool $firstTime;
    protected string|null $target;
    protected string|null $descript;
    protected string|null $result;
    protected array $idsToEvo;

    protected string $cedula;
    protected string $profesional;
    protected string $dateAppo;
    protected string $endAppo;
    protected int $numSessions;

    protected string|null $userHistory;
    protected string|null $epsCode;
    protected string|null $procediproCode;
    protected string|null $central;
    protected string|null $authCode;
    protected string|null $secuencie;

    protected int|null  $tipoEvo;
    protected string|null $tipoAppo;

    protected bool|null $isPelvic;
    protected bool|null $isHidric;
    protected bool|null $isFisio;

    protected string|null $covenatCode;
    protected string|null $procedim;
    protected string|null $registro;
    protected string|null $registroCed;
    protected string|null $registroDate;
    protected string|null $costCenter;
    protected float|null $value;
    protected string|null $tarife;
    protected string|null $client;
    protected string|null $clientCedula;
    protected string|null $procedimiento;
    protected string|null $specialty;
    protected string|null $now;

    protected string $prefijo;

    public function __construct(Request $request)
    {

        $this->validate($request->all());

        $this->companion = $request->input('companion')??null;
        $this->place = $request->input('place');
        $this->kinred = $request->input('kindred');
        $this->entryRoute = $request->input('entryRoute');
        $this->externalCause = $request->input('externalCause');
        $this->purpose=$request->input('purpose');
        $this->entry = $request->input('entry');
        $this->typeDiagnosisEntry = $request->input('typeDiagnosisEntry');
        $this->out = $request->input('out')??null;
        $this->typeDiagnosisOutPut = $request->input('typeDiagnosisOutPut')??null;
        $this->firstTime = $request->input('firstTime');
        $this->target = $request->input('target')??null;
        $this->descript = $request->input('descript')??null;
        $this->result = $request->input('result')??null;
        $this->idsToEvo = $request->input('idsToEvo', []);
        $this->profesional = $request->input('profesional');
        $this->cedula = $request->input('cedula') ?? '';
        $this->dateAppo = $request->input('date');
        $this->endAppo = $request->input('endHour');
        $this->secuencie= $request->input('secuencie');

        $this->numSessions = count($this->idsToEvo);


        $this->central             =null;
        $this->userHistory        = null;
        $this->epsCode            = null;
        $this->procediproCode     = null;
        $this->authCode           = null;
        $this->covenatCode         = null;
        $this->procedim            =null;
        $this->registro            =null;
        $this->registroCed         =null;
        $this->registroDate        =null;
        $this->costCenter          =null;
        $this->tarife              =null;
        $this->value               =null;
        $this->specialty            =null;
        $this->client              =null;
        $this->clientCedula         =null;
        $this->procedimiento        =null;



        $this->now                 =null;

        $this->tipoEvo             = null;
        $this->tipoAppo             =null;

        $this->isPelvic            = null;
        $this->isHidric            = null;
        $this->isFisio             = null;

        $this->prefijo             ='OR'; 
    }
    protected function validate(array $requestData):void{
        AppoimentsRequests::ValidateEvoAppoiments($requestData);
    }


    public function setUserHistory(string $userHistory): void
    {
        $this->userHistory = $userHistory;
     
    }

    public function setEpsCode(string $epsCode): void
    {
        $this->epsCode = $epsCode;

    }

    public function setProcedipro(string $procediproCode): void
    {
        $this->procediproCode = $procediproCode;
    }

    public function setCentral(string $central): void
    {
        $this->central = $central;
    }

    public function setAuthCode(?string $authCode): void
    {
        $this->authCode = $authCode;

    }

    public function setSecuencie(?string $secuencie): void
    {
        $this->secuencie = $secuencie;

    }

    public function setTipoEvo(string|int $tipo): void
    {
        $this->tipoEvo = (int)$tipo;

    }

    public function setIsPelvic(bool $isPelvic): void
    {
        $this->isPelvic = $isPelvic;

    }

    public function setIsHidric(bool $isHidric): void
    {
        $this->isHidric = $isHidric;

    }

    public function setIsFisio(bool $isFisio): void
    {
        $this->isFisio = $isFisio;

    }

    public function setCovenat(string $covenatCode):void{
        $this->covenatCode=$covenatCode;
    }
    public function setProcedim(string $procedim):void{
        $this->procedim=$procedim;
    }
    public function setRegistro(string $registro):void{
        $this->registro=$registro;
    }
    public function setRegistroDate(string $date):void{
        $this->registroDate=$date;
    }
    public function setRegistroCed(string $cedula):void{
        $this->registroCed=$cedula;
    }
    public function setCostCenter(string $costCenter):void{
        $this->costCenter=$costCenter;
    }
    public function setValue(float $unitValue):void{
        $this->value=$unitValue*$this->numSessions;
    }
    public function setTarife(string $tarife):void{
        $this->tarife=$tarife;
    }
    public function setClient(string $client):void{
        $this->client=$client;
    }
    public function setClientCedula(string $cedula):void{
        $this->clientCedula=$cedula;
    }
    public function setProcedimiento(string $procedimiento):void{
        $this->procedimiento=$procedimiento;
    }
    public function setSpecialty(string $especialty):void{
        $this->specialty=$especialty;
    }
    public function setNow(string $now):void{
        $this->now=$now;
    }
    public function setTipoAppo(string $tipo):void{
        $this->tipoAppo=$tipo;
    }
    public function getIdsToEvo():array{
        return $this->idsToEvo;
    }
    public function getCedula():string{
        return $this->cedula;
    }
    public function getAutoriz():string{
        return $this->authCode;
    }
    public function getHistory():string{
        return $this->userHistory;
    }
    public function getProfesional():string{
        return $this->profesional;
    }
    public function isFirstTime():bool{
        return $this->firstTime;
    }

    public function toArrayEvo(): array
    {
        return [
            'historia'              => $this->userHistory,
            'medico'                => $this->cedula,
            'sdt_fecha'             => $this->now,
            'usu_crea'              => $this->profesional,
            'text3'                 => $this->cedula,
            'text1'                 => $this->secuencie ?? '',
            'text2'                 => $this->profesional,
            'ver'                   => $this->tipoEvo== 2 ? '1' : '0',
            'tf1'                   => $this->tipoEvo==3 ? '1' : '0',
            'mh7'                   => $this->target,
            'mh8'                   => $this->descript,
            'mh9'                   => $this->result,
            'tipopantalla'          => $this->tipoEvo==1 ? 'fonoaudiologia' : '',
            'sdt_fecha_registro'    => $this->dateAppo,
            'sdt_fechahora'         => $this->now,
            'fisica'                => $this->isFisio ? '1' : '0',
            'hidrica'               => $this->isHidric ? '1' : '0',
            'pelvico'               => $this->isPelvic ? '1' : '0',
            'sdt_hculminacion_sesion'=> $this->dateAppo,
            'stt_hora_inicio'       => $this->endAppo,
            'num_sesiones'          => $this->numSessions ?? 0,
            'id_cita'               => isset($this->idsToEvo[0]) ? (int)$this->idsToEvo[0] : 0,
            'procedipro'            => $this->procediproCode,
            'acompaniante_asp'       => $this->companion,
            'cant_sesiones_asp'     => $this->numSessions ?? 0,
            'sede_asp'              => $this->central,
            'lugar_atencion_asp'    => $this->place,
            'nautoriz_asp'          => $this->authCode,
            'id_citas_asp'          => implode('-',$this->idsToEvo),
            'entidad'               =>$this->epsCode,
            'convenio'              =>$this->covenatCode,
            'parentezco_acompaniante_asp'=>$this->kinred

        ];
    }
    public function toDxArray(int $idCita, string $admision):array{
        return [
            'historia'=>$this->userHistory,
            'sdt_fecha'=>$this->dateAppo,
            'dxp'=>$this->entry,
            'dx1'=>$this->out,
            'convenio'=>$this->covenatCode,
            'entidad'=>$this->epsCode,
            'proc_adm'=>$this->procedim,
            'dxprin'=>1,
            'clase'=>0,
            'final'=>$this->purpose,
            'causa'=>$this->externalCause,
            'tipdx'=>$this->typeDiagnosisEntry,
            'sdt_fechacreacion'=>$this->now,
            'creadopor'=>$this->cedula,
            'sede'=>$this->central,
            'via_ingreso'=>$this->entryRoute,
            'id_cita'=>$idCita,
            'admision'=>$admision,
            'tipo_cita'=>$this->tipoAppo

        ];
    }
    public function toPaymentArray(int $idCita,string $admin){
        return [
            'codigo'=>$this->userHistory,
            'recibo'=>$admin,
            'sdt_fecha'=>$this->dateAppo,
            'valor'=>$this->value,
            'tipopago'=>'AUTORIZACIÓN',
            'elabora'=>$this->registroCed,
            'prefijo'=>$this->prefijo,
            'detalle'=>$this->epsCode,
            'ced_prof'=>$this->cedula,
            'ambito'=>'1',
            'finalidad'=>2,
            'concepto'=>3,
            'freal'=>1,
            'personal'=>5,
            'importado'=>0,
            'entsub'=>$this->covenatCode,
            'sdt_fecha_f'=>$this->registroDate,
            'vende'=>'55555555',
            'ccostos'=>$this->costCenter,
            'sede'=>'001',
            'tarifa'=>$this->tarife,
            'idcita'=>$idCita,
            'sede_old'=>$this->central,
            'centro_no_change'=>1,
            'usuario'=>$this->registro,
            
        ];
    }
    public function toPagoDetArray($idCita,$admision):array{
        return [
            'nro'=>$admision,
            'sdt_fecha'=>$this->dateAppo,
            'codigo'=>$this->userHistory,
            'trata'=>0,
            'cedprof'=>$this->epsCode,
            'nombre'=>$this->client,
            'cedula'=>$this->clientCedula,
            'cod_ing'=>$this->procedim,
            'descrip'=>$this->procedimiento,
            'vlr_ing'=>$this->value,
            'usuario'=>$this->registro,
            'prefijo'=>'OR',
            'cod_espec'=>$this->specialty,
            'cod_pcto'=>$this->cedula,
            'cant'=>$this->numSessions,
            'nivel'=>$this->authCode,
            'importado'=>'0',
            'sede'=>'001',
            'porrete'=>0,
            'retencion'=>0,
            'okadmi'=>0,
            'prof_remitente'=>'',//pendiente haber como se llena
            'liq_flag'=>0,
            'gestionada'=>0,
            'entregada'=>0,
            'nocobrar'=>0,
            'bodega'=>'',
            'lote'=>'',
            'costo'=>0,
            'valor_siniva'=>$this->value,
            'lab_proc'=>0,
            'cant_nc'=>0,
            'adm_citas'=>0,
            'id_cita'=>$idCita,
            'id_mipres'=>'',
            'durtrat'=>0,
            'tipo_cita'=>'01',
            'via_ingreso'=>$this->entryRoute,
            'finalidad'=>$this->purpose,
            'dxp'=>$this->entry
        ];
    }
    public function toBufferArray():array{
        return  [
            'place'               => $this->place,
            'entryRoute'          => $this->entryRoute,
            'externalCause'       => $this->externalCause,
            'purpose'             => $this->purpose,
            'entry'               => $this->entry,
            'typeDiagnosisEntry'  => $this->typeDiagnosisEntry,
            'out'                 => $this->out,
            'typeDiagnosisOutPut' => $this->typeDiagnosisOutPut,

        ];

    }
    public function toHistoricDxArray():array{
        return [
            'historia'=>$this->userHistory,
            'dx_entrada'=>$this->entry,
            'dx_salida'=>$this->out,
            'procedipro'=>$this->procediproCode,
            'sdt_fecha_reg'=>$this->now,
            'acompaniante_asp'=>$this->companion,
            'parentezco_acompaniante_asp'=>$this->kinred
        ];
    }

}

