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

class PostServiceConversation extends Conversation
{


  /**
  * First name
  */
  public function startConvo()
  {
    $this->ask('How\'s your car?',[
      [
        'pattern' => 'good|great|ok|amazing',
        'callback' => function () {
          $this->say('Glad to hear that');
          $this->ask('Please rate us on a scale of 1 to 5. 1 being lowest and 5 being highest', function(Answer $answer) {
          $int = (int) $answer->getText();
            if($int >= 1 && $int <= 5){
              $this->say('Thanks for feedback');
            }
          });
        }
      ],
      [
        'pattern' => 'bad|same|problem|still same',
        'callback' => function () {
          $this->say('Sorry about that');
          $this->ask('would you like to re schedule the appointment', function(Answer $answer){
            $this->say('Ok');
          });
        }
      ]
    ]);
    }


    public function generateReciept()
    {
      $html = '<h3>GM Motors Invoice</h3><br><p>For '.$this->retreiveUserName().' </p>';
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
