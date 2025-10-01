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
        Schema::table('users', function (Blueprint $table) {
            $table->date('trial_started_at')->nullable();
            $table->date('trial_ends_at')->nullable();
            $table->boolean('is_subscribed')->default(false);
            $table->date('subscribed_until')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['trial_started_at', 'trial_ends_at', 'is_subscribed', 'subscribed_until']);
        });
    }
};
