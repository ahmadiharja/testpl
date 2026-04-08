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
            $this->columnExists('workstations', 'workgroup_id') &&
            $this->columnExists('workstations', 'name') &&
            $this->columnExists('workstations', 'id') &&
            !$this->indexExists('workstations', 'idx_workstations_workgroup_name_id')
        ) {
            DB::statement('CREATE INDEX idx_workstations_workgroup_name_id ON workstations (workgroup_id, name, id)');
        }

        if (
            $this->columnExists('workgroups', 'facility_id') &&
            $this->columnExists('workgroups', 'name') &&
            $this->columnExists('workgroups', 'id') &&
            !$this->indexExists('workgroups', 'idx_workgroups_facility_name_id')
        ) {
            DB::statement('CREATE INDEX idx_workgroups_facility_name_id ON workgroups (facility_id, name, id)');
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        if ($this->indexExists('workstations', 'idx_workstations_workgroup_name_id')) {
            DB::statement('DROP INDEX idx_workstations_workgroup_name_id ON workstations');
        }

        if ($this->indexExists('workgroups', 'idx_workgroups_facility_name_id')) {
            DB::statement('DROP INDEX idx_workgroups_facility_name_id ON workgroups');
        }
    }
};

