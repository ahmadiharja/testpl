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
            $this->columnExists('display_hours', 'display_id') &&
            $this->columnExists('display_hours', 'start') &&
            $this->columnExists('display_hours', 'id') &&
            !$this->indexExists('display_hours', 'idx_display_hours_display_start_id')
        ) {
            DB::statement('CREATE INDEX idx_display_hours_display_start_id ON display_hours (display_id, start, id)');
        }

        if (
            $this->columnExists('display_hours', 'display_id') &&
            $this->columnExists('display_hours', 'duration') &&
            !$this->indexExists('display_hours', 'idx_display_hours_display_duration')
        ) {
            DB::statement('CREATE INDEX idx_display_hours_display_duration ON display_hours (display_id, duration)');
        }

        if (
            $this->columnExists('histories', 'display_id') &&
            $this->columnExists('histories', 'time') &&
            !$this->indexExists('histories', 'idx_histories_display_time')
        ) {
            DB::statement('CREATE INDEX idx_histories_display_time ON histories (display_id, time)');
        }

        if (
            $this->columnExists('histories', 'display_id') &&
            $this->columnExists('histories', 'result') &&
            !$this->indexExists('histories', 'idx_histories_display_result')
        ) {
            DB::statement('CREATE INDEX idx_histories_display_result ON histories (display_id, result)');
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        if ($this->indexExists('display_hours', 'idx_display_hours_display_start_id')) {
            DB::statement('DROP INDEX idx_display_hours_display_start_id ON display_hours');
        }

        if ($this->indexExists('display_hours', 'idx_display_hours_display_duration')) {
            DB::statement('DROP INDEX idx_display_hours_display_duration ON display_hours');
        }

        if ($this->indexExists('histories', 'idx_histories_display_time')) {
            DB::statement('DROP INDEX idx_histories_display_time ON histories');
        }

        if ($this->indexExists('histories', 'idx_histories_display_result')) {
            DB::statement('DROP INDEX idx_histories_display_result ON histories');
        }
    }
};
