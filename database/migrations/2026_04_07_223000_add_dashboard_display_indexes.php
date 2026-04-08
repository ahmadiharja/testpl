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
            $this->columnExists('display_preferences', 'display_id') &&
            $this->columnExists('display_preferences', 'name') &&
            $this->columnExists('display_preferences', 'value') &&
            !$this->indexExists('display_preferences', 'idx_display_preferences_display_name_value')
        ) {
            DB::statement('CREATE INDEX idx_display_preferences_display_name_value ON display_preferences (display_id, name, value)');
        }

        if (
            $this->columnExists('displays', 'workstation_id') &&
            $this->columnExists('displays', 'status') &&
            $this->columnExists('displays', 'updated_at') &&
            $this->columnExists('displays', 'id') &&
            !$this->indexExists('displays', 'idx_displays_workstation_status_updated')
        ) {
            DB::statement('CREATE INDEX idx_displays_workstation_status_updated ON displays (workstation_id, status, updated_at, id)');
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        if ($this->indexExists('display_preferences', 'idx_display_preferences_display_name_value')) {
            DB::statement('DROP INDEX idx_display_preferences_display_name_value ON display_preferences');
        }

        if ($this->indexExists('displays', 'idx_displays_workstation_status_updated')) {
            DB::statement('DROP INDEX idx_displays_workstation_status_updated ON displays');
        }
    }
};
