<?php


namespace App\Domain;

use App\Exceptions\CustomExceptions\BadRequestException;
use App\Models\ScheduleModel;
use Carbon\Carbon;
use App\utils\DateManager;


class CitasDomain{

    public static function completeScheduleSingleAppo(Carbon $startDate):array{

            $day = DateManager::getDayByDate($startDate);
            $hour = DateManager::getHoursOfDateInAmPmFormat($startDate);
    
          
            $infoCitaSingle = [
                'sessions' => 1,
                'startHour' => $hour,
            ];
            return [$day => $infoCitaSingle];

    }
    public static function validateStartDateInSchedule(Carbon $startDate,array $allowedDays,array $scheduleDays) {
         
        $startDayName = DateManager::getDayByDate($startDate);
        if (!in_array($startDayName, $allowedDays)) {
            throw new BadRequestException("El dia de inicio no coincide con los dias agregados al horario de citas", 400);
        }
    
        $configuredStartHour = $scheduleDays[$startDayName]['startHour'];
        $configuredHour = DateManager::getHourAmPmFormat($configuredStartHour);
        $configuredMinute = DateManager::getMinutesAmPmFormat($configuredStartHour);
    
        $startHour = $startDate->hour;
        $startMinute = $startDate->minute;
    
        if ($configuredHour != $startHour || $configuredMinute != $startMinute) {
            throw new BadRequestException("La hora de inicio no coincide con los dias agregados al horario de citas", 400);
        }
    }
    public static function getNumSessionsToSave(int $availableSessions,bool $isProcediproContable,int $requestedSessions) {
        
        if(!$isProcediproContable){
            return $requestedSessions;
        }
        return min($requestedSessions, $availableSessions);
    }
    public static function  checkRememberWhatsWhitObservations(bool $whatsappRemember,$observaId){


        if($whatsappRemember && !$observaId){
            throw new BadRequestException("Es necesario observaciones para guardar citas con recordatorio
                                            en whatsapp
                                            ",400);
        }

    }
    public static function validateLimitExpireAuth(array $schedule,Carbon $expireAuthDate){
        $limit = count($schedule);
        $ultimaFecha =$schedule[$limit - 1]->copy();
        $ultimaFechaInicio=$ultimaFecha->startOfDay();
        if ($ultimaFechaInicio->isAfter($expireAuthDate)) {
            throw new BadRequestException("No se puede crear las citas, puesto que la fecha final excede la fecha de vencimiento de la autorización", 400);
        }
    }
    public static  function getDifBeetwenDays(array $weekDays):array{
        $daysDictionary=DateManager::$daysWeekInverted;
        $diferenceDays=[];
        $sizeWeekDays=count($weekDays);

        for ($indexDay=0;$indexDay<$sizeWeekDays;$indexDay++){
            try{
                $diference = $daysDictionary[$weekDays[$indexDay + 1]] - $daysDictionary[$weekDays[$indexDay]];
                $diferenceDays[] = $diference;
            }catch(\Exception $e){
                $leftDays = 7- $daysDictionary[$weekDays[$sizeWeekDays - 1]];
                $daysLaterStartWeek = $daysDictionary[$weekDays[0]];
                $diferenceDays[] = $leftDays + $daysLaterStartWeek;
            }
        }
        return $diferenceDays;
    }
    /**
     * @return Carbon[]
     */
    public static  function CreateSchedule(int $sessionsAvaibles, 
                                            int $sessionDuration,
                                            Carbon $startDate, 
                                            array $weekDays, 
                                            array $diferenceDays,
                                            array $weekDaysKeys):array {
        $nameDayStart = DateManager::getDayByDate($startDate);
        $startIndex = array_search($nameDayStart, $weekDaysKeys);
        
        $schedule = [];
        $indexDay = $startIndex;

        while ($sessionsAvaibles > 0) {
            
            $dayName=DateManager::getDayByDate($startDate);

            $maxSessionsPerDay = $weekDays[$dayName]['sessions'];
            $numSessions = min($sessionsAvaibles, $maxSessionsPerDay);

            $startHourDay=$weekDays[$dayName]['startHour'];
            $hour=DateManager::getHourAmPmFormat($startHourDay);
            $minute=DateManager::getMinutesAmPmFormat($startHourDay);
            
            $startHour = $startDate->setTime(hour:$hour, minute:$minute);
             
            if (!DateManager::isHoliday($startHour)) {
                for ($session = 0; $session < $numSessions; $session++) {
                    $schedule[] = $startDate->copy();  
                    $startDate->addMinutes($sessionDuration);
                }
                $sessionsAvaibles -= $numSessions;
            }
    
             
            $startDate->addDays($diferenceDays[$indexDay]);
            $indexDay = ($indexDay + 1) % count($diferenceDays);
            
        }
    
        return $schedule;
    }
    /**
     * @param Carbon[]         $scheduleNewAppos
     * @param ScheduleModel[] $scheduleProfesional
     * @return array<string, \App\Models\ScheduleModel>
     */
    public static function validateSchedule(array $scheduleNewAppos, array $scheduleProfesional)
    {
        $alreadyValidate = [];
        $appoMapIntoSchedule=[];

        foreach ($scheduleNewAppos as $session) {

            $key = $session->dayOfWeek . '-' . $session->format('H:i');
            if (in_array($key, $alreadyValidate, true)) {
                $appoMapIntoSchedule[$session->format('Y-m-d H:i')]=$item;
                continue;
            }

            $hasDisponibility = false;

            foreach ($scheduleProfesional as $item) {
                if ($item->checkDisponibility($session)) {
                    $hasDisponibility = true;
                    $appoMapIntoSchedule[$session->format('Y-m-d H:i')]=$item;
                    break; 
                }
            }
            if (!$hasDisponibility) {
                throw new BadRequestException(
                    "El Profesional no tiene disponibilidad el día " .
                    DateManager::dateToStringFormat($session),
                    400
                );
            }

            $alreadyValidate[] = $key;
            
        }
        return $appoMapIntoSchedule;
    }
    /**
     * @param Carbon[]   $scheduleNewAppos
     * @param stdClass[] $scheduleClient
     * @param int       $sessionDuration
     * @param string $procedipro
     */
    public static function validateDisponibilityClient(
        array $scheduleNewAppos,
        array $scheduleClient,
        int $sessionDuration,
        string $procedipro
    ): void {

        // 🔹 Preprocesar citas existentes
        $clientIntervals = [];

        foreach ($scheduleClient as $sessionClient) {
            $start = Carbon::parse($sessionClient->fecha_inicio);
            $end = Carbon::parse($sessionClient->hora_fin);

            $clientIntervals[] = [$start, $end,$sessionClient->procedipro];
        }

        usort($clientIntervals, fn($a, $b) => $a[0] <=> $b[0]);
        foreach ($scheduleNewAppos as $newStart) {

            $newEnd = $newStart->copy()->addMinutes($sessionDuration);
            foreach ($clientIntervals as [$clientStart, $clientEnd, $clientProcedipro]) {

                if ($clientStart >= $newEnd) {
                    break;
                }
                if ($newStart < $clientEnd && $newEnd > $clientStart && $procedipro === $clientProcedipro) {
                    throw new BadRequestException(
                        "Esta accion no se puede realizar por que el cliente ya tiene una cita para el dia " .
                        DateManager::dateToStringFormat($newStart)." que hace conflicto con la nueva cita programada para el dia " .
                        DateManager::dateToStringFormat($clientStart),
                        400
                    );
                }
            }
        }
    }

    public static function buildAuditMsm(
        string $user,
        string $client,
        array $schedule,
        string $profesional,
        string $autoriz,
        string $family,
        array $ids
    ): string {

        $count   = count($schedule);
        $start   = DateManager::dateToStringFormatOnlyDay($schedule[0]);
        $finish  = DateManager::dateToStringFormatOnlyDay($schedule[$count - 1]);
        $idsStr  = implode(', ', $ids);

        return
            "El usuario $user creo $count citas ".
            "para el profesional $profesional ".
            "al paciente $client ".
            "desde $start hasta $finish ".
            "bajo la autorizacion $autoriz ".
            "con ids $idsStr ".
            "y asociadas al grupo de citas $family";
    }

    public static function buildAuditMsmClone(
        string $user,
        string $profesional,
        Carbon $from,
        Carbon $to,
        Carbon $start,
        array $ids,
    ){

        $fromStr   = DateManager::dateToStringFormatOnlyDay($from);
        $toStr  = DateManager::dateToStringFormatOnlyDay($to);
        $startStr=DateManager::dateToStringFormatOnlyDay($start);
        $idsStr  = implode(', ', $ids);

        return
            "El usuario $user clono ".
            "el calendario del  profesional $profesional ".
            "desde $fromStr hasta $toStr ".
            "Iniciando el dia $startStr".
            "con ids $idsStr ";

    }
    public static function buildAuditMsmDlete(
        string $user,
        array $ids,
        string $cliente,
        string $profesional
    ){
        $idsStr = implode(', ', $ids);
        return 
        "El usuario $user elimino ".
        "la(s) cita(s) con id(s) $idsStr ".
        "perteneciente al cliente $cliente ".
        "asignada al profesional $profesional ";

    }


}