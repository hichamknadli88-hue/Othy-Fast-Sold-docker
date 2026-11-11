<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) zid l unique jdid lawl: kay-bda b user_id, donc l foreign key kat-st3mlo
        Schema::table('user_platform_accounts', function (Blueprint $table) {
            $table->unique(['user_id', 'platform', 'account_id'], 'upa_user_platform_account_unique');
        });

        // 2) daba y9der y-tms7 l unique l9dim (user_id + platform)
        Schema::table('user_platform_accounts', function (Blueprint $table) {
            $table->dropUnique('user_platform_accounts_user_id_platform_unique');
        });
    }

    public function down(): void
    {
        Schema::table('user_platform_accounts', function (Blueprint $table) {
            $table->unique(['user_id', 'platform'], 'user_platform_accounts_user_id_platform_unique');
        });

        Schema::table('user_platform_accounts', function (Blueprint $table) {
            $table->dropUnique('upa_user_platform_account_unique');
        });
    }
};
