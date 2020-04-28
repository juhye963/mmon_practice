<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Seller;
use Faker\Generator as Faker;

$factory->define(Seller::class, function (Faker $faker) {
    $date = $faker->dateTimeThisMonth;
    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt('password'),
        'created_at' => $date,
        'updated_at' => $date
    ];
});
