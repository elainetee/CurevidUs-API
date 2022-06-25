<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $medicine= array('Atorvastatin', 'Levothyroxine','Lisinopril','Metformin',
        'Amlodipine','Metoprolol','Omeprazole');
        foreach($medicine as $index){
            DB::table('medicines')->insert([
                'medicine_name' => $index,
                'medicine_desc' => $faker -> sentence,
                'medicine_price' => $faker -> numberBetween(10, 65),
            ]);
        }
    }
}
