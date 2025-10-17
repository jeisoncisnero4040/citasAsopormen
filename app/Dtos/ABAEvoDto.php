<?php

namespace App\Dtos;

use App\Requests\AppoimentsRequests;
use Illuminate\Http\Request;


class ABAEvoDto extends PsicoEvoDto{
    protected string $observations;
    protected string $especialidad='012';

    public function __construct(Request $request) {
        parent::__construct($request);
        $this->observations=$request->input('observations');
        
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
            'id_cita'=>$this->idsToEvo[0],
            'observa'=>$this->observations
        ];
    }
    protected function validate(array $requestData): void{
        AppoimentsRequests::validateEvoABA($requestData);
    }


}