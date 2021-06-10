<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Copy;
use Faker\Generator as Faker;

$factory->define(Copy::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'important_text' => $faker->sentence,
    ];
});
