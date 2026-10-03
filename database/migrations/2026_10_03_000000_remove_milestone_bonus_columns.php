<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'milestone_cycle_progress')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('milestone_cycle_progress');
            });
        }

        if (Schema::hasColumn('users', 'milestone_500_notified_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('milestone_500_notified_at');
            });
        }

        if (Schema::hasColumn('user_platform_accounts', 'milestone_cycle_progress')) {
            Schema::table('user_platform_accounts', function (Blueprint $table) {
                $table->dropColumn('milestone_cycle_progress');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'milestone_500_notified_at')) {
                $table->timestamp('milestone_500_notified_at')->nullable()->after('base_token_hash');
            }
            if (! Schema::hasColumn('users', 'milestone_cycle_progress')) {
                $table->unsignedInteger('milestone_cycle_progress')->default(0)->after('milestone_500_notified_at');
            }
        });

        Schema::table('user_platform_accounts', function (Blueprint $table) {
            if (! Schema::hasColumn('user_platform_accounts', 'milestone_cycle_progress')) {
                $table->unsignedInteger('milestone_cycle_progress')->default(0)->after('full_name');
            }
        });
    }
};
