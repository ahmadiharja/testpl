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

        if (!$this->indexExists('display_preferences', 'idx_display_preferences_name_value_display')) {
            DB::statement('CREATE INDEX idx_display_preferences_name_value_display ON display_preferences (name, value, display_id)');
        }

        if ($this->columnExists('tasks', 'display_id') && !$this->indexExists('tasks', 'idx_tasks_display_id')) {
            DB::statement('CREATE INDEX idx_tasks_display_id ON tasks (display_id)');
        }

        if ($this->columnExists('tasks', 'displayId') && !$this->indexExists('tasks', 'idx_tasks_displayid')) {
            DB::statement('CREATE INDEX idx_tasks_displayid ON tasks (displayId)');
        }

        if ($this->columnExists('qa_tasks', 'display_id') && !$this->indexExists('qa_tasks', 'idx_qa_tasks_display_id')) {
            DB::statement('CREATE INDEX idx_qa_tasks_display_id ON qa_tasks (display_id)');
        }

        if ($this->columnExists('qa_tasks', 'displayId') && !$this->indexExists('qa_tasks', 'idx_qa_tasks_displayid')) {
            DB::statement('CREATE INDEX idx_qa_tasks_displayid ON qa_tasks (displayId)');
        }

        if (!$this->indexExists('workstations', 'idx_workstations_last_connected')) {
            DB::statement('CREATE INDEX idx_workstations_last_connected ON workstations (last_connected)');
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        if ($this->indexExists('display_preferences', 'idx_display_preferences_name_value_display')) {
            DB::statement('DROP INDEX idx_display_preferences_name_value_display ON display_preferences');
        }

        if ($this->indexExists('tasks', 'idx_tasks_display_id')) {
            DB::statement('DROP INDEX idx_tasks_display_id ON tasks');
        }

        if ($this->indexExists('tasks', 'idx_tasks_displayid')) {
            DB::statement('DROP INDEX idx_tasks_displayid ON tasks');
        }

        if ($this->indexExists('qa_tasks', 'idx_qa_tasks_display_id')) {
            DB::statement('DROP INDEX idx_qa_tasks_display_id ON qa_tasks');
        }

        if ($this->indexExists('qa_tasks', 'idx_qa_tasks_displayid')) {
            DB::statement('DROP INDEX idx_qa_tasks_displayid ON qa_tasks');
        }

        if ($this->indexExists('workstations', 'idx_workstations_last_connected')) {
            DB::statement('DROP INDEX idx_workstations_last_connected ON workstations');
        }
    }
};
