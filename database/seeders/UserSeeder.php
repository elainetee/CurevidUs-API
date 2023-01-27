<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        foreach(range (1,5) as $index){
            DB::table('users')->insert([
                'role_id' => $faker -> randomElement(['1', '2','3']),
                'name' => $faker -> name,
                'email' => $faker -> companyEmail,
                'password' => Hash::make($faker -> password),
                'tel_no' => $faker -> e164PhoneNumber,
                'quarantine_day' => 0,
            ]);
        }
    }
}
