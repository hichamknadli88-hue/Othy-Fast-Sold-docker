<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_platform_accounts', function (Blueprint $table) {
            // 7ayyed l unique l9dim (user_id + platform)
            $table->dropUnique(['user_id', 'platform']);

            // nfs l compte ma yt3awdch, walakin plusieurs comptes f nfs l platform mqboulin
            $table->unique(['user_id', 'platform', 'account_id']);
        });
    }

    public function down(): void
    {
        Schema::table('user_platform_accounts', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'platform', 'account_id']);
            $table->unique(['user_id', 'platform']);
        });
    }
};
