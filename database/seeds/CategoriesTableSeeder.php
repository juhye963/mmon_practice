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

        $faker = \Faker\Factory::create();
        $random_image_path = $faker->image(storage_path('app/public/product_image'), 400, 200, null, true);
        dd($random_image_path);

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
        //dd(App\Category::where('pid', '=', 0)->get()->random()->id);

        for ($i = 0; $i < 10; $i++) {
            App\Category::create([
                'name' => $faker->department(1),
                'pid' => 0
            ]);
        }

        for ($i = 0; $i < 40; $i++) {
            App\Category::create([
                'name' => $faker->department(1),
                'pid' => App\Category::where('pid', '=', 0)->get()->random()->id
            ]);
        }
    }
}
