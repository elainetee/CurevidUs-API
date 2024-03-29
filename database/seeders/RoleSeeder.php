<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role= array('Patient', 'Friend','Medical');
        foreach($role as $index){
            DB::table('roles')->insert([
                 'name' => $index,
            ]);
        }
    }
}
