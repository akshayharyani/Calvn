<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use App\Conversations\RecordProblemConversation;
use  App\Helpers\BotStorageHelper;
use App\Car;
use App\User;
use Carbon\Carbon;
use App\ServiceAppointment;


class IntoductionConversation extends Conversation
{

  private $storageHelper;
  private $mins_to_add = 90;

  /**
  * First name
  */
  public function askFirstname()
  {
    $this->ask('May i know your name?', function(Answer $answer) {
      if($this->stroreUserDetails($answer->getText())){
        $name = $this->retreiveUserName();
        $this->say('Nice to meet you '.$name);
        $this->askCar();
      }else{
        $this->say('Sorry I couldn\'t find your account');
      }

    });
  }

  /**
  * Ask for car problem
  */
  public function askCar()
  {
    $cars = $this->retreiveUserCars();
    $carscount = count($cars);

    if($carscount>1){
      $this->ask('How are your GM cars?', function(Answer $answer) {
        $answer_lower_string = strtolower($answer->getText());
        if (strpos($answer_lower_string, 'problem') !== false  || strpos($answer_lower_string, 'issue') !== false ) {
          $this->say('Sorry to hear that');
          $this->ask('Which car do you have problem with?', function(Answer $answer)
          {
            $this->storeProblematicCar($answer->getText());
            $this->askProblemDetails();
          });
        }
      });
    }else if($carscount == 0){
      $this->ask('How is your '.$cars[0], function(Answer $answer) {
        $answer_lower_string = strtolower($answer->getText());
        if (strpos($answer_lower_string, 'problem') !== false  || strpos($answer_lower_string, 'issue') !== false ) {
          $split = explode(" ", $answer->getText());
          $this->storeProblematicCar($split[count($split)-2]);
          $this->askProblemDetails();
        }
      });

    }else{
      $this->say('Sorry But you don\'t have any GM cars. Head out to nearest dealership to get one ');
    }

  }


  public function askProblemDetails()
  {
    $this->ask('Are there any details you would like to share?', function(Answer $answer) {
      $this->storeProblemsDetails($answer->getText());
      $this->say('Alright i have recorded the details');
      $this->scheduleAppointment();
    });

  }

  /**
  * Schedule appointment
  */
  public function scheduleAppointment()
  {
    $dt = Carbon::now('Asia/Kolkata');
    $time = $dt->addMinutes($this->mins_to_add);
    $time_formatted = $time->format('g:i A');
    $this->ask('I can send someone to look into it at '.$time_formatted.'. Will it be ok?', [
      [
        'pattern' => 'yes|yep|ok|sure',
        'callback' => function () {
          $dt = Carbon::now('Asia/Kolkata');
          $time = $dt->addMinutes($this->mins_to_add);
          $time_formatted = $time->format('g:i A');
          $this->storeAppointmentDetails($time_formatted);
          $name = $this->retreiveUserName();
          $this->say('Ok '.$name.', Appointment Scheduled');
          $this->say('Have a nice day');
        }
      ],
      [
        'pattern' => 'nah|no|nope|cancel',
        'callback' => function () {
          $this->mins_to_add = $this->mins_to_add+60;
          $this->say('Looking for Rescheduling ');
          $this->scheduleAppointment($this->mins_to_add);
        }
      ]
    ]);

  }

  /**
  * Start the conversation
  */
  public function run()
  {
    $this->askFirstname();
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

  public function storeProblemsDetails($details='')
  {
    $this->bot->userStorage()->save([
      'problem_details' => $details,
    ]);
  }

  public function retreiveProblemDetails()
  {
    $userinformation = $this->bot->userStorage()->all();
    $problem_details = $userinformation[0]->get('problem_details');
    return $problem_details;
  }

  public function storeAppointmentDetails($appointment_time)
  {
    $service_appointment = new ServiceAppointment;
    $service_appointment->user_name = $this->retreiveUserName();
    $service_appointment->car_name = $this->retreiveProblematicCar();
    $service_appointment->details = $this->retreiveProblemDetails();
    $service_appointment->appointment_time = $appointment_time;
    $service_appointment->save();
  }



}
