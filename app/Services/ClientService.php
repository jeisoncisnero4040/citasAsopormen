<?php

namespace App\Services;

use App\Dtos\CreateClientDto;
use App\Dtos\GetClientDto;
use App\Dtos\UpdateClientDto;
use App\Dtos\UpdateUserDto;
use App\Events\AuditEvent;
use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\StorageInterface;
use App\Kafka\Domain\MessageQueue;
use App\Mappers\ClientMapper;
use App\Mappers\HistoryChatBotMapper;
use App\Models\AuditMessageQueueBuilder;
use App\Models\ClientView;
use App\Models\UserRequesting;
use App\Requests\ClientRequest;
use App\utils\CelNumberManager;
use App\utils\DateManager;
use App\utils\PasswordGenerator;
use Illuminate\Support\Facades\DB;
use App\utils\ResponseManager;
use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;


class ClientService{
    private $responseManager;
    private $emailService;
    private $whatsappService;
    private ClientRepositoryInterface $clientRepo;
    private DistrictService $districtService;
    private StorageInterface $storage;
    private QueueService $queueService;

    public function __construct(ResponseManager $responseManager,
                                EmailService $emailService, 
                                WhatsappService $whatsappService,
                                ClientRepositoryInterface $clientRepo,
                                DistrictService $districtService,
                                StorageInterface $storage,
                                QueueService $queueService

                                ){
        $this->responseManager=$responseManager;
        $this->emailService=$emailService;
        $this->whatsappService=$whatsappService;
        $this->clientRepo=$clientRepo;
        $this->districtService=$districtService;
        $this->storage = $storage;
        $this->queueService = $queueService;
    }
    public function create(CreateClientDto $dto, ?UploadedFile $image, ?UploadedFile $document,UserRequesting $userRequesting):array{
        
        $codeMun= $dto->getMunicipality();
        $municipio=$this->districtService->get(code:$codeMun);
        $client = ClientMapper::clientDtoToClient(dto:$dto,municipality:$municipio);
        $lastClient=$this->clientRepo->getLastHistory();
        $lastCode = $lastClient[0]->codigo ?? '0000000000';
        $newCode = str_pad(((int)$lastCode) + 1, 10, '0', STR_PAD_LEFT);
        $client->setCode(code: $newCode);
        if(!empty($image)){
            $photoUrl=$this->storage->store(file:$image,path:"clientes/documentos/$newCode");
            $client->setUrlPhoto(url:$photoUrl);
        }
        if(!empty($document)){
            $url=$this->storage->store(file:$document,path:"clientes/documentos/$newCode");
            $client->setUrlDocument(url:$url);
        }
        
        $this->clientRepo->create(client:$client);
        return $this->getDataClientByHistoryId(['historyId'=>$newCode]);

    }
    public function searchClient(GetClientDto $dto)
    {
        $string = trim($dto->getText());

        $isNumeric = $dto->isOnlyNumericText();
        $startsWithZero = $dto->startWithZeros();

        $isCodHistory = $isNumeric && $startsWithZero && strlen($string) == 10;
        $isByCedula   = $isNumeric && !$startsWithZero;
        $isByName     = !$isNumeric;
        
        $filters = [];

        if ($isCodHistory) {
            $filters['codigo'] = $string;
        }
        if ($isByCedula) {
            $filters['nit_cli'] = $string;
        } 
        if($isByName){
            $searchName = strtoupper($string);
            $words = array_filter(explode(' ', $searchName));
            $filters['nombre'] = $words;
        }
        $clients = $this->clientRepo->search(filters: $filters);
        if (empty($clients)) {
            throw new NotFoundException("No se encontraron clientes con los filtros aplicados",404);
        }

        return $this->responseManager->success($clients);
    }
    public function update(
        string $history,
        CreateClientDto $dto,
        ?UploadedFile $image = null,
        UserRequesting $userRequesting
    ) {

        $clientView = $this->clientRepo->get(['codigo' => $history]);

        if (empty($clientView)) {
            throw new NotFoundException(
                "Información de usuario no encontrada",
                404
            );
        }
        $clientView = $clientView[0];

        $municipio = $this->districtService->get(
            code: $dto->getMunicipality()
        );

        $client = ClientMapper::clientDtoToClient(
            dto: $dto,
            municipality: $municipio,
            isNew: false
        );

        $client->setCode($history);
        $client->setUrlPhoto($clientView->getImageUrl());

        if ($image !== null) {

            $photoUrl = $this->storage->replace(
                file: $image,
                path: "clientes/documentos/$history",
                oldPath: $clientView->getImageUrl()
            );

            $client->setUrlPhoto($photoUrl);
        }

        $this->clientRepo->update($client);

        return $this->getDataClientByHistoryId([
            'historyId' => $history
        ]);
    }
    public function toggleActive(UpdateClientDto $dto, UserRequesting $userRequesting)
    {
        $code = $dto->getCode();
        $clients = $this->clientRepo->get(['codigo' => $code]);

        if (empty($clients)) {
            throw new NotFoundException(
                "Información de usuario no encontrada",
                404
            );
        }

        $client = $clients[0];

        $newStatus = !$client->isActive();

        $idsDeleted = $this->clientRepo->toggleActive(
            code: $code,
            active: $newStatus
        );

        $client->setActive($newStatus);

        $privateUrl = $client->getImageUrl();

        if ($privateUrl) {
            $client->setImageUrl(
                $this->storage->signedUrl($privateUrl)
            );
        }

        $now = DateManager::dateToStringFormat(Carbon::now());
        $user = $userRequesting->getUsername();
        $nameClient = $client->getName();

        $idsStr = !empty($idsDeleted)
            ? implode(' ', $idsDeleted)
            : 'ninguna';

        $this->queueService->publish(
            $this->buildMsmAudit(
                action: "El usuario $user modifico el status del cliente $nameClient eliminando las citas con ids $idsStr el día $now",
                userRequesting: $userRequesting
            )
        );
        return [$client->toSerialize()];
    }


    public function getDataClientByHistoryId($request)
    {
        ClientRequest::historyIdValidate($request);

        $client = $this->clientRepo->get(['codigo' => $request['historyId']]);

        if (empty($client)) {
            throw new NotFoundException("Información de usuario no encontrada", 404);
        }
    

        return collect($client)
            ->map(function ($c) {
                $privateUrl = $c->getImageUrl();
                if ($privateUrl) {
                    $c->setImageUrl(
                        $this->storage->signedUrl($privateUrl)
                    );
                }
                return $c->toSerialize();
            })
            ->toArray();
    }
    public function getFullInfoClient(array $request){
        $codHistory=$request['codigo']??null;
        if(!$codHistory){throw new  BadRequestException("No se ha proporcionado un cliente valido",400);}
        $clientInfo = $this->sendQueryToGetFullInfoClient(codigo:$codHistory);
        if (empty($clientInfo)){new NotFoundException("No se ha proporcionado un cliente valido",404);}
        return $this->responseManager->success($clientInfo);

    }

    public function getAuthorizationsByCliencode($clientCode){
        $clientCode=trim($clientCode);
        if (empty($clientCode)) {
            throw new BadRequestException("el parametro de búsqueda debe ser válido",400);
        }

        $Authorizations=$this->getAuthorizationsByClient($clientCode);
        
        if(empty($Authorizations)){
            throw new NotFoundException("El usuario no registra autorizaciones",404);
        }
        $authorizations=$this->attachSpecialtiesWithoutCollapsing(authorizations:$Authorizations);
        
        return $this->responseManager->success($authorizations);
    }
    public function getDataFromAuthorizationCode($authorizationCode){
        $authorizationCode=trim($authorizationCode);

        if (empty($authorizationCode)){
            throw new BadRequestException("el codigo de authorization debe ser un codigo valido",400);
        }
        
        $data=$this->getDataAuthorization($authorizationCode);
        return $this->responseManager->success($data);
    }
    public function GnerateNewPasswordClient($request){
        ClientRequest::ValidateDataToRequestPassword($request);
        $this->CheckWayToSendPasswordIsSelected($request);
        $client=$this->getClientByIdentiy($request);
        $request['clientIdentity']=$client[0]->codigo;
        if(empty($client)){
            throw new NotFoundException('No se han encontrado usuarios',404);
        }

        if($request['sendPasswordToEmail'] && !$client[0]->email){
            throw new BadRequestException("el usuario no registra Email",400);
        }
        if($request['sendPasswordToMobile'] && !$client[0]->thelephoneNumber){
            throw new BadRequestException("el usuario no registra contacto",400);
        }
        $password=PasswordGenerator::generatePasswordNumeric(length:5);
        $clientsUpdate=$this->saveNewPassword($password,$request);
        if(empty($clientsUpdate)){
            throw new NotFoundException("no se ha encontrado usuario a actualizar",404);
        }
        $this->sendMesaggeWithNewPassword($request,$client[0],$password);
        return $this->responseManager->success($client);
    }
    public function setPasswordClient($request){
        ClientRequest::ValidateNewPassword($request);
        $newPassword=$request['password'];
        $oldPassword=$request['oldPassword'];
        $codigo=$request['codigo'];
        $clients=$this->clientRepo->getPwdByCodigo(codigo:$codigo);
        if(empty($clients)){
            throw new BadRequestException("El usuario Porporcionado no es valido o no esxiste en el sistemas",400);
        }
        $client=$clients[0];


        if (!Hash::check($oldPassword, $client->password)) {
            throw new BadRequestException("La contraseña ingresada no es correcta", 400);
        }
        $newPasswordEncrypted = bcrypt($newPassword);
        $clientsUpdated=$this->clientRepo->setPws(codigo:$codigo,newPassword:$newPasswordEncrypted);
        if($clientsUpdated == 0){
            throw new BadRequestException("El codigo de usuario Ingresado no es valido o no se encuentra registrado en nuestro sistema",400);
        }

        return $this->responseManager->success([]);
    }

    public function getHistoryChatBotByClientCod($codigoClient){
        if(empty($codigoClient)){
            throw new BadRequestException("No se ha proporcionado ni un cliente",400);
        }
        $celClient=$this->GetNumberCelClient($codigoClient);
        if (empty($celClient)){
            throw new NotFoundException("El cliente no registra celular",404);
        }
        $celCleaned=$this->cleanerCels($celClient->cel);

        $history=$this->getHistoryWhatsapp($celCleaned);
        if (empty($history)){
            throw new NotFoundException("El usuario no guarda registro de chat",404);
        }
        $historyMapped=$this->MapHistory($history);
        return $this->responseManager->success($historyMapped);
        
    }


    public function getClientsByNumberCel($numberCel){
        $clients=$this->sendQueryTogetClientsByNumberCel($numberCel);
        if(empty($clients)){
            throw new NotFoundException("Clientes no registrados",404);
        }
        return $this->responseManager->success($clients);
    }

    public function getCalendarClient(array $request){
        $codigo=$request['codigo'];
        $from=Carbon::parse($request['from'])->format('Y-m-d');
        $to=Carbon::parse($request['to'])->format('Y-m-d');;
        $calendar=$this->makeQueryToGetCalendar($codigo,$from,$to);
        if(empty($calendar)){
            throw new NotFoundException("El Cliente no registra citas en este periodo de Tiempo",404);
        }
        return $this->responseManager->success($calendar);
    }
    public function getUtilitiesUser(){
        return $this->responseManager->success(
            $this->sendQueryToGetUtilityUser()
        );
    }
    public function updateClient(UpdateUserDto $dto,string $codigo):array{
        ClientRequest::validateDataToUpdateClient($dto->payload());
        $password=$dto->getPwd();
        $clients=$this->clientRepo->getPwdByCodigo(codigo:$codigo);
        if(empty($clients)){
            throw new BadRequestException("El usuario Porporcionado no es valido o no esxiste en el sistemas",400);
        }
        $client=$clients[0];


        if (!Hash::check($password, $client->password)) {
            throw new BadRequestException("La contraseña ingresada no es correcta", 400);
        }
        $clientsUpdated=$this->clientRepo->updateClient(codigo:$codigo,dto:$dto);
        if($clientsUpdated == 0){
            throw new BadRequestException("El codigo de usuario Ingresado no es valido o no se encuentra registrado en nuestro sistema",400);
        }

        return $this->getFullInfoClient(['codigo'=>$codigo]);
    }


    private function getAuthorizationsByClient($clientCode){
        try{
            $authorizations=DB::select("WITH params AS (
                SELECT CAST(? AS VARCHAR(10)) AS historia
            ),

            autorizaciones AS (
                SELECT DISTINCT
                    a.n_autoriza,
                    a.fecha,
                    a.f_vence,
                    a.f_inicial,
                    a.entidad        AS codent,
                    a.paquete        AS codent2,
                    a.observa,
                    a.entidad,
                    a.paquete,
                    a.historia
                FROM autoriza a
                CROSS JOIN params p
                WHERE a.historia = p.historia
                AND a.f_vence   >= CAST(GETDATE() AS DATE)
                AND a.f_inicial <= CAST(GETDATE() AS DATE)
                AND (a.anulada = 0 OR (a.suspendida = 1 AND a.anulada = 1))
                AND a.cerrar_ord_asp <> '1'
                AND NOT EXISTS (
                        SELECT 1
                        FROM ven_det vd
                        WHERE vd.autoriz = a.n_autoriza
                        AND vd.codigo = a.historia
                        AND vd.abierta = '0'
                        AND vd.detalle = ''
                )
            ),

            procedimientos AS (
                SELECT
                    au.n_autoriza,
                    au.procedi        AS tiempo,
                    pr.descrip        AS procedim,
                    au.cantidad,
                    au.cerrar_ord_asp AS cerrado,
                    es.Descripcion    AS especialidad
                FROM autoriza au
                INNER JOIN procdent pr 
                    ON au.procedi = pr.codigo
                LEFT JOIN especialidadAsp es 
                    ON es.id = pr.especialidadAsp
                INNER JOIN autorizaciones a 
                    ON a.n_autoriza = au.n_autoriza
                CROSS JOIN params p
                WHERE au.anulada = '0'
                AND au.historia = p.historia
                AND pr.cod_enti = (
                        SELECT TOP 1 en.tarifa
                        FROM entidades en
                        WHERE en.admini = au.entidad
                )

                UNION ALL

                SELECT
                    ad.n_autoriza,
                    ad.procedi     AS tiempo,
                    ad.nombre      AS procedim,
                    ad.cantidad,
                    '0'            AS cerrado,
                    es.Descripcion AS especialidad
                FROM autorizad ad
                INNER JOIN procdent pr 
                    ON ad.procedi = pr.codigo
                LEFT JOIN especialidadAsp es 
                    ON es.id = pr.especialidadAsp
                INNER JOIN autorizaciones a 
                    ON a.n_autoriza = ad.n_autoriza
                WHERE ad.anulada = '0'
            ),

            contador AS (
                SELECT
                    p.n_autoriza,
                    p.tiempo,
                    COUNT(*) AS total_programadas
                FROM procedimientos p
                INNER JOIN citas c 
                    ON c.tiempo  = p.tiempo
                AND c.autoriz = p.n_autoriza
                CROSS JOIN params pa
                WHERE c.cancelada <> '1'
                AND c.na        <> '1'
                AND c.nro_hist  = pa.historia
                AND NOT EXISTS (
                        SELECT 1
                        FROM procedipro pp
                        WHERE pp.nombre  = c.procedipro
                        AND pp.sumable = '0'
                )
                GROUP BY p.n_autoriza, p.tiempo
            ),

            entidades AS (
                SELECT *
                FROM cliente
                WHERE socie <> ''
            ),

            citas_por_autoriz AS (
                SELECT autoriz, COUNT(*) AS total
                FROM citas
                GROUP BY autoriz
            )

            SELECT
                p.n_autoriza,
                p.tiempo,
                p.procedim,
                p.cantidad,
                p.cerrado,
                ISNULL(p.especialidad, 'No Encontrada') AS especialidad,
                a.f_vence,
                a.f_inicial,
                a.observa,
                a.paquete        AS convenio,
                a.entidad        AS cod_entidad,
                ISNULL(c.total_programadas, 0) AS total_programadas,
                p.cantidad - ISNULL(c.total_programadas, 0) AS disponibles,
                e.nombre         AS entidad,
                CASE 
                    WHEN ISNULL(ca.total, 0) > 0 THEN '0'
                    ELSE '1'
                END AS nueva
            FROM procedimientos p
            LEFT JOIN contador c
                ON c.n_autoriza = p.n_autoriza
                AND c.tiempo     = p.tiempo
            INNER JOIN autorizaciones a
                    ON a.n_autoriza = p.n_autoriza
            INNER JOIN entidades e
                    ON e.codigo = a.entidad
            LEFT JOIN citas_por_autoriz ca
                ON ca.autoriz = a.n_autoriza
            ORDER BY a.f_inicial, a.f_vence;
            ",[$clientCode]);
            return $authorizations;
        }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);        }
    }
    private function getDataAuthorization($authorizationCode){
        try{
            $dataAuthorization=DB::select(
                "SELECT au.n_autoriza, au.procedi AS tiempo, pro.descrip AS procedim, au.cantidad
                FROM autoriza au
                INNER JOIN procdent pro ON au.procedi = pro.codigo  
                WHERE n_autoriza = ?
                AND pro.cod_enti = (
                    SELECT TOP 1 tarifa
                    FROM entidades en
                    INNER JOIN procdent pro ON en.tarifa = pro.cod_enti
                    WHERE en.admini = au.entidad
                    GROUP BY tarifa
                )
                UNION ALL
                SELECT n_autoriza, procedi AS tiempo, nombre AS procedim, cantidad
                FROM autorizad
                WHERE n_autoriza = ?
                ",[$authorizationCode,$authorizationCode]
            );
            return $dataAuthorization;
        }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);
        }
    }
    



    private function CheckWayToSendPasswordIsSelected($request){
        $emailMean=$request['sendPasswordToEmail'];
        $mobileMean=$request['sendPasswordToMobile'];

        if(!$emailMean && !$mobileMean){
            throw new BadRequestException('No se ha seleccionado un medio de envio del mensaje',400);
        }
    }
    private function getClientByIdentiy($request)
    {

        $identityClient = $request['clientIdentity'];
    
        try {
            $client = DB::select("
                SELECT TOP 1 
                nombre,
                codigo,
                email as email,
                cel AS thelephoneNumber
                FROM cliente
                WHERE nit_cli = ? 
                AND ok_ent != 1
            ", [$identityClient]);
    
            return $client;
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }
    private function saveNewPassword(string $newPassword, array $request)
    {   

        $codigoClient = $request['clientIdentity'];
         
        $newPasswordEncrypted = bcrypt($newPassword);
    
        try {
            $clientsWithNewPassword = DB::update(
                "
                UPDATE cliente2 
                SET user_password_mc = ?
                WHERE codigo = ?
                ",
                [
                    $newPasswordEncrypted,
                    $codigoClient
                ]
            );
    
            return $clientsWithNewPassword;
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }
    
    
    private function sendMesaggeWithNewPassword(Array $request,$client,string $newPassword){
        $sendToEmail=$request['sendPasswordToEmail'];
        $email=$client->email;
        $telephoneNumber=$client->thelephoneNumber;
        $nameClient=$client->nombre;

        $sendToEmail?$this->sendRecoveryEmail($nameClient,$newPassword,$email):
                    $this->sendRecoveryWhatsapp($nameClient,$newPassword,$telephoneNumber);


    }
    private function sendRecoveryEmail($username, $newPassword, $email) {
        if (!$this->emailService->sendEmail($username, $newPassword, $email)) {
            throw new ServerErrorException("No fue posible conectar con el servicio de email", 500);
        }
    }
    private function sendRecoveryWhatsapp($username,$newPassword,$thelephoneNumber){
        $telephoneNumberCleaned=$this->cleanThelphoneNumber($thelephoneNumber);
        $payload=$this->buildPayload($username,$newPassword,$telephoneNumberCleaned);
        $this->whatsappService->sendMessageToRetrievePassword($payload);

    }
    private function cleanThelphoneNumber(string $telephoneNumber){
        return substr($telephoneNumber, 0, 10);
    }
    private function buildPayload($username,$newPassword,$thelephoneNumber){
        return [

            'telephoneNumber' => $thelephoneNumber,
            'password'=>$newPassword,
            'clientName'=>$username
        ];
    }

    private function GetNumberCelClient($codigoClient){
        try{
            $cel=DB::select(
                "
                    select cel from cliente where codigo= ?
                ",[$codigoClient]
            );
            return $cel[0];
        }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);
        }
    }
    private function cleanerCels($dirttyCel){
        $celCleaned= CelNumberManager::chooseTelephoneNumber($dirttyCel);
        if(empty($celCleaned)){
            throw new NotFoundException("El usuario no registra numero de celular valido",404);
        }
        return $celCleaned;
    }
    private function getHistoryWhatsapp($cel){
        try{
            return $this->whatsappService->getHistoryWhatsapCel($cel);
        }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);
        }
    }
    private function MapHistory($history){
        return HistoryChatBotMapper::map($history);
    }


    public function sendQueryTogetClientsByNumberCel($numberCel){
        try {
            $clients = DB::select("
                SELECT 
                    LTRIM(RTRIM(cli.codigo)) AS codigo, 
                    LTRIM(RTRIM(cli.nombre)) AS nombre , 
                    LTRIM(RTRIM(cli.nit_cli)) AS cedula, 
                    LTRIM(RTRIM(ent.clase)) AS entidad
                FROM cliente cli 
                INNER JOIN entidades ent ON ent.codigo = cli.codent2 
                WHERE LTRIM(RTRIM(cli.cel)) LIKE ?
            ", ["%$numberCel%"]);
    
            return $clients;
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }
    private function makeQueryToGetCalendar($codigo,$from,$to){
        try{
            $clientCalendar=DB::select("WITH citas_con_fecha AS (
                    SELECT 
                            id,
                            CAST(fecha AS datetime) + CAST(hora AS time) AS fecha_completa,
                            autoriz,
                            tiempo,
                            nro_hist
                        FROM citas
                        WHERE nro_hist = ?
                            AND fecha BETWEEN CONVERT(smalldatetime, ?, 120) 
                            AND CONVERT(smalldatetime, ?, 120)
                    )
                    SELECT
                        cif.id, 
                        cif.fecha_completa,
                        DATEADD(MINUTE, pro.duraccion, cif.fecha_completa) AS hora_fin,
                        cif.nro_hist,
                        cif.tiempo,
                        ci.hora,
                        RTRIM(cli.nombre) AS usuario,
                        DATEDIFF(YEAR, cli.f_nacio, GETDATE()) AS edad,
                        ci.procedipro AS procedimiento,
                        ci.autoriz AS autorizacion,
                        ci.asistio,
                        ci.cancelada,
                        ci.procedim,
                        ci.direccion_cita AS direccion,
                        RTRIM(em.enombre) AS profesional,
                        ci.registro,
                        ci.fec_hora,
                        CASE
                            WHEN ci.fecha_evo_ampliada = '0'
                                AND CAST(cif.fecha_completa AS date) < CAST(GETDATE() AS date)
                                AND ci.asistio != '1'
                                AND ci.cancelada != '1'
                            THEN '1'
                            ELSE '0'
                        END AS no_asistida,
                        pro.tipo_evolucion,
                        ci.realizar AS razon_cancelamiento,
                        CONVERT(VARCHAR(5), DATEADD(MINUTE, pro.duraccion, cif.fecha_completa), 108)
                            + CASE 
                                WHEN DATEPART(HOUR, DATEADD(MINUTE, pro.duraccion, cif.fecha_completa)) >= 12 
                                    THEN ' PM'
                                ELSE ' AM'
                            END AS fin,
                        CASE
                            WHEN ci.asistio = '1' OR ci.cancelada = '1'
                                THEN '0'
                            WHEN ci.fecha_evo_ampliada = '1'
                                AND CAST(cif.fecha_completa AS date) < CAST(GETDATE() AS date)
                                THEN '1'
                            ELSE '0'
                        END AS evolucionable,
                        pro.id_programa,
                        pro.multi_evol AS multi_evo,
                        pro.consulta,
                        pro.duraccion AS duracion

                    FROM citas ci
                    INNER JOIN cliente cli ON ci.nro_hist = cli.codigo
                    INNER JOIN citas_con_fecha cif ON ci.id = cif.id
                    INNER JOIN procedipro pro ON pro.nombre = ci.procedipro
                    INNER JOIN emplea em ON em.ecc = ci.cedprof
                    ORDER BY cif.fecha_completa, CAST(ci.hora AS TIME)",[$codigo,$from,$to]);
                return $clientCalendar;
            }catch(\Exception $e ){
                throw new ServerErrorException($e->getMessage(),500);
            }
    }
    private function sendQueryToGetFullInfoClient(string $codigo){
        try{
            $fullInfoClient=DB::select("SELECT 
                RTRIM(cli.direcc) AS direccion,
                RTRIM(cli.barrio) AS barrio,
                RTRIM(cli.cel) AS celular,
                RTRIM(mun.nombre) AS municipio,
                RTRIM(ocupa.descrip) AS ocupacion,
                RTRIM(cli.email) AS email,

                RTRIM(cli.telacompañante) AS celular_responsable,
                RTRIM(cli.nombreresponsable) AS responsable,
                RTRIM(cli.parentresponsable) AS parentezco_responsable


            FROM 
                cliente cli
            INNER JOIN 
                entidades ent ON ent.codigo = cli.codent2
            INNER JOIN 
                municipio mun ON mun.codigo = cli.cod_ciudad
            INNER JOIN CIUO ocupa on ocupa.cod = cli.ocupacion

            WHERE 
                cli.codigo = ?",[$codigo]);
            return $fullInfoClient;
            }catch(\Exception $e ){
                throw new ServerErrorException($e->getMessage(),500);
            }
    }
    public function sendQueryToGetUtilityUser(){
        return DB::select(query:"SELECT tipo as cod,
                            documento AS nombre,
                            'documento' as tipo,
                            NULL AS referencia,
                            NULL AS cod_referencia
                            FROM tipo_doc

                            UNION ALL 

                            select codigo AS cod,
                            tipodiag AS nombre,
                            'regimen' AS tipo,
                            NULL AS referencia,
                            NULL AS cod_referencia
                            from tipodiag  
                            where codigo_SISPRO <> ''

                            UNION ALL
                            
                            SELECT codigo AS cod,
                                RTRIM(nombre) as nombre,
                                'entidad' AS tipo,
                                NULL AS referencia,
                                NULL AS cod_referencia
                                from cliente where
                                ok_ent <> 0
                                and particu = '0'
                                and activo ='1'
                                and cod_con = '1'

                            UNION ALL 

                            select ent.codigo as cod,
                            RTRIM(ent.clase) AS nombre, 
                            'convenio' AS tipo,
                            RTRIM(cli.nombre) as referencia,
                            cli.codigo as cod_referencia
                            FROM entidades ent
                            INNER JOIN cliente cli ON cli.codigo =  ent.admini
                                                    AND cli.ok_ent <> 0
                                                    and cli.particu = '0'
                                                    and cli.activo ='1'
                                                    and cli.cod_con = '1'

                            union all 
                            SELECT sigla as cod,
                            nombre,
                            'sexo' as tipo,
                            NULL AS referencia,
                            NULL AS cod_referencia
                            FROM sexoAsp
                            --

                            union all 
                            SELECT CAST(id AS varchar) as cod,
                            nombre,
                            'tipo_usuario' as tipo,
                            NULL AS referencia,
                            NULL AS cod_referencia
                            FROM tipo_usuario_asp

                            union all 
                            SELECT CAST(id AS VARCHAR) as cod,
                            escolaridad AS nombre,
                            'escolaridad' as tipo,
                            NULL AS referencia,
                            NULL AS cod_referencia
                            FROM escolaridad

                            union all 
                            SELECT CAST(codigo as varchar) as cod,
                                zona AS nombre,
                                'zona' as tipo,
                                NULL AS referencia,
                                NULL AS cod_referencia
                            FROM zonas
                            UNION ALL
                            SELECT 
                            cod,
                            REPLACE(REPLACE(descrip, CHAR(13), ''), CHAR(10), '') AS nombre,
                            'ocupacion' AS tipo,
                            NULL AS referencia,
                            NULL AS cod_referencia
                            FROM CIUO
                            WHERE LEN(cod) =4

                            UNION ALL

                            
							SELECT 
								RTRIM(codigo) AS cod,
								nombre,
								'municipio' AS tipo,
								NULL AS referencia,
								NULL AS cod_referencia
							FROM municipio
                            UNION ALL
                            SELECT 
                            codigo AS cod,
                            parentezco AS nombre, 
                            'parentezco' AS tipo ,
                            NULL AS referencia,
                            NULL AS cod_referencia
                            FROM parentezco

                            UNION ALL
                            SELECT 
                                CAST(id AS VARCHAR) AS cod,
                                est_civil AS nombre, 
                                'estado_civil' AS tipo ,
                                NULL AS referencia,
                                NULL AS cod_referencia
                            FROM est_civil
                            UNION ALL
                                SELECT CAST(id AS VARCHAR) AS cod,
                                grupo AS nombre,
                                'poblacion' as tipo,
                                NULL AS referencia,
                                NULL AS cod_referencia
                            from grupo_poblacional
                            where activo = '1'
                            UNION ALL
                                SELECT CAST(id AS VARCHAR) AS cod,
                                etnia AS nombre,
                                'etnia' as tipo,
                                NULL AS referencia,
                                NULL AS cod_referencia
                            from etnias_asp
                            UNION ALL
                                SELECT CAST(codigo AS VARCHAR) AS cod,
                                    discapacidad AS nombre,
                                    'discapacidad' as tipo,
                                    NULL AS referencia,
                                    NULL AS cod_referencia
                                FROM tipo_discapacidad
                            UNION ALL
                                SELECT CAST(CODIGO AS VARCHAR) AS cod,
                                NOMBRE AS nombre,
                                'pais' as tipo,
                                NULL AS referencia,
                                NULL AS cod_referencia
                            from paises
                            UNION ALL 
                                SELECT CAST(id AS VARCHAR) AS cod,
                                grupo AS nombre,
                                'grupo' AS tipo,
                                NULL AS referencia,
                                NULL AS cod_referencia
                            FROM grupo_sisben_asp
                            ORDER BY tipo,nombre");
    }
    private function attachSpecialtiesWithoutCollapsing(array $authorizations): array
    {
        $specialtiesByAuth = collect($authorizations)
            ->groupBy('n_autoriza')
            ->map(fn ($items) =>
                $items->pluck('especialidad')->filter()->unique()->values()->all()
            );

        return collect($authorizations)
            ->map(function ($item) use ($specialtiesByAuth) {
                $item->especialidades = $specialtiesByAuth[$item->n_autoriza] ?? [];
                return $item;
            })
            ->all();
    }
    private function buildMsmAudit(string $action,UserRequesting $userRequesting,string $modulo = 'citas'):MessageQueue{
        return AuditMessageQueueBuilder::create()->withData([
            'audit'=>$action,
            'cedula'=>$userRequesting->getCedula(),
            'modulo'=>$modulo
        ])->build();
    }
    public  function getByClientCode(string $clientCode):ClientView{
        $client=$this->clientRepo->get(['codigo'=>$clientCode]);
        if(empty($client)){
            throw new NotFoundException("No se ha encontrado un cliente con el codigo proporcionado",404);
        }
        return $client[0];
    }
    
    
}
    
    
    
    
    
