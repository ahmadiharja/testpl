<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChromaUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $u = new \App\Models\User;
        $u->name = 'chroma_admin';
        $u->fullname = 'Chroma Admin';
        $u->email = 'chroma@test.com';
        $u->password = \Illuminate\Support\Facades\Hash::make('123456');
        $u->enabled = 1;
        $u->status = 1;
        $u->platform = 'perfectchroma';
        $u->save();
        
        \Illuminate\Support\Facades\DB::table('model_has_roles')->insert([
            'role_id' => 2, // admin
            'model_type' => 'App\Models\User',
            'model_id' => $u->id
        ]);

        $u2 = new \App\Models\User;
        $u2->name = 'dual_admin';
        $u2->fullname = 'Dual Platform Admin';
        $u2->email = 'dual@test.com';
        $u2->password = \Illuminate\Support\Facades\Hash::make('123456');
        $u2->enabled = 1;
        $u2->status = 1;
        $u2->platform = 'both';
        $u2->save();
        
        \Illuminate\Support\Facades\DB::table('model_has_roles')->insert([
            'role_id' => 2, // admin
            'model_type' => 'App\Models\User',
            'model_id' => $u2->id
        ]);
    }
}
