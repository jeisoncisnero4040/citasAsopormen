<?php
namespace App\Services;

use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Services\EmailService;
use App\Models\EmployeeModel;
use App\Models\User;
use App\Requests\UserRequest;
use App\utils\ResponseManager;
use App\utils\PasswordGenerator;
use Illuminate\Support\Facades\DB;

class UserService {
    private $emailService;
    private $userModel;
    private $employeeModel;
    private $responseManager;

    public function __construct(
        EmailService $emailService, 
        User $userModel, 
        EmployeeModel $employeeModel, 
        ResponseManager $responseManager
    ) {
        $this->emailService = $emailService;
        $this->userModel = $userModel;
        $this->employeeModel = $employeeModel;
        $this->responseManager = $responseManager;
    }

    public function recoverPassword($request) {
        $this->validateRequest($request);
        $employee = $this->getEmployeeByCedula($request['cedula']);
        $this->validateEmployeeEmail($employee, $request['email']);
        $newPassword = PasswordGenerator::generatePassword();
        $user=$this->getUserByCedula($request['cedula']);
        $userUpdated = $this->updateUserPassword($user, $newPassword);
        $this->sendRecoveryEmail($user->usuario, $newPassword, 'jecsnroxf@gmail.com');

        return $this->responseManager->success(null);
    }

    public function UpdatePasswordByUserCedula($request){
        
        UserRequest::validatePasswordAndCedula($request);
        
        $cedula=$request['cedula'];
        $oldPassword=$request['oldPassword'];
        $newPassword=$request['newPassword'];

        $user=$this->getUserByCedula($cedula);
        $this->validatePassword($user, $oldPassword);
        $userUpdate=$this->updateUserPassword($user,$newPassword);

        return $this->responseManager->success($userUpdate);
    }

    private function validateRequest($request) {
        UserRequest::validateEmailAndCedula($request);
        
    }
 

    private function getEmployeeByCedula($cedula) {
        $employee = $this->employeeModel::select('ecc', 'email')->where('ecc', $cedula)->first();

        if (empty($employee)) {
            throw new NotFoundException("Usuario no encontrado", 404);
        }

        return $employee;
    }
    private function getUserByCedula($cedula){
        try {
            $user = $this->userModel::select('cedula','password')->where("cedula", $cedula)->first();

            if (!$user) {
                throw new NotFoundException("Usuario no encontrado", 404);
            }
            return $user;
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }

    private function validateEmployeeEmail($employee, $emailInput) {
        if ($employee->email != $emailInput) {
            throw new BadRequestException('Correo electrónico no registrado', 400);
        }
    }
    private function validatePassword($user,$oldPassword){
        if($user->password != $oldPassword){
            throw new BadRequestException("por favor verifica la contraseña",400);
        }
    }

    private function updateUserPassword($user, $newPassword) {
        try {
            $user=DB::update("update usuarios set password = ? where cedula = ?",[$newPassword,$user->cedula]);
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }

    private function sendRecoveryEmail($username, $newPassword, $email) {
        if (!$this->emailService->sendEmail($username, $newPassword, $email)) {
            throw new ServerErrorException("No fue posible conectar con el servicio de email", 500);
        }
    }
}

