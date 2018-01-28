<?php
use App\Http\Controllers\BotManController;
use App\Conversations\IntoductionConversation;
use App\Conversations\ExampleConversation;
use App\Conversations\VehicleServicedConversation;

$botman = resolve('botman');

$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
    $bot->startConversation(new IntoductionConversation);

});

$botman->hears('Great', function ($bot) {
  $bot->startConversation(new VehicleServicedConversation);
});

$botman->hears('Start conversation', BotManController::class.'@startConversation');

$botman->fallback(function($bot) {
    $bot->reply('Sorry, I did not understand');
});
