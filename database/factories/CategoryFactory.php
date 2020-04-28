<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Category;
use Faker\Generator as Faker;


$factory->define(Category::class, function (Faker $faker) {

    $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));

    return [
        'name' => $faker->department,
        'pid' => 0,
    ];
});
