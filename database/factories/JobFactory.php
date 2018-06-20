<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Job::class, function (Faker $faker) {
    return [
        'title' => $faker->text(50),
        'description' => $faker->text(200),
        'user_id' => $faker->randomElement(range(1, 10))
    ];
});
