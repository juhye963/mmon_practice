<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        if (config('database.default') !== 'sqlite') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }


        App\Brand::truncate();
        $this->call(BrandsTableSeeder::class);

        App\Seller::truncate();
        $this->call(SellersTableSeeder::class);

        App\Category::truncate();
        $this->call(CategoriesTableSeeder::class);

        App\Product::truncate();
        $this->call(ProductsTableSeeder::class);



        if (config('database.default') !== 'sqlite') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}
