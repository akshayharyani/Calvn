<?php
use App\Http\Controllers\BotManController;
use App\Conversations\IntoductionConversation;
use App\Conversations\ExampleConversation;

$botman = resolve('botman');

$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
    $bot->startConversation(new IntoductionConversation);

});

$botman->hears('Start conversation', BotManController::class.'@startConversation');
