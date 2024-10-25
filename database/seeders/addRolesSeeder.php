<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class addRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('roles')->updateOrInsert([
            'name' => 'admin',
        ]);

        DB::table('roles')->updateOrInsert([
            'name' => 'konsumen',
        ]);

        DB::table('roles')->updateOrInsert([
            'name' => 'mitra',
        ]);

        DB::table('roles')->updateOrInsert([
            'name' => 'pemkot',
        ]);
    }
    
}
