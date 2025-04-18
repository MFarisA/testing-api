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
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('users', UserController::class);
});


Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logoutUser']);

Route::apiResource('kategori', KategoriController::class);
Route::apiResource('berita', BeritaController::class);
Route::apiResource('iklan', IklanController::class);
Route::apiResource('acara', AcaraController::class);
Route::apiResource('program', ProgramController::class);
Route::apiResource('our-programs', OurProgramsController::class);
Route::apiResource('recent-trailer', RecentTrailerController::class);
Route::apiResource('seputar-dinus-slider', SeputarDinusSliderController::class);
Route::apiResource('seputar-dinus-sidebar-banner', SeputarDinusSidebarBannerController::class);
Route::apiResource('seputar-dinus-slides-title', SeputarDinusSlidesTitleController::class);

Route::prefix('home')->group(function () {
    Route::apiResource('our-expertise1', HomeOurExpertise1Controller::class);
    Route::apiResource('our-expertise2', HomeOurExpertise2Controller::class);
    Route::apiResource('slider', HomeSliderController::class);
    Route::apiResource('who-we-are', HomeWhoWeAreController::class);
});

Route::prefix('roles')->controller(RoleController::class)->group(function () {
    Route::post('/create', 'storeRole');
    Route::post('/permission/create', 'storePermission');
    Route::post('/permission/assign', 'assignPermissionToRole');
    Route::delete('/roles/delete/{id}', [RoleController::class, 'destroyRole']);
    Route::get('/', 'index');
});
