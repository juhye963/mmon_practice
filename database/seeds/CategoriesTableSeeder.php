<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*factory(App\Category::class, 10)->create();
        $categories = App\Category::all();

        foreach ($categories as $category) {
            $category->subCategories()->createMany(
                factory(App\Category::class, 5)->make()->toArray()
            );
        }

        */

        $faker = \Faker\Factory::create();
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));

        //dd($faker->department(2,false));

        for ($i = 0; $i < 10; $i++) {
            App\Category::create([
                'name' => $faker->department(1),
                'pid' => 0
            ]);
        }

        for ($i = 0; $i < 4; $i++) {
            App\Category::all()->each(function($parent_category){
                $parent_category->subCategories()->create([
                    'name' => $faker->department
                ]);
            });
        }
    }
}
