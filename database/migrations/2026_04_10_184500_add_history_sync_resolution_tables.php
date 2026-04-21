<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('history_sync_resolutions')) {
            Schema::create('history_sync_resolutions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('history_id')->unique();
                $table->unsignedBigInteger('workstation_id')->nullable()->index();
                $table->string('requested_client_id', 64)->nullable()->index();
                $table->unsignedBigInteger('resolved_display_id')->nullable()->index();
                $table->string('method', 64)->default('exact')->index();
                $table->string('confidence', 32)->default('high')->index();
                $table->text('notes')->nullable();
                $table->longText('context')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sync_display_mappings')) {
            Schema::create('sync_display_mappings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('workstation_id')->index();
                $table->string('requested_client_id', 64)->index();
                $table->string('signal_name', 191)->nullable();
                $table->string('signal_regulation', 191)->nullable();
                $table->string('signal_classification', 191)->nullable();
                $table->char('signal_hash', 40)->index();
                $table->unsignedBigInteger('resolved_display_id')->index();
                $table->string('confidence', 32)->default('medium');
                $table->unsignedInteger('hit_count')->default(1);
                $table->timestamp('last_matched_at')->nullable();
                $table->timestamps();

                $table->unique(
                    ['workstation_id', 'requested_client_id', 'signal_hash'],
                    'sync_display_map_unique'
                );
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('history_sync_resolutions')) {
            Schema::drop('history_sync_resolutions');
        }

        if (Schema::hasTable('sync_display_mappings')) {
            Schema::drop('sync_display_mappings');
        }
    }
};
