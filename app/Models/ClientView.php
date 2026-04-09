<?php

namespace App\Models;

use Carbon\Carbon;

class ClientView
{

    public function __construct(

        public string $codigo,

        public ?string $zona,
        public ?string $nombre,
        public ?string $nit_cli,
        public ?string $direcc,
        public ?string $barrio,

        public ?string $municipio_codigo,
        public ?string $municipio,


        public ?string $pais,
        public ?string $pais_text,

        public ?string $fechareg,
        public ?string $f_nacio,

        public ?string $activo,
        public ?string $ecivil,
        public ?string $tip_usuario,
        public ?string $contrib,

        public ?string $ocupacion,
        public ?string $ocupacion_text,

        public ?string $creado,

        public ?string $cod_entidad,
        public ?string $convenio,
        public ?string $cod_convenio,

        public ?string $sexo,
        public ?string $sexo_text,

        public ?string $pn,
        public ?string $sn,
        public ?string $pa,
        public ?string $sa,

        public ?string $email,

        public ?string $tip_iden,
        public ?string $documento,

        public ?string $usucrea,
        public ?string $usumodi,

        public ?string $sabefirmar,

        public ?string $telacompanante,
        public ?string $parentresponsable,
        public ?string $cedula_resp,

        public ?string $g_poblacional,
        public ?string $grupo,

        public ?string $p_etnica,
        public ?string $etnia,

        public ?string $rh,

        public ?string $cel,
        public ?string $tel_acompa,

        public ?string $lugarnac,
        public ?string $niv_academi,

        public ?int $n_hijos,

        public ?string $tip_discapacidad,
        public ?string $discapacidad,

        public ?string $regimen,

        public ?string $entidad,
        public ?string $escolaridad,
        public ?string $imageUrl,
        public ?string $observaciones,
        public  ?string $grupoSisben,

        public ?string  $pn_responsable,
        public ?string  $sn_responsable,
        public ?string  $pa_responsable,
        public ?string  $sa_responsable,

    ) {}



    public static function fromArray(array $data): self
    {
        return new self(

            codigo: $data['codigo'],

            zona: $data['zona'] ?? null,
            nombre: $data['nombre'] ?? null,
            nit_cli: $data['nit_cli'] ?? null,
            direcc: $data['direcc'] ?? null,
            barrio: $data['barrio'] ?? null,

            municipio_codigo: $data['municipio_codigo'] ?? null,
            municipio: $data['municipio'] ?? null,

            pais: $data['pais'] ?? null,
            pais_text: $data['pais_text'] ?? null,

            fechareg: $data['fechareg'] ?? null,
            f_nacio: $data['f_nacio'] ?? null,

            activo: $data['activo'],
            ecivil: $data['ecivil'] ?? null,
            tip_usuario: $data['tip_usuario'] ?? null,
            contrib: $data['contrib'] ?? null,

            ocupacion: $data['ocupacion'] ?? null,
            ocupacion_text: $data['ocupacion_text'] ?? null,

            creado: $data['creado'] ?? null,

            cod_entidad: $data['cod_entidad'] ?? null,

            cod_convenio: $data['cod_convenio'] ?? null,
            convenio: $data['convenio'] ?? null,
            

            sexo: $data['sexo'] ?? null,
            sexo_text: $data['sexo_text'] ?? null,

            pn: $data['pn'] ?? null,
            sn: $data['sn'] ?? null,
            pa: $data['pa'] ?? null,
            sa: $data['sa'] ?? null,

            email: $data['email'] ?? null,

            tip_iden: $data['tip_iden'] ?? null,
            documento: $data['documento'] ?? null,

            usucrea: $data['usucrea'] ?? null,
            usumodi: $data['usumodi'] ?? null,

            sabefirmar: $data['sabefirmar'] ?? null,

            telacompanante: $data['telacompañante'] ?? null,
            parentresponsable: $data['parentresponsable'] ?? null,
            cedula_resp: $data['cedulaacompañante'] ?? null,

            g_poblacional: $data['G_poblacional'] ?? null,
            grupo: $data['grupo'] ?? null,

            p_etnica: $data['P_etnica'] ?? null,
            etnia: $data['etnia'] ?? null,

            rh: $data['rh'] ?? null,

            cel: $data['cel'] ?? null,

            tel_acompa: $data['tel_acompa'] ?? null,

            lugarnac: $data['lugarnac'] ?? null,
            niv_academi: $data['niv_academi'] ?? null,

            n_hijos: isset($data['n_hijos']) ? (int)$data['n_hijos'] : 0,

            tip_discapacidad: $data['tip_discapacidad'] ?? null,
            discapacidad: $data['discapacidad'] ?? null,

            regimen: $data['regimen'] ?? null,

            entidad: $data['entidad'] ?? null,
            escolaridad:$data['escolaridad']??null,
                        imageUrl: isset($data['foto']) && trim($data['foto']) !== '' 
                        ? trim($data['foto']) 
                        : null,
            observaciones:$data['observaciones_asp']??null,
            grupoSisben:$data['grupo_sisben']??null,
            pn_responsable:$data['pn_responsable']??null,
            sn_responsable:$data['sn_responsable']??null,
            pa_responsable:$data['pa_responsable']??null,
            sa_responsable:$data['sa_responsable']??null,

        );
    }


    public function getCode(): string
    {
        return $this->codigo;
    }
    public function getEpsCode(): ?string
    {
        return $this->cod_entidad;
    }
    public function getCovenantCode(): ?string
    {
        return $this->cod_convenio;
    }   
    public function getEdad(): ?string
    {
        if (!$this->f_nacio) {
            return null;
        }

        return Carbon::parse($this->f_nacio)->age . ' años';
    }
    public function isActive():bool{
        return $this->activo ==='1';
    }

    public function setActive(bool $active):void{
         $this->activo = (string)(int)$active;
    }



    public function toSerialize(): array
    {
        return [
            ...get_object_vars($this),
            'edad' => $this->getEdad()
        ];
    }
    public function getName():string{
        return    $this->pn.' '.
                ($this->sn ?? '').' '.
                $this->pa.' '.
                ($this->ps?? '');
    }

    public function setImageUrl(string $publicUrl){
        $this->imageUrl=$publicUrl;
    }
    public function getImageUrl():?string{
        return $this->imageUrl;
    }

}