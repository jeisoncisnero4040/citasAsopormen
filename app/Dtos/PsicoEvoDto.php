<?php

namespace App\Dtos;

use App\Requests\AppoimentsRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class PsicoEvoDto extends BasicEvoDto{

    protected string|null $plan;
    protected string|null $analisys;
    protected string|null $evolution;
    protected int|null $admisionType;
    protected int|null $numEvo;
    protected string|null $entryEnferm;
    protected string|null $outEnferm;
    protected string $especialidad='009';
    

    public function __construct(Request $request) {
        parent::__construct($request);
        $this->admisionType=null;
        $this->plan=$request->input('plan')??null;
        $this->evolution=$request->input('evolution')??null;
        $this->analisys=$request->input('analisys')??null;
        $this->entryEnferm=Str::upper($request->input('entryDisae'));
        $this->outEnferm=Str::upper($request->input('outDisae'));
        $this->numEvo=null;
        
        

    }

    public function setAdmisionType(int|null $admisionType):void{
        $this->admisionType=$admisionType;
    }
    public function getAadmisionType():int|null{
        return $this->admisionType;
    }
    public function setNUmEvo(int $numEvo):void{
        $this->numEvo=$numEvo;
    }
    public function getnumEvo():int|null{
        return $this->numEvo;
    }

    public function toArrayEvo(): array
    {
        return [
            'codigo'=>$this->userHistory,
            'num_evo'=>$this->numEvo,
            'creado'=>$this->cedula,
            'sdt_fec_creado'=>$this->dateAppo,
            'especialidad'=>$this->especialidad,
            'sdt_fecha_crea'=>$this->now,
            'dx_entrada'=>$this->entryEnferm,
            'dx_salida'=>$this->outEnferm,
            'idtipo_admision'=>$this->admisionType,
            'objetivo'=>$this->target,
            'analisis'=>$this->analisys,
            'plam'=>$this->plan,
            'movil'=>0,
            'reg_modulo'=>'psicologia',
            'id_cita'=>$this->idsToEvo[0],
            'numero_sesiones'=>$this->numSessions,
            'procedipro'            => $this->procediproCode,
            'acompaniante_asp'       => $this->companion,
            'cant_sesiones_asp'     => $this->numSessions ?? 0,
            'sede_asp'              => $this->central,
            'lugar_atencion_asp'    => $this->place,
            'nautoriz_asp'          => $this->authCode,
            'id_citas_asp'          => implode('-',$this->idsToEvo),
            'entidad'               =>$this->epsCode,
            'convenio'              =>$this->covenatCode,
            'parentezco_acompaniante_asp'=>$this->kinred];
    }

    public function toHVitailsArray():array{
        return [
            'historia'=>$this->userHistory,
            'observa'=>'Ninguna',
            'usuario'=>$this->cedula,
            'sdt_f_reg'=>$this->now,
            'sdt_fecha'=>$this->dateAppo,
            'entidad'=>$this->epsCode,
            'subentidad'=>$this->covenatCode,
            'sesion'=>0,
            'forma'=>'EVOLUCIONES',
            'evol'=>$this->evolution,
            'id_cita'=>$this->idsToEvo[0]
        ];
    }

    protected function validate(array $requestData): void{
        AppoimentsRequests::validateEvoPsicology($requestData);
    }

}