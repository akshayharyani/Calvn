<?php
namespace App\Helpers;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use App\Car;
use App\User;

class BotStorageHelper{

  private $bot;

  public function __construct($bot)
  {
    $this->bot = $bot;
  }

  public function stroreUserDetails($username='')
  {
    $userDetails = User::where('name',$username)
                      ->first();

    if($userDetails == null){
        return false;
    }

    $userCars = Car::where('user_id',$userDetails->user_id)
                    ->get();

    $cars_arr = array();

    foreach ($userCars as $car) {
      array_push($cars_arr,$car->car_name);
    }

    $this->bot->userStorage()->save([
        'name' => $userDetails->name,
        'cars' => $cars_arr,
    ]);

    return true;
  }

  public function storeProblematicCar($car)
  {
    $this->bot->userStorage()->save([
      'problematic_car' => $car,
    ]);
  }

  public function retreiveUserName()
  {
    $userinformation = $this->bot->userStorage()->all();
    $name = $userinformation[0]->get('name');
    return $name;
  }

  public function retreiveUserCars()
  {
    $userinformation = $this->bot->userStorage()->all();
    $userCars = $userinformation[0]->get('cars');
    return $userCars;
  }

  public function retreiveProblematicCar()
  {
    $userinformation = $this->bot->userStorage()->all();
    $problematic_car = $userinformation[0]->get('problematic_car');
    return $problematic_car;
  }

}
