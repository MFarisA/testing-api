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
use App\Http\Controllers\Api\GoogleAuthController;
use App\Http\Controllers\Api\ProgramAcaraController;
use App\Http\Controllers\Api\JadwalAcaraController;
use App\Http\Controllers\Api\TranslationController;
use App\Http\Controllers\Api\RolePermissionController;
use App\Http\Controllers\Api\UserController;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\NotificationController;
use App\Models\Kategori;
use App\Models\SeputarDinusSidebarBanner;

Route::apiResource('acara', AcaraController::class)->only(['index', 'show']);
Route::get('/acara-translations', [AcaraController::class, 'indexTranslations']);
Route::get('/acara/translations/{id}', [AcaraController::class, 'getOnlyTranslationData']);
Route::apiResource('berita', BeritaController::class)->only(['index', 'show']);
Route::get('/admin/berita/{id}', [BeritaController::class, 'showAdmin']);
Route::get('/berita-translations', [BeritaController::class, 'indexTranslations']);
Route::get('/berita/translations/{id}', [BeritaController::class, 'getOnlyTranslationData']);
Route::apiResource('iklan', IklanController::class)->only(['index', 'show']);
Route::get('/iklan-translations', [IklanController::class, 'indexTranslations']);
Route::get('/iklan/translations/{id}', [IklanController::class, 'getOnlyTranslationData']);
Route::apiResource('jadwal-acara', JadwalAcaraController::class)->only(['index', 'show']);
Route::apiResource('kategori', KategoriController::class)->only(['index', 'show']);
Route::get('/kategori-translations', [KategoriController::class, 'indexTranslations']);
Route::get('/kategori/translations/{id}', [KategoriController::class, 'getOnlyTranslationData']);
Route::get('/kategori-translations', [KategoriController::class, 'getKategoriTranslationsByParams']);
Route::get('/kategori-translations-all', [KategoriController::class, 'indexTranslations']);
Route::apiResource('program', ProgramController::class)->only(['index', 'show']);
Route::apiResource('program-acara', ProgramAcaraController::class)->only(['index', 'show']);
Route::apiResource('users', UserController::class)->only(['index', 'show']);
Route::apiResource('translations', TranslationController::class)->only(['index', 'show']);

Route::apiResource('our-programs', OurProgramsController::class)->only(['index', 'show']);
Route::get('/our-programs-translations', [OurProgramsController::class, 'indexTranslations']);
Route::get('/our-programs/translations/{id}', [OurProgramsController::class, 'getOnlyTranslationData']);
Route::apiResource('recent-trailer', RecentTrailerController::class)->only(['index', 'show']);
Route::get('/recent-trailer-translations', [RecentTrailerController::class, 'indexTranslations']);
Route::get('/recent-trailer/translations/{id}', [RecentTrailerController::class, 'getOnlyTranslationData']);
Route::apiResource('seputar-dinus-slider', SeputarDinusSliderController::class)->only(['index', 'show']);
Route::get('/seputar-dinus-slider-translations', [SeputarDinusSliderController::class, 'indexTranslations']);
Route::get('/seputar-dinus-slider/translations/{id}', [SeputarDinusSliderController::class, 'getOnlyTranslationData']);
Route::apiResource('seputar-dinus-sidebar-banner', SeputarDinusSidebarBannerController::class)->only(['index', 'show']);
Route::get('/seputar-dinus-sidebar-banner-translations', [SeputarDinusSidebarBannerController::class, 'indexTranslations']);
Route::get('/seputar-dinus-sidebar-banner/translations/{id}', [SeputarDinusSidebarBannerController::class, 'getOnlyTranslationData']);
Route::apiResource('seputar-dinus-slides-title', SeputarDinusSlidesTitleController::class)->only(['index', 'show']);
Route::get('/seputar-dinus-slides-title-translations', [SeputarDinusSlidesTitleController::class, 'indexTranslations']);
Route::get('/seputar-dinus-slides-title/translations/{id}', [SeputarDinusSlidesTitleController::class, 'getOnlyTranslationData']);

Route::prefix('home')->group(function () {
    Route::apiResource('our-expertise1', HomeOurExpertise1Controller::class)->only(['index', 'show']);
    Route::get('/our-expertise1-translations', [HomeOurExpertise1Controller::class, 'indexTranslations']);
    Route::get('/our-expertise1/translation-only/{id}', [HomeOurExpertise1Controller::class, 'getOnlyTranslationData']);
    Route::apiResource('our-expertise2', HomeOurExpertise2Controller::class)->only(['index', 'show']);
    Route::get('/our-expertise2/translation-only/{id}', [HomeOurExpertise2Controller::class, 'getOnlyTranslationData']);
    Route::apiResource('slider', HomeSliderController::class)->only(['index', 'show']);
    Route::get('/slider/translation-only/{id}', [HomeSliderController::class, 'getOnlyTranslationData']);
    Route::apiResource('who-we-are', HomeWhoWeAreController::class)->only(['index', 'show']);
    Route::get('/who-we-are-translations', [HomeWhoWeAreController::class, 'indexTranslations']);
    Route::get('/who-we-are/translation-only/{id}', [HomeWhoWeAreController::class, 'getOnlyTranslationData']);
});

Route::post('/oauth/token', [AccessTokenController::class, 'issueToken'])->middleware(['throttle'])->name('passport.token');

Route::post('/login', [AuthController::class, 'login']);

Route::prefix('auth')->group(function () {
    Route::get('/google', [GoogleAuthController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
});

Route::post('notification/preferences', [
    NotificationController::class,
    'updatePreferences'
]);
Route::post('/notification/preferences/get', [
    NotificationController::class,
    'getPreferences'
]);

// Route::middleware('auth:api', 'permissionOrSuper')->group(function () {
//     Route::post('/register', [AuthController::class, 'register']);
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::post('/validate-token', [AuthController::class, 'validateToken']);

//     Route::apiResource('acara', AcaraController::class)->except(['index', 'show']);
//     Route::delete('/acara/translation/{id}', [AcaraController::class, 'destroyTranslation']);
//     Route::apiResource('berita', BeritaController::class)->except(['index', 'show']);
//     Route::delete('/berita/translation/{id}', [BeritaController::class, 'destroyTranslation']);
//     Route::apiResource('iklan', IklanController::class)->except(['index', 'show']);
//     Route::delete('/iklan/translation/{id}', [IklanController::class, 'destroyTranslation']);
//     Route::apiResource('jadwal-acara', JadwalAcaraController::class)->except(['index', 'show']);
//     Route::apiResource('kategori', KategoriController::class)->except(['index', 'show']);
//     Route::delete('/kategori/translation/{id}', [KategoriController::class, 'destroyTranslation']);
//     Route::apiResource('program', ProgramController::class)->except(['index', 'show']);
//     Route::apiResource('program-acara', ProgramAcaraController::class)->except(['index', 'show']);
//     Route::apiResource('users', UserController::class)->except(['index', 'show']);
//     Route::apiResource('translations', TranslationController::class)->except(['index', 'show']);

//     Route::apiResource('our-programs', OurProgramsController::class)->except(['index', 'show']);
//     Route::delete('/our-programs/translation/{id}', [OurProgramsController::class, 'destroyTranslation']);
//     Route::apiResource('recent-trailer', RecentTrailerController::class)->except(['index', 'show']);
//     Route::delete('/recent-trailer/translation/{id}', [RecentTrailerController::class, 'destroyTranslation']);
//     Route::apiResource('seputar-dinus-slider', SeputarDinusSliderController::class)->except(['index', 'show']);
//     Route::delete('/seputar-dinus-slider/translation/{id}', [SeputarDinusSliderController::class, 'destroyTranslation']);
//     Route::apiResource('seputar-dinus-slides-title', SeputarDinusSlidesTitleController::class)->except(['index', 'show']);
//     Route::delete('/seputar-dinus-slides-title/translation/{id}', [SeputarDinusSlidesTitleController::class, 'destroyTranslation']);
//     Route::apiResource('seputar-dinus-sidebar-banner', SeputarDinusSidebarBannerController::class)->except(['index', 'show']);
//     Route::delete('/seputar-dinus-sidebar-banner/translation/{id}', [SeputarDinusSidebarBannerController::class, 'destroyTranslation']);


//     Route::prefix('home')->group(function () {
//         Route::apiResource('our-expertise1', HomeOurExpertise1Controller::class)->except(['index', 'show']);
//         Route::delete('/our-expertise1/translation/{id}', [HomeOurExpertise1Controller::class, 'destroyTranslation']);
//         Route::apiResource('our-expertise2', HomeOurExpertise2Controller::class)->except(['index', 'show']);
//         Route::delete('/our-expertise2/translation/{id}', [HomeOurExpertise2Controller::class, 'destroyTranslation']);
//         Route::apiResource('slider', HomeSliderController::class)->except(['index', 'show']);
//         Route::delete('/slider/translation/{id}', [HomeSliderController::class, 'destroyTranslation']);
//         Route::apiResource('who-we-are', HomeWhoWeAreController::class)->except(['index', 'show']);
//         Route::delete('/who-we-are/translation/{id}', [HomeWhoWeAreController::class, 'destroyTranslation']);
//     });
// });


Route::get('tambahpermission', function () {
    $permissions = [];

    // Acara permissions
    $perm1 = Spatie\Permission\Models\Permission::findOrCreate('acara.store');
    $perm1->update(['kategori' => 'acara', 'order' => 100]);
    $permissions[] = $perm1;

    $perm2 = Spatie\Permission\Models\Permission::findOrCreate('acara.update');
    $perm2->update(['kategori' => 'acara', 'order' => 200]);
    $permissions[] = $perm2;

    $perm3 = Spatie\Permission\Models\Permission::findOrCreate('acara.delete');
    $perm3->update(['kategori' => 'acara', 'order' => 300]);
    $permissions[] = $perm3;

    // Berita permissions
    $perm4 = Spatie\Permission\Models\Permission::findOrCreate('berita.store');
    $perm4->update(['kategori' => 'berita', 'order' => 100]);
    $permissions[] = $perm4;

    $perm5 = Spatie\Permission\Models\Permission::findOrCreate('berita.update');
    $perm5->update(['kategori' => 'berita', 'order' => 200]);
    $permissions[] = $perm5;

    $perm6 = Spatie\Permission\Models\Permission::findOrCreate('berita.delete');
    $perm6->update(['kategori' => 'berita', 'order' => 300]);
    $permissions[] = $perm6;

    // Iklan permissions
    $perm7 = Spatie\Permission\Models\Permission::findOrCreate('iklan.store');
    $perm7->update(['kategori' => 'iklan', 'order' => 100]);
    $permissions[] = $perm7;

    $perm8 = Spatie\Permission\Models\Permission::findOrCreate('iklan.update');
    $perm8->update(['kategori' => 'iklan', 'order' => 200]);
    $permissions[] = $perm8;

    $perm9 = Spatie\Permission\Models\Permission::findOrCreate('iklan.delete');
    $perm9->update(['kategori' => 'iklan', 'order' => 300]);
    $permissions[] = $perm9;

    // Jadwal Acara permissions
    $perm10 = Spatie\Permission\Models\Permission::findOrCreate('jadwal-acara.store');
    $perm10->update(['kategori' => 'jadwal-acara', 'order' => 100]);
    $permissions[] = $perm10;

    $perm11 = Spatie\Permission\Models\Permission::findOrCreate('jadwal-acara.update');
    $perm11->update(['kategori' => 'jadwal-acara', 'order' => 200]);
    $permissions[] = $perm11;

    $perm12 = Spatie\Permission\Models\Permission::findOrCreate('jadwal-acara.delete');
    $perm12->update(['kategori' => 'jadwal-acara', 'order' => 300]);
    $permissions[] = $perm12;

    // Kategori permissions
    $perm13 = Spatie\Permission\Models\Permission::findOrCreate('kategori.store');
    $perm13->update(['kategori' => 'kategori', 'order' => 100]);
    $permissions[] = $perm13;

    $perm14 = Spatie\Permission\Models\Permission::findOrCreate('kategori.update');
    $perm14->update(['kategori' => 'kategori', 'order' => 200]);
    $permissions[] = $perm14;

    $perm15 = Spatie\Permission\Models\Permission::findOrCreate('kategori.delete');
    $perm15->update(['kategori' => 'kategori', 'order' => 300]);
    $permissions[] = $perm15;

    // Program permissions
    $perm16 = Spatie\Permission\Models\Permission::findOrCreate('program.store');
    $perm16->update(['kategori' => 'program', 'order' => 100]);
    $permissions[] = $perm16;

    $perm17 = Spatie\Permission\Models\Permission::findOrCreate('program.update');
    $perm17->update(['kategori' => 'program', 'order' => 200]);
    $permissions[] = $perm17;

    $perm18 = Spatie\Permission\Models\Permission::findOrCreate('program.delete');
    $perm18->update(['kategori' => 'program', 'order' => 300]);
    $permissions[] = $perm18;

    // Program Acara permissions
    $perm19 = Spatie\Permission\Models\Permission::findOrCreate('program-acara.store');
    $perm19->update(['kategori' => 'program-acara', 'order' => 100]);
    $permissions[] = $perm19;

    $perm20 = Spatie\Permission\Models\Permission::findOrCreate('program-acara.update');
    $perm20->update(['kategori' => 'program-acara', 'order' => 200]);
    $permissions[] = $perm20;

    $perm21 = Spatie\Permission\Models\Permission::findOrCreate('program-acara.delete');
    $perm21->update(['kategori' => 'program-acara', 'order' => 300]);
    $permissions[] = $perm21;

    // Users permissions
    $perm22 = Spatie\Permission\Models\Permission::findOrCreate('users.store');
    $perm22->update(['kategori' => 'users', 'order' => 100]);
    $permissions[] = $perm22;

    $perm23 = Spatie\Permission\Models\Permission::findOrCreate('users.update');
    $perm23->update(['kategori' => 'users', 'order' => 200]);
    $permissions[] = $perm23;

    $perm24 = Spatie\Permission\Models\Permission::findOrCreate('users.delete');
    $perm24->update(['kategori' => 'users', 'order' => 300]);
    $permissions[] = $perm24;

    // Translations permissions
    $perm25 = Spatie\Permission\Models\Permission::findOrCreate('translations.store');
    $perm25->update(['kategori' => 'translations', 'order' => 100]);
    $permissions[] = $perm25;

    $perm26 = Spatie\Permission\Models\Permission::findOrCreate('translations.update');
    $perm26->update(['kategori' => 'translations', 'order' => 200]);
    $permissions[] = $perm26;

    $perm27 = Spatie\Permission\Models\Permission::findOrCreate('translations.delete');
    $perm27->update(['kategori' => 'translations', 'order' => 300]);
    $permissions[] = $perm27;

    // Our Programs permissions
    $perm28 = Spatie\Permission\Models\Permission::findOrCreate('our-programs.store');
    $perm28->update(['kategori' => 'our-programs', 'order' => 100]);
    $permissions[] = $perm28;

    $perm29 = Spatie\Permission\Models\Permission::findOrCreate('our-programs.update');
    $perm29->update(['kategori' => 'our-programs', 'order' => 200]);
    $permissions[] = $perm29;

    $perm30 = Spatie\Permission\Models\Permission::findOrCreate('our-programs.delete');
    $perm30->update(['kategori' => 'our-programs', 'order' => 300]);
    $permissions[] = $perm30;

    // Recent Trailer permissions
    $perm31 = Spatie\Permission\Models\Permission::findOrCreate('recent-trailer.store');
    $perm31->update(['kategori' => 'recent-trailer', 'order' => 100]);
    $permissions[] = $perm31;

    $perm32 = Spatie\Permission\Models\Permission::findOrCreate('recent-trailer.update');
    $perm32->update(['kategori' => 'recent-trailer', 'order' => 200]);
    $permissions[] = $perm32;

    $perm33 = Spatie\Permission\Models\Permission::findOrCreate('recent-trailer.delete');
    $perm33->update(['kategori' => 'recent-trailer', 'order' => 300]);
    $permissions[] = $perm33;

    // Seputar Dinus Slider permissions
    $perm34 = Spatie\Permission\Models\Permission::findOrCreate('seputar-dinus-slider.store');
    $perm34->update(['kategori' => 'seputar-dinus', 'order' => 100]);
    $permissions[] = $perm34;

    $perm35 = Spatie\Permission\Models\Permission::findOrCreate('seputar-dinus-slider.update');
    $perm35->update(['kategori' => 'seputar-dinus', 'order' => 200]);
    $permissions[] = $perm35;

    $perm36 = Spatie\Permission\Models\Permission::findOrCreate('seputar-dinus-slider.delete');
    $perm36->update(['kategori' => 'seputar-dinus', 'order' => 300]);
    $permissions[] = $perm36;

    // Seputar Dinus Slides Title permissions
    $perm37 = Spatie\Permission\Models\Permission::findOrCreate('seputar-dinus-slides-title.store');
    $perm37->update(['kategori' => 'seputar-dinus-slides-title', 'order' => 100]);
    $permissions[] = $perm37;

    $perm38 = Spatie\Permission\Models\Permission::findOrCreate('seputar-dinus-slides-title.update');
    $perm38->update(['kategori' => 'seputar-dinus-slides-title', 'order' => 200]);
    $permissions[] = $perm38;

    $perm39 = Spatie\Permission\Models\Permission::findOrCreate('seputar-dinus-slides-title.delete');
    $perm39->update(['kategori' => 'seputar-dinus-slides-title', 'order' => 300]);
    $permissions[] = $perm39;

    // Seputar Dinus Sidebar Banner permissions
    $perm40 = Spatie\Permission\Models\Permission::findOrCreate('seputar-dinus-sidebar-banner.store');
    $perm40->update(['kategori' => 'seputar-dinus-sidebar-banner', 'order' => 100]);
    $permissions[] = $perm40;

    $perm41 = Spatie\Permission\Models\Permission::findOrCreate('seputar-dinus-sidebar-banner.update');
    $perm41->update(['kategori' => 'seputar-dinus-sidebar-banner', 'order' => 200]);
    $permissions[] = $perm41;

    $perm42 = Spatie\Permission\Models\Permission::findOrCreate('seputar-dinus-sidebar-banner.delete');
    $perm42->update(['kategori' => 'seputar-dinus-sidebar-banner', 'order' => 300]);
    $permissions[] = $perm42;

    // Home Our Expertise1 permissions
    $perm43 = Spatie\Permission\Models\Permission::findOrCreate('home-our-expertise1.store');
    $perm43->update(['kategori' => 'our-expertise1', 'order' => 100]);
    $permissions[] = $perm43;

    $perm44 = Spatie\Permission\Models\Permission::findOrCreate('home-our-expertise1.update');
    $perm44->update(['kategori' => 'our-expertise1', 'order' => 200]);
    $permissions[] = $perm44;

    $perm45 = Spatie\Permission\Models\Permission::findOrCreate('home-our-expertise1.delete');
    $perm45->update(['kategori' => 'our-expertise1', 'order' => 300]);
    $permissions[] = $perm45;

    // Home Our Expertise2 permissions
    $perm46 = Spatie\Permission\Models\Permission::findOrCreate('home-our-expertise2.store');
    $perm46->update(['kategori' => 'our-expertise2', 'order' => 100]);
    $permissions[] = $perm46;

    $perm47 = Spatie\Permission\Models\Permission::findOrCreate('home-our-expertise2.update');
    $perm47->update(['kategori' => 'our-expertise2', 'order' => 200]);
    $permissions[] = $perm47;

    $perm48 = Spatie\Permission\Models\Permission::findOrCreate('home-our-expertise2.delete');
    $perm48->update(['kategori' => 'our-expertise2', 'order' => 300]);
    $permissions[] = $perm48;

    // Home Slider permissions
    $perm49 = Spatie\Permission\Models\Permission::findOrCreate('home-slider.store');
    $perm49->update(['kategori' => 'home-slider', 'order' => 100]);
    $permissions[] = $perm49;

    $perm50 = Spatie\Permission\Models\Permission::findOrCreate('home-slider.update');
    $perm50->update(['kategori' => 'home-slider', 'order' => 200]);
    $permissions[] = $perm50;

    $perm51 = Spatie\Permission\Models\Permission::findOrCreate('home-slider.delete');
    $perm51->update(['kategori' => 'home-slider', 'order' => 300]);
    $permissions[] = $perm51;

    // Home Who We Are permissions
    $perm52 = Spatie\Permission\Models\Permission::findOrCreate('home-who-we-are.store');
    $perm52->update(['kategori' => 'who-we-are', 'order' => 100]);
    $permissions[] = $perm52;

    $perm53 = Spatie\Permission\Models\Permission::findOrCreate('home-who-we-are.update');
    $perm53->update(['kategori' => 'who-we-are', 'order' => 200]);
    $permissions[] = $perm53;

    $perm54 = Spatie\Permission\Models\Permission::findOrCreate('home-who-we-are.delete');
    $perm54->update(['kategori' => 'who-we-are', 'order' => 300]);
    $permissions[] = $perm54;

    $adminRole = Spatie\Permission\Models\Role::where('name', 'Administrator')->first();
    if ($adminRole) {
        foreach ($permissions as $permission) {
            $adminRole->givePermissionTo($permission);
        }
    }

    return response()->json(['message' => 'Permissions created and assigned to Administrator role successfully']);
});

Route::middleware(['auth:api','permissionOrSuper'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/validate-token', [AuthController::class, 'validateToken']);

    Route::post('/acara', [AcaraController::class, 'store'])->middleware('can:acara.store');
    Route::put('/acara/{id}', [AcaraController::class, 'update'])->middleware('can:acara.update');
    Route::delete('/acara/{id}', [AcaraController::class, 'destroy'])->middleware('can:acara.delete');
    Route::delete('/acara/translation/{id}', [AcaraController::class, 'destroyTranslation'])->middleware('can:acara.delete');

    Route::post('/berita', [BeritaController::class, 'store'])->middleware('can:berita.store');
    Route::put('/berita/{id}', [BeritaController::class, 'update'])->middleware('can:berita.update');
    Route::delete('/berita/{id}', [BeritaController::class, 'destroy'])->middleware('can:berita.delete');
    Route::delete('/berita/translation/{id}', [BeritaController::class, 'destroyTranslation'])->middleware('can:berita.delete');

    Route::post('/iklan', [IklanController::class, 'store'])->middleware('can:iklan.store');
    Route::put('/iklan/{id}', [IklanController::class, 'update'])->middleware('can:iklan.update');
    Route::delete('/iklan/{id}', [IklanController::class, 'destroy'])->middleware('can:iklan.delete');
    Route::delete('/iklan/translation/{id}', [IklanController::class, 'destroyTranslation'])->middleware('can:iklan.delete');

    Route::post('/jadwal-acara', [JadwalAcaraController::class, 'store'])->middleware('can:jadwal-acara.store');
    Route::put('/jadwal-acara/{id}', [JadwalAcaraController::class, 'update'])->middleware('can:jadwal-acara.update');
    Route::delete('/jadwal-acara/{id}', [JadwalAcaraController::class, 'destroy'])->middleware('can:jadwal-acara.delete');

    Route::post('/kategori', [KategoriController::class, 'store'])->middleware('can:kategori.store');
    Route::put('/kategori/{id}', [KategoriController::class, 'update'])->middleware('can:kategori.update');
    Route::delete('/kategori/{id}', [KategoriController::class, 'destroy'])->middleware('can:kategori.delete');
    Route::delete('/kategori/translation/{id}', [KategoriController::class, 'destroyTranslation'])->middleware('can:kategori.delete');

    Route::post('/program', [ProgramController::class, 'store'])->middleware('can:program.store');
    Route::put('/program/{id}', [ProgramController::class, 'update'])->middleware('can:program.update');
    Route::delete('/program/{id}', [ProgramController::class, 'destroy'])->middleware('can:program.delete');

    Route::post('/program-acara', [ProgramAcaraController::class, 'store'])->middleware('can:program-acara.store');
    Route::put('/program-acara/{id}', [ProgramAcaraController::class, 'update'])->middleware('can:program-acara.update');
    Route::delete('/program-acara/{id}', [ProgramAcaraController::class, 'destroy'])->middleware('can:program-acara.delete');

    Route::post('/users', [UserController::class, 'store'])->middleware('can:users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->middleware('can:users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->middleware('can:users.delete');

    Route::post('/translations', [TranslationController::class, 'store'])->middleware('can:translations.store');
    Route::put('/translations/{id}', [TranslationController::class, 'update'])->middleware('can:translations.update');
    Route::delete('/translations/{id}', [TranslationController::class, 'destroy'])->middleware('can:translations.delete');

    Route::post('/our-programs', [OurProgramsController::class, 'store'])->middleware('can:our-programs.store');
    Route::put('/our-programs/{id}', [OurProgramsController::class, 'update'])->middleware('can:our-programs.update');
    Route::delete('/our-programs/{id}', [OurProgramsController::class, 'destroy'])->middleware('can:our-programs.delete');
    Route::delete('/our-programs/translation/{id}', [OurProgramsController::class, 'destroyTranslation'])->middleware('can:our-programs.delete');

    Route::post('/recent-trailer', [RecentTrailerController::class, 'store'])->middleware('can:recent-trailer.store');
    Route::put('/recent-trailer/{id}', [RecentTrailerController::class, 'update'])->middleware('can:recent-trailer.update');
    Route::delete('/recent-trailer/{id}', [RecentTrailerController::class, 'destroy'])->middleware('can:recent-trailer.delete');
    Route::delete('/recent-trailer/translation/{id}', [RecentTrailerController::class, 'destroyTranslation'])->middleware('can:recent-trailer.delete');

    Route::post('/seputar-dinus-slider', [SeputarDinusSliderController::class, 'store'])->middleware('can:seputar-dinus-slider.store');
    Route::put('/seputar-dinus-slider/{id}', [SeputarDinusSliderController::class, 'update'])->middleware('can:seputar-dinus-slider.update');
    Route::delete('/seputar-dinus-slider/{id}', [SeputarDinusSliderController::class, 'destroy'])->middleware('can:seputar-dinus-slider.delete');
    Route::delete('/seputar-dinus-slider/translation/{id}', [SeputarDinusSliderController::class, 'destroyTranslation'])->middleware('can:seputar-dinus-slider.delete');

    Route::post('/seputar-dinus-slides-title', [SeputarDinusSlidesTitleController::class, 'store'])->middleware('can:seputar-dinus-slides-title.store');
    Route::put('/seputar-dinus-slides-title/{id}', [SeputarDinusSlidesTitleController::class, 'update'])->middleware('can:seputar-dinus-slides-title.update');
    Route::delete('/seputar-dinus-slides-title/{id}', [SeputarDinusSlidesTitleController::class, 'destroy'])->middleware('can:seputar-dinus-slides-title.delete');
    Route::delete('/seputar-dinus-slides-title/translation/{id}', [SeputarDinusSlidesTitleController::class, 'destroyTranslation'])->middleware('can:seputar-dinus-slides-title.delete');

    Route::post('/seputar-dinus-sidebar-banner', [SeputarDinusSidebarBannerController::class, 'store'])->middleware('can:seputar-dinus-sidebar-banner.store');
    Route::put('/seputar-dinus-sidebar-banner/{id}', [SeputarDinusSidebarBannerController::class, 'update'])->middleware('can:seputar-dinus-sidebar-banner.update');
    Route::delete('/seputar-dinus-sidebar-banner/{id}', [SeputarDinusSidebarBannerController::class, 'destroy'])->middleware('can:seputar-dinus-sidebar-banner.delete');
    Route::delete('/seputar-dinus-sidebar-banner/translation/{id}', [SeputarDinusSidebarBannerController::class, 'destroyTranslation'])->middleware('can:seputar-dinus-sidebar-banner.delete');

    Route::prefix('home')->group(function () {
        Route::post('/our-expertise1', [HomeOurExpertise1Controller::class, 'store'])->middleware('can:home-our-expertise1.store');
        Route::put('/our-expertise1/{id}', [HomeOurExpertise1Controller::class, 'update'])->middleware('can:home-our-expertise1.update');
        Route::delete('/our-expertise1/{id}', [HomeOurExpertise1Controller::class, 'destroy'])->middleware('can:home-our-expertise1.delete');
        Route::delete('/our-expertise1/translation/{id}', [HomeOurExpertise1Controller::class, 'destroyTranslation'])->middleware('can:home-our-expertise1.delete');

        Route::post('/our-expertise2', [HomeOurExpertise2Controller::class, 'store'])->middleware('can:home-our-expertise2.store');
        Route::put('/our-expertise2/{id}', [HomeOurExpertise2Controller::class, 'update'])->middleware('can:home-our-expertise2.update');
        Route::delete('/our-expertise2/{id}', [HomeOurExpertise2Controller::class, 'destroy'])->middleware('can:home-our-expertise2.delete');
        Route::delete('/our-expertise2/translation/{id}', [HomeOurExpertise2Controller::class, 'destroyTranslation'])->middleware('can:home-our-expertise2.delete');

        Route::post('/slider', [HomeSliderController::class, 'store'])->middleware('can:home-slider.store');
        Route::put('/slider/{id}', [HomeSliderController::class, 'update'])->middleware('can:home-slider.update');
        Route::delete('/slider/{id}', [HomeSliderController::class, 'destroy'])->middleware('can:home-slider.delete');
        Route::delete('/slider/translation/{id}', [HomeSliderController::class, 'destroyTranslation'])->middleware('can:home-slider.delete');

        Route::post('/who-we-are', [HomeWhoWeAreController::class, 'store'])->middleware('can:home-who-we-are.store');
        Route::put('/who-we-are/{id}', [HomeWhoWeAreController::class, 'update'])->middleware('can:home-who-we-are.update');
        Route::delete('/who-we-are/{id}', [HomeWhoWeAreController::class, 'destroy'])->middleware('can:home-who-we-are.delete');
        Route::delete('/who-we-are/translation/{id}', [HomeWhoWeAreController::class, 'destroyTranslation'])->middleware('can:home-who-we-are.delete');
    });
});

Route::middleware(['auth:api', 'ensure.superadmin'])->prefix('super-admin')->group(function () {
    // Role management
    Route::get('/roles', [RolePermissionController::class, 'indexRoles']);
    Route::post('/roles', [RolePermissionController::class, 'storeRole']);
    Route::put('/roles/{id}', [RolePermissionController::class, 'updateRole']);
    Route::delete('/roles/{id}', [RolePermissionController::class, 'deleteRole']);
    
    // Permission management
    Route::get('/permissions', [RolePermissionController::class, 'indexPermissions']);
    Route::get('/permissions-for-form', [RolePermissionController::class, 'getPermissionsForRoleForm']);
    Route::post('/permissions', [RolePermissionController::class, 'storePermission']);
    Route::delete('/permissions/{id}', [RolePermissionController::class, 'destroyPermission']);
    
    // Role-Permission assignment
    Route::post('/assign-permissions-to-role', [RolePermissionController::class, 'assignPermissionToRole']);
    Route::post('/bulk-assign-permissions', [RolePermissionController::class, 'bulkAssignPermissions']);
    Route::get('/roles-with-permissions', [RolePermissionController::class, 'getRoleWithPermissions']);
    
    // User role management
    Route::get('/users-with-roles', [RolePermissionController::class, 'getUsersWithRoles']);
    Route::get('/users/{id}/roles-permissions', [RolePermissionController::class, 'getUserWithRoleAndPermission']);
    Route::post('/assign-role-to-user', [RolePermissionController::class, 'assignRoleToUser']);
    Route::post('/remove-role-from-user', [RolePermissionController::class, 'removeRoleFromUser']);
});

Route::middleware(['cookie.token', 'auth:api'])->get('/cookie', function () {
    return response()->json(Auth::user());
});
