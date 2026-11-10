
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE permintaan_custom
            MODIFY status_custom
            ENUM('PENDING', 'MENUNGGU_PERSETUJUAN', 'DISETUJUI', 'DITOLAK')
            NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            UPDATE permintaan_custom
            SET status_custom = 'PENDING'
            WHERE status_custom = 'MENUNGGU_PERSETUJUAN'
        ");

        DB::statement("
            ALTER TABLE permintaan_custom
            MODIFY status_custom
            ENUM('PENDING', 'DISETUJUI', 'DITOLAK')
            NOT NULL
        ");
    }
};
