<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected function indexExists(string $table, string $indexName): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::raw('DATABASE()'))
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();
    }

    public function up(): void
    {
        if (Schema::hasTable('tasks') && !$this->indexExists('tasks', 'tasks_deleted_disabled_nextrun_idx')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->index(['deleted', 'disabled', 'nextrun'], 'tasks_deleted_disabled_nextrun_idx');
            });
        }

        if (Schema::hasTable('qa_tasks') && !$this->indexExists('qa_tasks', 'qa_tasks_deleted_nextdate_display_idx')) {
            Schema::table('qa_tasks', function (Blueprint $table) {
                $table->index(['deleted', 'nextdate', 'display_id'], 'qa_tasks_deleted_nextdate_display_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tasks') && $this->indexExists('tasks', 'tasks_deleted_disabled_nextrun_idx')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropIndex('tasks_deleted_disabled_nextrun_idx');
            });
        }

        if (Schema::hasTable('qa_tasks') && $this->indexExists('qa_tasks', 'qa_tasks_deleted_nextdate_display_idx')) {
            Schema::table('qa_tasks', function (Blueprint $table) {
                $table->dropIndex('qa_tasks_deleted_nextdate_display_idx');
            });
        }
    }
};

