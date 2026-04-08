<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected function indexExists(string $table, string $index): bool
    {
        $database = DB::getDatabaseName();

        $row = DB::table('information_schema.statistics')
            ->select('index_name')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->first();

        return $row !== null;
    }

    protected function columnExists(string $table, string $column): bool
    {
        $database = DB::getDatabaseName();

        $row = DB::table('information_schema.columns')
            ->select('column_name')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('column_name', $column)
            ->first();

        return $row !== null;
    }

    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        if (
            $this->columnExists('qa_tasks', 'display_id') &&
            $this->columnExists('qa_tasks', 'deleted') &&
            $this->columnExists('qa_tasks', 'nextdate') &&
            !$this->indexExists('qa_tasks', 'idx_qa_tasks_display_deleted_nextdate')
        ) {
            DB::statement('CREATE INDEX idx_qa_tasks_display_deleted_nextdate ON qa_tasks (display_id, deleted, nextdate)');
        }

        if (
            $this->columnExists('tasks', 'display_id') &&
            $this->columnExists('tasks', 'deleted') &&
            $this->columnExists('tasks', 'disabled') &&
            $this->columnExists('tasks', 'nextrun') &&
            !$this->indexExists('tasks', 'idx_tasks_display_deleted_disabled_nextrun')
        ) {
            DB::statement('CREATE INDEX idx_tasks_display_deleted_disabled_nextrun ON tasks (display_id, deleted, disabled, nextrun)');
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        if ($this->indexExists('qa_tasks', 'idx_qa_tasks_display_deleted_nextdate')) {
            DB::statement('DROP INDEX idx_qa_tasks_display_deleted_nextdate ON qa_tasks');
        }

        if ($this->indexExists('tasks', 'idx_tasks_display_deleted_disabled_nextrun')) {
            DB::statement('DROP INDEX idx_tasks_display_deleted_disabled_nextrun ON tasks');
        }
    }
};
