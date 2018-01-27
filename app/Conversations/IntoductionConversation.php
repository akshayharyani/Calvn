<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use App\Conversations\RecordProblemConversation;
use  App\Helpers\StorageHelper;
use App\Cars;

class IntoductionConversation extends Conversation
{

  private $storageHelper;

  /**
  * First name
  */
  public function askFirstname()
  {
    $this->ask('May i know your name?', function(Answer $answer) {
      if($this->storageHelper->stroreUserDetails($answer->getText())){
        $name = $this->storageHelper->retreiveUserName();
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
    $cars = $this->storageHelper->retreiveUserCars();
    $carscount = count($cars);

    if($carscount>1){
      $this->ask('How are your GM cars?', function(Answer $answer) {
        $answer_lower_string = strtolower($answer->getText());
        if (strpos($answer_lower_string, 'problem') !== false  || strpos($answer_lower_string, 'issue') !== false ) {
          $this->say('Sorry to hear that');
          $this->ask('Which car do you have problem with?', function(Answer $answer)
          {
            $this->storageHelper->storeProblematicCar($answer->getText());
            $this->bot->startConversation(new RecordProblemConversation);
          });
        }
      });
    }else if($carscount == 0){
      $this->ask('How is your '.$cars[0], function(Answer $answer) {
        $answer_lower_string = strtolower($answer->getText());
        if (strpos($answer_lower_string, 'problem') !== false  || strpos($answer_lower_string, 'issue') !== false ) {
          $split = explode(" ", $answer->getText());
          $this->storageHelper->storeProblematicCar($split[count($split)-2]);
          $this->bot->startConversation(new RecordProblemConversation);
        }
      });

    }else{
      $this->say('Sorry But you don\'t have any GM cars. Head out to nearest dealership to get one ');
    }

  }

  /**
  * Start the conversation
  */
  public function run()
  {
    $this->storageHelper = new StorageHelper($this->bot);
    $this->askFirstname();
  }


}
