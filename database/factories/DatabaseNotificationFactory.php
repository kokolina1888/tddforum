<?php

use Faker\Generator as Faker;

$factory->define(\Illumminate\Notifications\DatabaseNotification::class, function (Faker $faker) {
    return [
        'id'=>\Ramsey\Uuid\Uuid::uuid4()->toString(),
        'type' => 'App\Notifications\ThreadWasUpdated',
        'notifiable_id' => function(){
        	auth()->id ?: factory('App\User')->create()->id;
        },
        'notifiable_type' => 'App\User',
        'data' => ['foo' => 'bar'],
    ];
});
