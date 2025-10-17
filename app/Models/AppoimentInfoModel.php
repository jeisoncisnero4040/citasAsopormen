<?php

namespace App\Models;

use stdClass;

class AppoimentInfoModel
{
    private string $sede;
    private string $history;
    private string $codEps;
    private string $codProcedipro;
    private string $autoriz;
    private string $convenio;
    private string $tiempo;
    private string $userRegistro;
    private string $cedulaRegistro;
    private string $saveDate;
    private string $costCenter;
    private string $tarifa;
    private float $precio;
    private string $accountBank;
    private string $referencia;
    private string $user;
    private string $cedulaUser;
    private string $procedimiento;
    private string|null $idHistorico;
    private int|null $admisionType;

    public function __construct(stdClass $appo)
    {
        $this->sede = $appo->sede;
        $this->history = $appo->nro_hist;
        $this->codEps = $appo->codent;
        $this->codProcedipro = $appo->procedipro;
        $this->autoriz = $appo->autoriz;
        $this->convenio = $appo->codent2;
        $this->tiempo = $appo->tiempo;
        $this->userRegistro = $appo->registro;
        $this->cedulaRegistro = $appo->ced_usu;
        $this->saveDate = $appo->fec_hora;
        $this->costCenter = $appo->c_costo;
        $this->tarifa = $appo->tarifa;
        $this->precio = (float) $appo->precio;
        $this->accountBank = $appo->codesp;
        $this->referencia = $appo->referencia;
        $this->user = $appo->cliente;
        $this->cedulaUser = $appo->cedula;
        $this->procedimiento = $appo->procedimiento;
        $this->idHistorico=$appo->historico_dx;
        $this->admisionType = isset($appo->tipo_admision) ? (int)$appo->tipo_admision : null;
    }

    public function getSede(): string { return $this->sede; }
    public function getHistory(): string { return $this->history; }
    public function getCodEps(): string { return $this->codEps; }
    public function getCodProcedipro(): string { return $this->codProcedipro; }
    public function getAutoriz(): string { return $this->autoriz; }
    public function getConvenio(): string { return $this->convenio; }
    public function getTiempo(): string { return $this->tiempo; }
    public function getUserRegistro(): string { return $this->userRegistro; }
    public function getCedulaRegistro(): string { return $this->cedulaRegistro; }
    public function getSaveDate(): string { return $this->saveDate; }
    public function getCostCenter(): string { return $this->costCenter; }
    public function getTarifa(): string { return $this->tarifa; }
    public function getPrecio(): float { return $this->precio; }
    public function getAccountBank(): string { return $this->accountBank; }
    public function getReferencia(): string { return $this->referencia; }
    public function getClient(): string { return $this->user; }
    public function getCedulaClient(): string { return $this->cedulaUser; }
    public function getProcedimiento(): string { return $this->procedimiento; }
    public function getHistoricoDxId():string|null{return $this->idHistorico;}
    public function getTypeAdmision():int|null{return $this->admisionType;}
}
