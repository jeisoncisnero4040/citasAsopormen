<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfesionalController;
use App\Http\Controllers\AppoimentController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuthsController;
use App\Http\Controllers\UtilitiesController;
use App\Http\Controllers\BufferController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\EvoController;
use App\Http\Controllers\PrometheusController;
use App\Http\Controllers\RolesAndPermissionsController;
use App\Http\Controllers\SignaturesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/logout',[AuthController::class, 'logout']);
Route::post('auth/refresh',[AuthController::class, 'refresh']);
Route::post('auth/me',[AuthController::class, 'me']);
Route::post('auth/forgot-password',[AuthController::class, 'forgotPassword']);
Route::post('auth/change-password',[AuthController::class,'changePassword']);
Route::post('auth/add-pass',[AuthController::class,'defaultPass']);

Route::get('profesional/get-daily-schedule',[ProfesionalController::class,'getDaylSchedule'])->middleware('login.check:agenda');
Route::get('profesional/get-schedule',[ProfesionalController::class,'getSchedule'])->middleware('login.check:agenda');
Route::get('profesional/get-auths',[ProfesionalController::class,'getAuthsProfesional'])->middleware('login.check:agenda');
Route::get('profesional/search',[ProfesionalController::class,'searchProfesionales']);
Route::post('profesional',[ProfesionalController::class,'updateProfesional']);

Route::patch('appoiments/cancel',[AppoimentController::class,'cancelAppoiment'])->middleware('login.check:agenda');
Route::post('appoiments/evo-fono',[AppoimentController::class,'evoFono'])->middleware('login.check:agenda');
Route::post('appoiments/evo-psico',[AppoimentController::class,'evoPsico'])->middleware('login.check:agenda');
Route::post('appoiments/evo-ABA',[AppoimentController::class,'evoABA'])->middleware('login.check:agenda');
Route::get('appoiments/dispo-info',[AppoimentController::class,'getInfoDispoAppoById'])->middleware('login.check:agenda');
Route::get('appoiments/dx-historico',[AppoimentController::class,'getHistAppoId'])->middleware('login.check:agenda');
Route::patch('appoiments/opoen-past',[AppoimentController::class,'openPastAppos']);
Route::get('appoiments/get-auths-avaibles',[AppoimentController::class,'getAuthsToChange'])->middleware('login.check:agenda');
Route::patch('/appoiments/{id}/set-auth', [AppoimentController::class, 'changeAuthAppo'])->middleware('login.check:agenda');


Route::patch('/auths/close',[AuthsController::class,'closeAuth'])->middleware('login.check:agenda');

Route::get('/utilities/evolution', [UtilitiesController::class, 'getEvoUtilities'])->middleware('login.check:agenda');
Route::get('/utilities/search-dx', [UtilitiesController::class, 'searchDx'])->middleware('login.check:agenda');

Route::get('/buffer',[BufferController::class,'getAll'])->middleware('login.check:agenda');
Route::get('/buffer/get-evo-data',[BufferController::class,'getEvo'])->middleware('login.check:agenda');

Route::get('/roles-permissions/get-all-permissions',[RolesAndPermissionsController::class,'getAllPermissions']);
Route::get('/roles-permissions/get-all-roles',[RolesAndPermissionsController::class,'getAllRoles']);
Route::post('/roles-permissions/create-rol',[RolesAndPermissionsController::class,'storeRole']);
Route::post('/roles-permissions/create-permission',[RolesAndPermissionsController::class,'storePermission']);
Route::get('/roles-permissions/all',[RolesAndPermissionsController::class,'all']);
Route::put('/roles-permissions/set-permissions-rol',[RolesAndPermissionsController::class,'setPermissionRoles']);
Route::delete('/roles-permissions/delete/{id}',[RolesAndPermissionsController::class,'deleteRol']);
Route::patch('/roles-permissions/activate/{id}',[RolesAndPermissionsController::class,'changueActiveRole']);
Route::patch('/roles-permissions/activate-permission/{id}',[RolesAndPermissionsController::class,'toggleActivePermission']);
Route::delete('/roles-permissions/delete-permission/{id}',[RolesAndPermissionsController::class,'deletePermission']);

Route::get('/evo/print/get-procedipros',[EvoController::class,'getProcediproToPrint'])->middleware('login.check:agenda');
Route::get('/evo',[EvoController::class,'getEvo'])->middleware('login.check:agenda');

Route::get('clients/search',[ClientController::class,'searchClient'])->middleware('login.check:agenda');
Route::get('clients/{code}',[ClientController::class,'find'])->middleware('login.check:agenda');
Route::get('clients',[ClientController::class,'getInfoClient'])->middleware('login.check:agenda');

Route::get('/firmas/{path}', [SignaturesController::class, 'serve'])
     ->where('path', '.*')
     ->middleware('login.check:agenda')
     ->name('firmas.serve');

Route::post('audit/create-print-evos-audit',[AuditController::class,'savePrintEvosAudit']);

Route::get('metrics',[PrometheusController::class,'metrics']);