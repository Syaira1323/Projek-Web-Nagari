<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'ttd_path')) {
                    $table->string('ttd_path')->nullable()->after('address');
                }
                if (!Schema::hasColumn('users', 'gelar')) {
                    $table->string('gelar')->nullable()->after('ttd_path');
                }
                if (!Schema::hasColumn('users', 'jabatan')) {
                    $table->string('jabatan')->nullable()->after('gelar');
                }
            });
        }

        if (Schema::hasTable('surat_permohonans')) {
            Schema::table('surat_permohonans', function (Blueprint $table) {
                if (!Schema::hasColumn('surat_permohonans', 'ttd_wali_path')) {
                    $table->string('ttd_wali_path')->nullable()->after('disetujui_wali_at');
                }
                if (!Schema::hasColumn('surat_permohonans', 'signed_by_user_id')) {
                    $table->unsignedBigInteger('signed_by_user_id')->nullable()->after('ttd_wali_path');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'ttd_path')) {
                    $table->dropColumn('ttd_path');
                }
                if (Schema::hasColumn('users', 'gelar')) {
                    $table->dropColumn('gelar');
                }
                if (Schema::hasColumn('users', 'jabatan')) {
                    $table->dropColumn('jabatan');
                }
            });
        }

        if (Schema::hasTable('surat_permohonans')) {
            Schema::table('surat_permohonans', function (Blueprint $table) {
                if (Schema::hasColumn('surat_permohonans', 'ttd_wali_path')) {
                    $table->dropColumn('ttd_wali_path');
                }
                if (Schema::hasColumn('surat_permohonans', 'signed_by_user_id')) {
                    $table->dropColumn('signed_by_user_id');
                }
            });
        }
    }
};