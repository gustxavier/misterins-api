<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;
use Faker\Provider\pt_BR\Person;

$factory->define(User::class, function (Faker $faker) {
    $personFaker = new Person($faker);
    return [
        'name' => $faker->name,
        'cpf' => $personFaker->cpf(),
        'facebook' => $faker->sentence,
        'instagram' => $faker->sentence,
        'email' => $faker->unique()->safeEmail,
        'password' => Hash::make('12345') // password
    ];
});
