<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('kategori')->nullable()->after('updated_at');
            $table->integer('order')->nullable()->after('kategori');
        });

        DB::table('permissions')->get()->each(function ($permission, $index) {
            $parts = explode('.', $permission->name);
            $moduleName = $parts[0] ?? 'default';
            
            $kategori = $moduleName;
            
            $order = 500;
            
            if (str_contains($permission->name, '.store')) {
                $order = 100;
            } elseif (str_contains($permission->name, '.index') || str_contains($permission->name, '.show')) {
                $order = 200;
            } elseif (str_contains($permission->name, '.update')) {
                $order = 300;
            } elseif (str_contains($permission->name, '.destroy')) {
                $order = 400;
            }
            
            DB::table('permissions')
                ->where('id', $permission->id)
                ->update([
                    'kategori' => $kategori,
                    'order' => $order,
                ]);
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'order']);
        });
    }
};
