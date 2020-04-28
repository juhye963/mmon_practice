<?php

use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = App\Category::all();
        $sellers = App\Seller::where('brand_id', '!=', null)->get();

        foreach ($sellers as $seller) {
            $seller->products()->createMany(
              factory(App\Product::class, 100)->make([
                  'category_id' => $categories->random(1)->first()->id,
                  'brand_id' => $seller->brand_id
              ])->toArray()
            );
        }
    }
}
