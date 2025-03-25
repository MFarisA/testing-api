<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $table = 'translations';

    protected $fillable = [
        'name',
        'code',
    ];


    public function beritaTranslations()
    {
        return $this->hasMany(BeritaTranslation::class, 'translation_id');
    }

    public function acaraTranslations()
    {
        return $this->hasMany(AcaraTranslation::class, 'translation_id');
    }

    public function homeSliderTranslations()
    {
        return $this->hasMany(HomeSliderTranslation::class, 'translation_id');
    }

    public function homeWhoWeAreTranslations()
    {
        return $this->hasMany(HomeWhoWeAreTranslation::class, 'translation_id');
    }

    public function OurProgramsTranslations()
    {
        return $this->hasMany(OurProgramsTranslation::class, 'translation_id');
    }

    public function recentTrailerTranslations()
    {
        return $this->hasMany(RecentTrailerTranslation::class, 'translation_id');
    }

    public function sptDinusSliderTranslations()
    {
        return $this->hasMany(SeputarDinusSliderTranslation::class, 'translation_id');
    }

    public function sptDinusSlidesTitleTranslations()
    {
        return $this->hasMany(SeputarDinusSlidesTitleTranslation::class, 'translation_id');
    }

    public function sptDinusSidebarBannerTranslations()
    {
        return $this->hasMany(SeputarDinusSidebarBannerTranslation::class, 'translation_id');
    }

    public function marketingTranslations()
    {
        return $this->hasMany(IklanTranslation::class, 'translation_id');
    }

    public function homeOurExpertise1Translations()
    {
        return $this->hasMany(HomeOurExpertise1Translation::class, 'translation_id');
    }

    public function homeOurExpertise2Translations()
    {
        return $this->hasMany(HomeOurExpertise2Translation::class, 'translation_id');
    }
}
