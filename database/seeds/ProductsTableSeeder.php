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

        for ($i = 0; $i < 950000; $i++) {

            $random_seller = $sellers->random(1)->first();
            /*dump($random_seller->brand_id);
            dump($random_seller->id);
            dd($categories->random(1)->first()->id);*/

            factory(App\Product::class, 1)->create([
                'seller_id' => $random_seller->id,
                'category_id' => $categories->random(1)->first()->id,
                'brand_id' => $random_seller->brand_id
            ]);
        }

    }
}
