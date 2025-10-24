<?php

namespace App\Repositories;

use App\Interfaces\EvoRepositoryInterface;
use App\Models\NeuroEvoModel;
use App\Models\IntegralRehabilitacionEvo;

class EvoRepository extends BaseRepository implements EvoRepositoryInterface{
    public function getPsicoEvoInRangeTime(string $historia, string $from, string $to): array
    {
        $query="SELECT 
                evd.fecha AS fecha_registro,    
                evd.historia AS historia, 
                evd.usuario AS idter, 
                evd.evol AS descripcion, 
                evo.analisis,        
                evo.objetivo, 
                evd.id, 
                evd.observa AS observacion, 
                evo.acompaniante_asp as acompaniante,
                pa.parentezco,
                evo.plam , 
                emp.firma, 
                emp.enombre, 
                emp.titulouni, 
                emp.tarjetap,
                'neuro' AS formato   
                FROM h_svitales as evd  
                INNER JOIN evoluciones evo ON evd.historia  = evo.codigo
                INNER JOIN emplea emp ON  evd.usuario = emp.ecc AND evd.fecha = evo.fec_creado  
                LEFT JOIN parentezco pa ON evo.parentezco_acompaniante_asp = pa.codigo
                WHERE evd.f_reg BETWEEN CONVERT(smalldatetime,?,120) AND CONVERT(smalldatetime,?,120)
				AND evd.historia = ?
                AND  evo.especialidad = '009'
                ORDER BY evd.f_reg ASC
                ";
                
        $bindings=[$from,$to,$historia];
        $evos=self::sendQuery(query:$query,bindings:$bindings);
        return collect($evos)->mapInto(NeuroEvoModel::class)->toArray();

    }

    public function getAbaEvoInRangeTime(string $historia, string $from, string $to){
        $query="SELECT 
                evd.fecha AS fecha_registro,    
                evd.historia AS historia, 
                evd.usuario AS idter, 
                evd.evol AS descripcion, 
                evo.analisis,        
                evo.objetivo, 
                evd.id, 
                evd.observa AS observacion, 
                evo.acompaniante_asp as acompaniante,
                pa.parentezco,
                evo.plam , 
                emp.firma, 
                emp.enombre, 
                emp.titulouni, 
                emp.tarjetap,
               'aba' AS formato  
               FROM h_svitales as evd  
               INNER JOIN evoluciones evo ON evd.historia  = evo.codigo 
               INNER JOIN emplea emp ON  evd.usuario = emp.ecc AND evd.fecha = evo.fec_creado  
               LEFT JOIN parentezco pa ON evo.parentezco_acompaniante_asp = pa.codigo
               WHERE evd.f_reg BETWEEN CONVERT(smalldatetime,?,120) AND CONVERT(smalldatetime,?,120)
               AND  evd.historia = ?
               AND  evo.especialidad = '012'
               ORDER BY evd.f_reg ASC
        ";
        $bindings=[$from,$to,$historia];
        $evos=self::sendQuery(query:$query,bindings:$bindings);
        return collect($evos)->mapInto(NeuroEvoModel::class)->toArray();

    }

    public function getEvoIntegralReabilitationByRangeDate(string $historia, string $from, string $to, bool $isFono=false): array
    {

        $query = "SELECT  fonoaudiologia_2.fecha_registro,
                SUBSTRING(CONVERT(varchar, fonoaudiologia_2.hora_inicio),1,5) as hora_ter,
                fonoaudiologia_2.text1 as sesiones,
                fonoaudiologia_2.historia,
                fonoaudiologia_2.text3 AS cedula,
                fonoaudiologia_2.mh7 AS objetivos,
                fonoaudiologia_2.mh8 AS descripcion,
                fonoaudiologia_2.mh9 as resultados,
                fonoaudiologia_2.id,
				fonoaudiologia_2.procedipro,
				fonoaudiologia_2.acompaniante_asp as acompaniante,
				pa.parentezco,
                'rehabilitacion_integral' AS formato,
                emp.firma,
                emp.enombre,
                RTRIM(emp.titulouni) AS titulouni,
                RTRIM(emp.tarjetap) AS tarjetap
            FROM fonoaudiologia_2
            INNER JOIN emplea emp ON fonoaudiologia_2.text3 = emp.ecc
			LEFT JOIN parentezco pa ON fonoaudiologia_2.parentezco_acompaniante_asp = pa.codigo
            WHERE fonoaudiologia_2.fecha BETWEEN CONVERT(smalldatetime,?,120) and   CONVERT(smalldatetime,?,120) 
			AND fonoaudiologia_2.historia = ?";

        $bindings = [$from,$to,$historia];
        if ($isFono) {
            $query .= " AND CAST(fonoaudiologia_2.Tf1 AS int) = 0 AND CAST(fonoaudiologia_2.ver AS int) = 0";}

        $query .= " ORDER BY fonoaudiologia_2.fecha_registro ASC";
        $evos= self::sendQuery(query: $query, bindings: $bindings);

        return collect($evos)->mapInto(IntegralRehabilitacionEvo::class)->toArray();
    }
}