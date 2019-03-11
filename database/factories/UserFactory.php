<?php

use Carbon\Carbon;
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

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Concert::class, function(Faker $faker){
    return [
        'title' => 'The Example Band',
        'subtitle' => 'with Example Openers',
        'date' => Carbon::parse('+2 weeks'),
        'price' => 2000,
        'location' => 'The Example Club',
        'street' => '123 Example Lane',
        'city' => 'Fakeville',
        'state' => 'ON',
        'zip' => '26023',
        'additional_info' => 'Example additional info'
    ];
});

$factory->state(App\Concert::class, 'published', function(Faker $faker){
    return [
        'published_at' => Carbon::parse('-1 week')
    ];
});

$factory->state(App\Concert::class, 'unpublished', function(Faker $faker){
    return [
        'published_at' => null
    ];
});

$factory->define(App\Ticket::class, function(Faker $faker){
    return [
        'concert_id' => function(){
            return factory(App\Concert::class)->create()->id;
        }
    ];
});