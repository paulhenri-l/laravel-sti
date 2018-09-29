<?php

use Tests\Fakes\Member;
use Tests\Fakes\Subscription;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */

$factory->define(Subscription::class, function (Faker $faker) {
    return ['name' => $faker->name];
});

$factory->state(Subscription::class, 'with-member', function () use ($factory) {
    return [
        'member_id' => $factory->create(Member::class)
    ];
});
