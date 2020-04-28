<?php

use Illuminate\Database\Seeder;

class BrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //factory(App\Brand::class, 200)->create();

        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 200; $i++) {
            App\Brand::create([
                'name' => $faker->company
            ]);
        }
    }
}
