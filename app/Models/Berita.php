<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Berita extends Model
{
    use HasFactory;

    protected $table = 'tb_berita';
    public $timestamps = false;

    protected $fillable = [
        'judul',
        'path_media',
        'link',
        'filename',
        'deskripsi',
        'waktu',
        'id_uploader',
        'id_kategori',
        'publish',
        'open',
        'cover',
        'keyword',
        'editor',
        'library',
        'redaktur',
        'waktu_publish',
        'program_id',
        'type',
    ];

    protected $casts = [
        'waktu' => 'datetime',
        'waktu_publish' => 'datetime',
        'publish' => 'boolean',
        'editor' => 'boolean',
        'library' => 'boolean',
        'redaktur' => 'boolean',
    ];

    protected $with = ['kategori'];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'id_uploader', 'id');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'id');
    }

    public function getCoverAttribute($value)
    {
        $baseUrl = config('app.tvku_storage.base_url');
        $thumbnailPath = config('app.tvku_storage.thumbnail_berita_path');
        $pathMedia = $this->path_media;

        return $value ? $baseUrl . '/' . trim($thumbnailPath, '/') . '/' . trim($pathMedia, '/') . '/' . ltrim($value, '/') : null;
    }

    public static function storeCover($file, $oldFile = null, $pathMedia = null)
    {
        $baseFolderPath = config('app.tvku_storage.thumbnail_berita_path');

        if ($pathMedia) {
            $baseFolderPath .= '/' . trim($pathMedia, '/');
        }
        $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
        $filePath = $baseFolderPath . '/' . $filename;

        if (!Storage::disk('tvku_storage')->exists($baseFolderPath)) {
            Storage::disk('tvku_storage')->makeDirectory($baseFolderPath);
        }

        Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));

        if ($oldFile) {
            $oldFilePath = config('app.tvku_storage.thumbnail_berita_path') . '/' . trim($pathMedia, '/') . '/' . $oldFile;
            if (Storage::disk('tvku_storage')->exists($oldFilePath)) {
                Storage::disk('tvku_storage')->delete($oldFilePath);
            }
        }

        return $filename;
    }

    public function translations()
    {
        return $this->hasMany(BeritaTranslation::class, 'berita_id', 'id');
    }
}
