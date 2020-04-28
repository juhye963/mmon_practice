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
        factory(App\Category::class, 10)->create();
        $categories = App\Category::all();

        foreach ($categories as $category) {
            $category->subCategories()->createMany(
                factory(App\Category::class, 5)->make()->toArray()
            );
        }
    }
}
