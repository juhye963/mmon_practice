<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use Faker\Generator as Faker;

/*
 * https://github.com/mbezhanov/faker-provider-collection
 * 상품명 만들어주기 위해 넣었음
 * composer require mbezhanov/faker-provider-collection
 *
 * */
/*$faker = \Faker\Factory::create();
$faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));*/

$factory->define(Product::class, function (Faker $faker) {

    $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));

    $price = $faker->numberBetween($min = 0, $max = 1000000);

    $date =

    return [
        'name' => $faker->productName,
        'price' => $price,
        'discounted_price' => $price,
        'stock' => $faker->numberBetween($min = 0, $max = 16777215),
        //'status' =>
        ''
    ];
});
