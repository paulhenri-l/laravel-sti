<?php

use Tests\Fakes\Plan;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */

$factory->define(Plan::class, function (Faker $faker) {
    return ['name' => $faker->name];
});
