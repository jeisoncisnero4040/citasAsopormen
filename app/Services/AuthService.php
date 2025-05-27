<?php
namespace App\Services;

use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Interfaces\AuthInterface;
use App\utils\PasswordGenerator;
use App\Utils\ResponseManager;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthService extends BaseService{
    protected ResponseManager $responseManager;
    private AuthInterface $authRepository;
    private JwtService $jwtService;
    private EmailService $emailService;

    public function __construct(ResponseManager $responseManager,AuthInterface $authRepository,JwtService $jwtService,EmailService $emailService)
    {
        $this->responseManager=$responseManager;
        $this->authRepository=$authRepository;
        $this->jwtService=$jwtService;
        $this->emailService=$emailService;
    }
    public function login($request){
        //validarLogin

        $user =User::where('cedula', $request['cedula'])
        ->select('cedula','usuario','password','estado','permisomc','rol_id')
        ->first();


        if(!$user){
            throw new NotFoundException("Usuario no Encontrado o Documento Incorrecto",404);
        }
        if ($user->estado=='INACTIVO'){
            throw new BadRequestException( "El usuario no se encuentra activo",400);
        }
        if ($user->permisomc=='0'){
            throw new BadRequestException( "El usuario no tiene permisos para esta acción",400);
        }
        if(!Hash::check($request['password'], $user->password)){
            throw new BadRequestException("contraseña incorrecta",400);
        }
        $token=$this->jwtService->generateToken($user);
        return $this->responseManager->success(['user'=>$user,'token'=>$token]);
    }
    public function recoverPassword($request) {
        //$this->validateRequest($request);
        $employe= $this->authRepository->getEmailByUserCedula($request['cedula'])[0];
        $this->validateEmployeeEmail($employe, $request['email']);
        $newPassword = PasswordGenerator::generatePassword();
        $user=$this->authRepository->login($employe->ecc,$newPassword);
        $this->updateUserPassword($user, $newPassword);
        $this->sendRecoveryEmail($user->usuario, $newPassword,$employe->email);

        return $this->responseManager->success($user);
    }
        
    public function updatePasswordByUserCedula($request)
    {
        $cedula = $request['cedula'];
        $oldPassword = $request['oldPassword'];
        $newPassword = $request['newPassword'];

        $user = $this->authRepository->login($cedula, $oldPassword);

        if (!$user) {
            throw new NotFoundException("Usuario no Encontrado o Documento Incorrecto", 404);
        }

        if ($user->estado === 'INACTIVO') {
            throw new BadRequestException("El usuario no se encuentra activo", 400);
        }

        if ($user->permisomc === '0') {
            throw new BadRequestException("El usuario no tiene permisos para esta acción", 400);
        }

        if (!Hash::check($oldPassword, $user->password)) {
            throw new BadRequestException("Contraseña actual incorrecta", 400);
        }

        $updateResult = $this->updateUserPassword($user, $newPassword);

        return $this->responseManager->success($updateResult);
    }
    private function updateUserPassword($user, $newPassword) {
        $newPasswordEncrypted=bcrypt($newPassword);
        try {
            $user=$this->authRepository->changePasswordUser($user->cedula,$newPasswordEncrypted);
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }
    private function validateEmployeeEmail($employee, $emailInput) {
    if ($employee->email != $emailInput) {
        throw new BadRequestException('Correo electrónico no registrado', 400);
        }
    }
    private function sendRecoveryEmail($username, $newPassword, $email) {
        if (!$this->emailService->sendEmail($username, $newPassword, $email)) {
            throw new ServerErrorException("No fue posible conectar con el servicio de email", 500);
        }
    }
}