<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\BeritaController;
use App\Http\Controllers\Api\IklanController;
use App\Http\Controllers\Api\AcaraController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\HomeOurExpertise1Controller;
use App\Http\Controllers\Api\HomeOurExpertise2Controller;
use App\Http\Controllers\Api\HomeSliderController;
use App\Http\Controllers\Api\HomeWhoWeAreController;
use App\Http\Controllers\Api\OurProgramsController;
use App\Http\Controllers\Api\RecentTrailerController;
use App\Http\Controllers\Api\SeputarDinusSliderController;
use App\Http\Controllers\Api\SeputarDinusSidebarBannerController;
use App\Http\Controllers\Api\SeputarDinusSlidesTitleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JadwalAcaraController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::apiResource('kategori', KategoriController::class);
Route::apiResource('berita', BeritaController::class);
Route::patch('berita/{id}', [BeritaController::class, 'update']);
Route::apiResource('iklan', IklanController::class);
Route::patch('iklan/{id}', [IklanController::class, 'update']);
Route::apiResource('acara', AcaraController::class);
Route::patch('acara/{id}', [AcaraController::class, 'update']);
Route::apiResource('jadwal-acara', JadwalAcaraController::class);
Route::apiResource('program', ProgramController::class);
Route::apiResource('our-programs', OurProgramsController::class);
Route::patch('our-programs/{id}', [OurProgramsController::class, 'update']);
Route::apiResource('recent-trailer', RecentTrailerController::class);
Route::patch('recent-trailer/{id}', [RecentTrailerController::class, 'update']);
Route::apiResource('seputar-dinus-slider', SeputarDinusSliderController::class);
Route::apiResource('seputar-dinus-sidebar-banner', SeputarDinusSidebarBannerController::class);
Route::apiResource('seputar-dinus-slides-title', SeputarDinusSlidesTitleController::class);
Route::apiResource('users', UserController::class);

Route::prefix('home')->group(function () {
    Route::apiResource('our-expertise1', HomeOurExpertise1Controller::class);
    Route::apiResource('our-expertise2', HomeOurExpertise2Controller::class);
    Route::apiResource('slider', HomeSliderController::class);
    Route::apiResource('who-we-are', HomeWhoWeAreController::class);
});

Route::post('/oauth/token', [AccessTokenController::class, 'issueToken'])
    ->middleware(['throttle'])
    ->name('passport.token');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/validate-token', [AuthController::class, 'validateToken']);
});

Route::prefix('roles')->controller(RoleController::class)->group(function () {
    Route::post('/create', 'storeRole');
    Route::post('/permission/assign', 'givePermissionToRole');
    Route::delete('/delete/{id}', 'destroyRole');
    Route::get('/', 'index');
});

Route::prefix('permissions')->controller(PermissionController::class)->group(function () {
    Route::post('/create', 'storePermission');
    Route::get('/', 'index');
    Route::put('/{permission}', 'update');
    Route::delete('/{permission}', 'destroy');
});

Route::middleware(['cookie.token', 'auth:api'])->get('/cookie', function () {
    return response()->json(Auth::user());
});