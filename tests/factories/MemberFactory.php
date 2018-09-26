<?php

use Tests\Fakes\Member;
use Tests\Fakes\PremiumMember;
use Tests\Fakes\RegularMember;
use Faker\Generator as Faker;

$types = [
    PremiumMember::class,
    RegularMember::class
];

/** @var \Illuminate\Database\Eloquent\Factory $factory */

$factory->define(Member::class, function (Faker $faker) use ($types) {
    return ['type' => $faker->randomElement($types), 'name' => $faker->name];
});

$factory->state(Member::class, RegularMember::class, [
    'type' => RegularMember::class
]);

$factory->state(Member::class, PremiumMember::class, [
    'type' => PremiumMember::class
]);
