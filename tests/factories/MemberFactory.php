<?php

use Tests\Fakes\Member;
use Tests\Fakes\PremiumMember;
use Tests\Fakes\RegularMember;
use Faker\Generator as Faker;

$types = [
    'regular_member',
    PremiumMember::class,
];

/** @var \Illuminate\Database\Eloquent\Factory $factory */

$factory->define(Member::class, function (Faker $faker) use ($types) {
    return ['type' => $faker->randomElement($types), 'name' => $faker->name];
});

$factory->state(Member::class, RegularMember::class, [
    'type' => 'regular_member'
]);

$factory->state(Member::class, PremiumMember::class, [
    'type' => PremiumMember::class
]);
