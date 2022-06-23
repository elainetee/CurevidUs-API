<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $visibility= array('onlyme', 'friend','public');
        foreach(range (1,5) as $index){
            DB::table('posts')->insert([
                'user_id' => $faker -> randomElement(['4', '2','3','5','6']),
                'content' => $faker->sentence,
                'visibility' => $faker -> randomElement($visibility),
            ]);
        }
    }
}
