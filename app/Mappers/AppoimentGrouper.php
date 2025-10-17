<?php

namespace App\Mappers;

use Carbon\Carbon;

class AppoimentGrouper {
    public static function groupAppoiments($appoimentList) {
        $grouped = [];
        $currentGroup = 1;
        $prevAppoiment = null;

        foreach ($appoimentList as $index => $appoiment) {
            if ($prevAppoiment === null) {
                $appoiment->grupo = $currentGroup;
            } else {
                $isSamePatient = $appoiment->nro_hist === $prevAppoiment->nro_hist;
                $isSameAuth = $appoiment->autorizacion=== $prevAppoiment->autorizacion;
                $isSameTime = $appoiment->tiempo === $prevAppoiment->tiempo;
                $isSameProcedipro=$appoiment->procedimiento===$prevAppoiment->procedimiento;
                $isChained = Carbon::parse($appoiment->fecha_completa)->eq(Carbon::parse($prevAppoiment->hora_fin));

                if ($isSamePatient && $isSameAuth && $isSameTime && $isChained && $isSameProcedipro) {
                    $appoiment->grupo = $currentGroup;
                } else {
                    $currentGroup++;
                    $appoiment->grupo = $currentGroup;
                }
            }

            $grouped[] = $appoiment;
            $prevAppoiment = $appoiment;
        }

        return $grouped;
    }
}
