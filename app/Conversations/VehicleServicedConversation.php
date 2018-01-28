<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Attachments\File;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use App\Car;
use App\User;
use Carbon\Carbon;
use App\ServiceAppointment;
use PDF;

class VehicleServicedConversation extends Conversation
{


  /**
  * First name
  */
  public function startConvo()
  {
    $this->ask(':)', function(Answer $answer) {
      $answer_lower_string = strtolower($answer->getText());
      if (strpos($answer_lower_string, 'deliver') !== false ) {
        $dt = Carbon::now('Asia/Kolkata');
        $time = $dt->addMinutes(50);
        $time_formatted = $time->format('g:i A');
        $this->say('We Can deliver at '.$time_formatted);
        $this->generateReciept();
        $attachment = new File('http://localhost:8080/PDF/invoice.pdf');
        $message = OutgoingMessage::create('Here\'s your receipt')
        ->withAttachment($attachment);

        $this->bot->reply($message);

      }
    });
  }


  public function generateReciept()
  {
    $html = '<h1>Test</h1>';
    PDF::loadHTML($html)->save(public_path().'/PDF/invoice.pdf');
  }

  /**
  * Start the conversation
  */
  public function run()
  {
    $this->startConvo();
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

  public function retreiveProblemDetails()
  {
    $userinformation = $this->bot->userStorage()->all();
    $problem_details = $userinformation[0]->get('problem_details');
    return $problem_details;
  }



}
