<?php

namespace App\Repositories;

use App\Constants\TemplatesQuerys;
use App\Dtos\ABAEvoDto;
use App\Dtos\BasicEvoDto;
use App\Dtos\PsicoEvoDto;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\PersistenceError;
use App\Interfaces\AppoimentsRepositoryInterface;
use App\Models\AppoimentInfoModel;
use App\Models\AppoimentModel;
use App\Models\DisponilityEvoModel;
use App\Models\DxHistoryModel;
use App\Models\NumEvoModel;
use App\Services\LogsService;
use Illuminate\Support\Facades\DB;



class AppoimentRepository extends BaseRepository implements AppoimentsRepositoryInterface
{
    public function getDailyAppoimetsProfesionalByIdentity(string $identityNumber, bool $toModel = false): array
    {
        $where = "WHERE CAST(fecha AS DATE) = CAST(GETDATE() AS DATE) AND cedprof = ?";
        $query = str_replace('{{{}}}', $where, TemplatesQuerys::TEMPLATE_QUERY_CITAS);
        $appoiments = self::sendQuery(query: $query, bindings: [$identityNumber]);

        if (empty($appoiments)) {
            throw new NotFoundException("No se encontraron citas hoy", 404);
        }
        return $toModel ? $this->toModel($appoiments) : $appoiments;
    }

    public function getScheduleProfesionalInRangeTime(string $identityNumber, bool $toModel, string $from, string $to): array
    {
        $where = "WHERE cedprof = ?
                  AND fecha BETWEEN CONVERT(smalldatetime, ?, 120) 
                  AND CONVERT(smalldatetime, ?, 120)";
        
        $query = str_replace('{{{}}}', $where, TemplatesQuerys::TEMPLATE_QUERY_CITAS);
        $bindings = [ $identityNumber,$from, $to];
        $appoiments = self::sendQuery(query: $query, bindings: $bindings);
        if (empty($appoiments)) {
            throw new NotFoundException("No se encontraron citas en el rango", 404);
        }
        return $toModel ? $this->toModel($appoiments) : $appoiments;
    }

    public function getAppoimentsById(array $ids, bool $toModel = false): array
    {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $where = "WHERE id IN ($placeholders)";
        $query = str_replace('{{{}}}', $where, TemplatesQuerys::TEMPLATE_QUERY_CITAS);
        $appoiments = self::sendQuery(query: $query, bindings: $ids);

        if (empty($appoiments)) {
            throw new NotFoundException("No se encontraron citas con los IDs proporcionados", 404);
        }
        return $toModel ? $this->toModel($appoiments) : $appoiments;
    }

    public function cancelAppoiment(string $ids, string $meanCancel, string $dateAppoiment, string $razon): array
    {
        $idsArray = explode('|||', $ids);
        $idsForQuery = array_map('intval', $idsArray);
        $query1 = "UPDATE citas 
            SET cancelada = '1',
                realizar = ?, 
                fec_can = GETDATE(),
                mean_cancel = ?
            WHERE id = ? 
              AND asistio = '0' 
              AND na = '0'";

        $query2 = "INSERT INTO citas_canceladas 
            (ids_sessions, id_example, num_sessions_canceled, date_cita_canceled)
            VALUES (?, ?, ?, CONVERT(smalldatetime, ?, 120))";

        DB::transaction(function () use ($razon, $idsForQuery, $ids, $dateAppoiment, $meanCancel, $query1, $query2) {
            foreach ($idsForQuery as $id) {
                self::sendQuery($query1, [$razon, $meanCancel, $id], 'update');
            }
            self::sendQuery($query2, [
                $ids,
                $idsForQuery[0],
                count($idsForQuery),
                $dateAppoiment
            ], 'insert');
        });

        return $idsForQuery;
    }
    public function getIdsAppoimentsByAutoriz(string $autoriz, 
                                            string $cedulaProfesional, 
                                            string $codEnt, 
                                            string $history,
                                            string $procedure): array
    {   
        $querySelect=TemplatesQuerys::TEMPLATE_TO_FILTER_APPIMENTS_BY_AUTORIZ;
        $bindingsGetIds = [$cedulaProfesional, $history, $autoriz,$procedure];
        return self::sendQuery(query:$querySelect,bindings:$bindingsGetIds);
    }

    public function getInfoFacAppoiment(int $id): AppoimentInfoModel
    {
        $query="WITH tarifa AS (
            SELECT TOP 1 
                ent.tarifa, 
                ci.id       AS cita,
                ci.tiempo
            FROM citas ci
            JOIN entidades ent 
            ON ent.codigo = ci.codent2
            AND ent.admini = ci.codent
            WHERE ci.id = ?
        ),
        precio AS (
            SELECT TOP 1 
                pd.precio,
                pd.codesp,
                pd.ref,
                ta.cita
            FROM procdent pd
            JOIN tarifa ta 
            ON ta.tarifa = pd.cod_enti 
            AND ta.tiempo = pd.codigo
        ),
        historial_dx AS (
            SELECT TOP 1 
                h.id, 
                ci.id AS cita_id
            FROM historico_dx_asp AS h
            INNER JOIN citas ci 
                ON ci.nro_hist = h.historia 
            AND ci.procedipro = h.procedipro
            WHERE DATEDIFF(DAY, h.ult_actualizacion, GETDATE()) < 90
            AND ci.id = ?
            
        )
        
        SELECT 
            ci.sede,
            ci.nro_hist,
            ci.codent,
            pro.id                    AS procedipro,
            ci.autoriz,
            ci.codent2,
            ci.tiempo,
            ci.registro,
            ci.ced_usu,
            ci.fec_hora,
            se.c_costo_asp            AS c_costo,
            ta.tarifa,
            pr.precio,
            pr.codesp,
            pr.ref					  AS referencia,
            RTRIM(cli.nombre)         AS cliente,
            cli.nit_cli               AS cedula,
            ci.procedim               AS procedimiento,
            h.id                      AS historico_dx,
			adm.id					  AS tipo_admision
        FROM citas ci
        JOIN procedipro pro 
        ON pro.nombre = ci.procedipro
        JOIN sede se 
        ON se.cod = ci.sede
        JOIN tarifa ta 
        ON ta.cita = ci.id
        JOIN precio pr 
        ON pr.cita = ci.id
        JOIN cliente cli 
        ON cli.codigo = ci.nro_hist
        LEFT JOIN historial_dx h
        ON h.cita_id=ci.id
        LEFT JOIN tipo_admision adm ON ci.procedipro = adm.procedipro_asp
                                    AND ci.sede = adm.sede_asp
        WHERE ci.id = ?;
        ";
        $bindings=[$id,$id,$id];
        $infoAppo=self::sendQuery(query:$query,bindings:$bindings);
        return new AppoimentInfoModel($infoAppo[0]);
    }

    public function evoFono(BasicEvoDto $evo, DisponilityEvoModel $dispo, array $ids,string|null $idHistoricoDx): void
    {
        try {
            DB::beginTransaction();

            $evoData = $evo->toArrayEvo();
            $placeholdersEvo = self::makePlaceholders($evoData);
            $columnsEvo = self::makeColumns($evoData);
            $valuesEvo = self::makeValues($evoData);
            DB::insert("INSERT INTO fonoaudiologia_2 ({$columnsEvo}) VALUES ({$placeholdersEvo})", $valuesEvo);

            $isFirstTime=$evo->isFirstTime();

            $this->saveStaticData(evo:$evo,ids:$ids,dispo:$dispo,idHistoricoDx:$idHistoricoDx,isFirstTime:$isFirstTime);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack(); 
            throw new PersistenceError(
                message: $e->getMessage(),
                action: 'evolucion fonoaudiologia_2',
                logsService: app(LogsService::class)
            );
        }
    }
    public function evoPsico(PsicoEvoDto $evo, DisponilityEvoModel $dispo, array $ids, ?string $idHistoricoDx): void
    {
        try {
            DB::beginTransaction();

            $evoData = $evo->toArrayEvo();
            $vitalesData=$evo->toHVitailsArray();

            $placeholdersEvo = self::makePlaceholders($evoData);
            $columnsEvo = self::makeColumns($evoData);
            $valuesEvo = self::makeValues($evoData);

            $placeholdersVitals = self::makePlaceholders($vitalesData);
            $columnsVitails = self::makeColumns($vitalesData);
            $valuesVitails = self::makeValues($vitalesData);

            DB::insert("INSERT INTO evoluciones ({$columnsEvo}) VALUES ({$placeholdersEvo})", $valuesEvo);
            DB::insert("INSERT INTO h_svitales ({$columnsVitails}) VALUES ({$placeholdersVitals})", $valuesVitails);


            $isFirstTime=$evo->isFirstTime();

            $this->saveStaticData(evo:$evo,ids:$ids,dispo:$dispo,idHistoricoDx:$idHistoricoDx,isFirstTime:$isFirstTime);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack(); 
            throw new PersistenceError(
                message: $e->getMessage(),
                action: 'evolucion psicologia',
                logsService: app(LogsService::class)
            );
        } 
    }
    public function evoABA(ABAEvoDto $evo, DisponilityEvoModel $dispo, array $ids, ?string $idHistoricoDx): void
    {
        try {
            DB::beginTransaction();

            $evoData = $evo->toArrayEvo();
            $vitalesData=$evo->toHVitailsArray();

            $placeholdersEvo = self::makePlaceholders($evoData);
            $columnsEvo = self::makeColumns($evoData);
            $valuesEvo = self::makeValues($evoData);

            $placeholdersVitals = self::makePlaceholders($vitalesData);
            $columnsVitails = self::makeColumns($vitalesData);
            $valuesVitails = self::makeValues($vitalesData);

            DB::insert("INSERT INTO evoluciones ({$columnsEvo}) VALUES ({$placeholdersEvo})", $valuesEvo);
            DB::insert("INSERT INTO h_svitales ({$columnsVitails}) VALUES ({$placeholdersVitals})", $valuesVitails);


            $isFirstTime=$evo->isFirstTime();

            $this->saveStaticData(evo:$evo,ids:$ids,dispo:$dispo,idHistoricoDx:$idHistoricoDx,isFirstTime:$isFirstTime);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack(); 
            throw new PersistenceError(
                message: $e->getMessage(),
                action: 'evolucion ABA',
                logsService: app(LogsService::class)
            );
        } 
    }


    public function getDisponibilityAppo(int $id):DisponilityEvoModel
    {   
        $query="WITH cita AS (
            SELECT tiempo,
                autoriz,
                nro_hist
            FROM citas 
            WHERE id = ?
        ),

        asistidas AS (
            SELECT COUNT(*) AS asistidas 
            FROM citas ci
            JOIN cita ON ci.tiempo = cita.tiempo
                    AND ci.autoriz = cita.autoriz 
                    AND ci.nro_hist = cita.nro_hist
            WHERE ci.asistio = 1
        ),

        procedimientos AS (
            SELECT 
                au.id,
                au.cantidad,
                'autoriza' AS tabla
            FROM autoriza au
            JOIN cita ON au.n_autoriza = cita.autoriz
                    AND au.historia = cita.nro_hist
                    AND au.procedi = cita.tiempo

            UNION ALL

            SELECT 
                ad.id,
                ad.cantidad,
                'autorizad' AS tabla
            FROM autorizad ad
            JOIN cita ON ad.n_autoriza = cita.autoriz
                    AND ad.procedi = cita.tiempo
                    AND ad.historia = cita.nro_hist
        )

        SELECT 
            a.asistidas, 
            p.*, 
            CAST(p.cantidad - a.asistidas AS INT) AS disponibles
        FROM asistidas a
        JOIN procedimientos p ON 1 = 1";
        $disponibility=self::sendQuery(query:$query,bindings:[$id]);
        return new DisponilityEvoModel($disponibility[0]);
    }

    public function getDxHistoryByAppoId(int $id):DxHistoryModel|null
    {
        $query="SELECT TOP 1 
                h.historia,
                h.dx_entrada,
                dx_salida,
                acompaniante_asp,
                parentezco_acompaniante_asp
            FROM historico_dx_asp AS h
            INNER JOIN citas ci 
                ON ci.nro_hist = h.historia 
            AND ci.procedipro = h.procedipro
            WHERE DATEDIFF(DAY, h.ult_actualizacion, GETDATE()) < 90
            AND ci.id = ?
            ORDER BY h.id DESC";

        
        $history=self::sendQuery(query:$query,bindings:[$id]);
        return !empty($history)?new DxHistoryModel($history[0]):null;
    }
    public function getNumEvoPsicologyByHistory(string $history):NumEvoModel{
        $query="SELECT ISNULL(MAX(num_evo), 0) AS num_evo
                    FROM evoluciones
                    WHERE codigo = ?";
        $numEvo=$this->sendQuery(query:$query,bindings:[$history]);
        return new NumEvoModel($numEvo[0]);
    }
    public function getAutorizAvailablesToChangeByAppoId(int $id): array
    {
        $query="SELECT TOP 3 code
            FROM (
                SELECT DISTINCT
                    au.n_autoriza AS code,
                    au.fecha
                FROM autoriza au
                INNER JOIN citas ci
                    ON ci.tiempo = au.procedi
                    AND ci.nro_hist = au.historia
                WHERE au.f_vence >= CAST(GETDATE() AS DATE)
                AND au.f_inicial <= CAST(GETDATE() AS DATE)
                AND (au.anulada = 0 OR (au.suspendida = 1 AND au.anulada = 1))
                AND au.cerrar_ord_asp != '1'
                AND ci.id = ?
                AND NOT EXISTS (
                    SELECT 1
                    FROM ven_det
                    WHERE ven_det.autoriz = au.n_autoriza
                        AND ven_det.codigo = ci.nro_hist
                        AND ven_det.detalle = ''
                )
            ) AS auths
            ORDER BY fecha DESC";
        return self::sendQuery(query:$query,bindings:[$id]);
    }

    public function openPastApposByIds(array $ids):void{
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        self::sendQuery(query:"UPDATE citas set na='0',fecha_evo_ampliada = '1' WHERE id in ($placeholders)",
                        bindings:$ids,typeConsult:'update');
    }
    public function updateAppoById(int $id,array $data): int
    {
        $setClause=self::makeSetClause($data);
        $values=self::makeValues($data);
        $query="UPDATE citas SET $setClause WHERE id = ?";
        return self::sendQuery(query:$query,bindings:[...$values,$id],typeConsult:'update');
    }
    private function getConsecutive():string {
        $consecutivoRow = DB::selectOne("
            SELECT LTRIM(RTRIM(prefijo)) + '' + LTRIM(RTRIM(consecu)) AS consecutivo
            FROM con_inv WITH (UPDLOCK, ROWLOCK)
            WHERE sigla = 'OR' ");
       return  $consecutivoRow->consecutivo;
    }

    private function saveDxEvo(BasicEvoDto|PsicoEvoDto $evo,array $ids,string $consecutivo){

        foreach ($ids as $id) {
            // Inserción en dx
            $dx = $evo->toDxArray((int) $id, $consecutivo);
            DB::insert(
                "INSERT INTO dx (" . self::makeColumns($dx) . ") VALUES (" . self::makePlaceholders($dx) . ")",
                self::makeValues($dx)
            );

            // Inserción en pagosr
            $pagosr = $evo->toPaymentArray((int) $id, $consecutivo);
            DB::insert(
                "INSERT INTO pagosr (" . self::makeColumns($pagosr) . ") VALUES (" . self::makePlaceholders($pagosr) . ")",
                self::makeValues($pagosr)
            );
            $idAdmision = DB::getPdo()->lastInsertId();

            // Inserción en pagodet
            $pagodet = $evo->toPagoDetArray((int) $id, $consecutivo);
            DB::insert(
                "INSERT INTO pagodet (" . self::makeColumns($pagodet) . ") VALUES (" . self::makePlaceholders($pagodet) . ")",
                self::makeValues($pagodet)
            );

            // Marcar asistencia
            DB::update("UPDATE citas SET asistio = '1',clinico_nuevo='1', id_pagosr = ? WHERE id = ?", [$idAdmision, $id]);

            $consecutivo=$this->consecutivoIncrementer($consecutivo);
        }
        return $consecutivo;
    }
    private function setConsecutive(string $newConsecutive):void{
        DB::update("UPDATE con_inv SET consecu = ? WHERE sigla = 'OR'",[$newConsecutive]);
    }
    private function setAvailibiility(array $ids,DisponilityEvoModel $dispo):void{
        DB::update(
            "UPDATE {$dispo->getTable()} SET cdispo = cdispo + ? WHERE id = ?",
            [count($ids), $dispo->getId()]
        );

    }
    private function updateHistoricDx(bool $isFirstTime,BasicEvoDto|PsicoEvoDto $evo,string|null $idHistoricoDx):void{
        if($isFirstTime){
            $dxHist = $evo->toHistoricDxArray();
            $placeholdersDxHist = self::makePlaceholders($dxHist);
            $columnsDxHist = self::makeColumns($dxHist);
            $valuesDxHist = self::makeValues($dxHist);
            DB::insert("INSERT INTO historico_dx_asp ({$columnsDxHist}) VALUES ({$placeholdersDxHist})",$valuesDxHist);
        }
        //actualizar la ultima actaulizacion en el historico
        elseif($idHistoricoDx){
            DB::update("UPDATE historico_dx_asp SET ult_actualizacion = GETDATE() WHERE id = ?",[$idHistoricoDx]);
        }
    }

    private function saveStaticData(BasicEvoDto|PsicoEvoDto $evo,
                                    array $ids,
                                    DisponilityEvoModel $dispo,
                                    string|null $idHistoricoDx,
                                    bool $isFirstTime
                                    ):void{
            $consecutivoRow = self::getConsecutive();
            $consecutivoNew=$this->saveDxEvo(evo:$evo,ids:$ids,consecutivo:$consecutivoRow);
            $this->setConsecutive($consecutivoNew);
            $this->setAvailibiility(ids:$ids,dispo:$dispo);
            $this->updateHistoricDx(isFirstTime:$isFirstTime,evo:$evo,idHistoricoDx:$idHistoricoDx);
    }


    private function toModel(array $data): array{
        return collect($data)->mapInto(AppoimentModel::class)->toArray();
    }
    private function consecutivoIncrementer(string $consecutivo){
        $numero = substr($consecutivo, 2);
        $numeroNuevo = (int)$numero + 1;
        $numeroFormateado = str_pad($numeroNuevo, 8, '0', STR_PAD_LEFT);

        return  $numeroFormateado;

    }
}


