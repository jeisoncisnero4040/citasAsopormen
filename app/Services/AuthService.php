<?php

namespace App\Services;

use App\Constants\AuditTemplates;
use App\Events\AuditEvent;
use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Interfaces\JwtInterface;
use App\Interfaces\ProfesionalRepositoryInterface;
use App\Interfaces\RolesAndPermissionsRepositoryInterface;
use App\Requests\AuthRequests;
use App\Utils\PasswordGenerator;
use App\Utils\ResponseManager;
use Illuminate\Support\Facades\Hash;
use App\Jobs\SendNewPasswordEmail;
use App\Utils\DateManager;

class AuthService extends BaseService
{
    private ProfesionalRepositoryInterface $profesionalRepository;
    protected ResponseManager $responseManager;
    private JwtInterface $jwt;


    public function __construct(
        ProfesionalRepositoryInterface $profesionalRepository,
        ResponseManager $responseManager,
        JwtInterface $jwt,

    ) {
        $this->profesionalRepository = $profesionalRepository;
        $this->responseManager = $responseManager;
        $this->jwt=$jwt;

    }

    public function login(array $requests,$ip): array
    {
        AuthRequests::validateLoginData($requests);

        $cedula = $requests['cedula'];
        $password = $requests['password'];
        $rol=$requests['rol'];


        $profesional = $this->profesionalRepository->getProfesionalByIdentity(identityNumber: $cedula);

        if (!Hash::check($password, $profesional->getPassword())) {
            throw new BadRequestException("Credenciales incorrectas", 400);
        }
        if (!$profesional->isActive()) {
            throw new BadRequestException("El profesional no está activo en la plataforma", 400);
        }

        if (!$profesional->hasAgend()) {
            throw new BadRequestException("El profesional no tiene permisos de agendamiento", 400);
        }
        $subRols=$profesional->getSubRols();
        if(!in_array($rol,$subRols)){
            throw new BadRequestException("El usuario no tiene permisos para ingresar a este modulo", 400);
        }


        $token=$this->jwt->generateToken($profesional);
        $messageAudit=str_replace(
            search:AuditTemplates::VARS_LOGIN_AUDIT,
            replace:[$profesional->getNombre(),DateManager::nowInLargeFormat(),$ip],
            subject:AuditTemplates::LOGIN_AUDIT
        );
        event(new AuditEvent($messageAudit));
        return $this->responseManager->success([
            'token' => $token,
            'profesional'=>$profesional->toList()
        ]);
    }
    public function validateTokenRequest($token){
        $this->jwt->validateToken($token);
    }
    public function logout ($request)  {
 
            $token = $request->header('Authorization');
            if (!$token) {
                return $this->responseManager->success('logout exitoso',200);
            }
            $token = str_replace('Bearer ', '', $token);
            try {
                $this->jwt->invalidateToken($token);
                return $this->responseManager->success('logout exitoso');
            } 
            catch (\Exception $e) {
                throw new ServerErrorException($e->getMessage(),500);
            }
        
    }

    public function refresh($token){ 
        try {
            $newToken = $this->jwt->refreshToken($token);
            return $newToken;
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(),500);
        }
    }
    public function me($token) {
        try {
            return $this->jwt->getUserByToken($token);
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }
    public function changePassword($request){
        AuthRequests::validateChangePassword($request);
        $cedula=$request['cedula'];
        $newPassword=$request['password'];
        $firstChange=$request['first_change'];
        $oldPassword=$request['oldPassword']??null;
        $newPasswordEncrypted=bcrypt($newPassword);
        $profesional=$this->profesionalRepository->getProfesionalByIdentity($cedula);

        if($oldPassword && !Hash::check($oldPassword, $profesional->getPassword())){
            throw new BadRequestException("La contraseña nueva debe coincidar con la anterior", 400);
        }
        $this->updatePassword(newPassword:$newPasswordEncrypted,cedula:$cedula,firstChange:$firstChange);
        $msmAudit=str_replace(
            search:AuditTemplates::VARS_CHANGE_PASSWORD,
            replace:[$profesional->getNombre(),DateManager::nowInLargeFormat()],
            subject:AuditTemplates::CHANGE_PASSWORD
        );
        event(new AuditEvent(auditMessage:$msmAudit));
        return $this->responseManager->success($profesional->toList());
    }
    public function forgotPassword($request){
        $cedula=$request['cedula'];
        $firstChange=false;
        $profesional=$this->profesionalRepository->getProfesionalByIdentity(identityNumber:$cedula);
        if(!$profesional->passwordChanged()){
            throw new BadRequestException("Esta opción no esta disponible hasta que realices un primer inicio de sesión",400);
        }
        $email=$profesional->getEmail();
        if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new BadRequestException("La dirección de correo electronico no existe o mo es una direccion valida",400);
        }
        $newPassword=PasswordGenerator::generatePassword();
        $newPasswordEncrypted=bcrypt($newPassword);
        $this->updatePassword(newPassword:$newPasswordEncrypted,cedula:$cedula,firstChange:$firstChange);
        SendNewPasswordEmail::dispatch($email, $newPassword,$profesional->getNombre());
        $msmAudit = str_replace(
            AuditTemplates::VARS_FORGOT_PASSWORD_AUDIT,
            [
                $profesional->getNombre(),
                $profesional->getEmail(),
                DateManager::nowInLargeFormat()
            ],
            AuditTemplates::FORGOT_PASSWORD_AUDIT
        );
        event(new AuditEvent(auditMessage:$msmAudit));
        return $this->responseManager->success(['email'=>$email]);

    }
    private function updatePassword($newPassword,$cedula,$firstChange){
        $passwordUpdated= $this->profesionalRepository->changePasswordProfesional(cedula:$cedula,newPassword:$newPassword,firstChange:$firstChange);
        $this->driveResponse($passwordUpdated,"Usuario con la cedula proporcionada ");
    }
    public function defaultPasswords(array $request)
    {
        $users = $request['users'];

        collect($users)->map(function ($user) {
            $password = bcrypt("user{$user}");
            $this->updatePassword($password, $user, true);
        });
        return $this->responseManager->success(count($users));
    }

}
