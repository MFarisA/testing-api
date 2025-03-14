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
use App\Http\Controllers\Api\SeputarDinusSlidesTitleController;

Route::apiResource('kategori', KategoriController::class);
Route::apiResource('berita', BeritaController::class);
Route::apiResource('iklan', IklanController::class);
Route::apiResource('acara', AcaraController::class);
Route::apiResource('program', ProgramController::class);
Route::apiResource('our-programs', OurProgramsController::class);
Route::apiResource('recent-trailer', RecentTrailerController::class);
Route::apiResource('seputar-dinus-slider', SeputarDinusSliderController::class);
Route::apiResource('seputar-dinus-slides-title', SeputarDinusSlidesTitleController::class);

Route::prefix('home')->group(function () {
    Route::apiResource('our-expertise1', HomeOurExpertise1Controller::class);
    Route::apiResource('our-expertise2', HomeOurExpertise2Controller::class);
    Route::apiResource('slider', HomeSliderController::class);
    Route::apiResource('who-we-are', HomeWhoWeAreController::class);
});
