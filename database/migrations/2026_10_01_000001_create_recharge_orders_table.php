<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recharge_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('montant');
            $table->string('account_id');
            $table->string('platform');
            $table->string('full_name')->nullable();
            $table->boolean('is_repeat')->default(false);

            // pending -> sent (set by RechargeController)
            $table->string('status', 20)->default('pending')->index();

            // HMAC-SHA256 of the 16-digit recharge code (never the code itself).
            // Unique: blocks replayed codes and double-clicks.
            $table->char('code_hash', 64)->nullable()->unique();

            $table->timestamp('telegram_sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recharge_orders');
    }
};