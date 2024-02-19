<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Gallery::class, function (Faker $faker) {
    $exts = [
        'bmp',
        'doc',
        'docx',
        'jpeg',
        'jpg',
        'pdf',
        'png',
        'ppt',
        'pptx',
        'zip',
    ];

    return [
        'file'        => 'OmbpNw4XwJE509Jqxz/test.' . $exts[array_rand($exts, 1)],
        'year'        => array_rand(Helper::getGalleryYears(), 1),
        'country'     => array_rand(Helper::getGalleryCountries(), 1),
        'description' => $faker->realText,
        'updated_by'  => 1,
    ];
});
