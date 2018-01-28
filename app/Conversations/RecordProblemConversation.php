<?php

namespace App\Conversations;

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use  App\Helpers\StorageHelper;
use Carbon\Carbon;

class RecordProblemConversation extends Conversation
{

  private $mins_to_add = 90;

  /**
  * Ask Details about the problem and store them
  */
  public function askProblemDetails()
  {
    $this->ask('Are there any details you would like to share?', function(Answer $answer) {
      $this->say('Alright i have recorded the details');

      $this->scheduleAppointment();
    });

  }

  /**
  * Schedule appointment
  */
  public function scheduleAppointment()
  {
    $dt = Carbon::now();
    $time = $dt->addMinutes($this->mins_to_add);
    $time_formatted = $time->format('g:i A');
    $this->ask('I can send someone to look into it at '.$time_formatted.'. Will it be ok?', [
        [
            'pattern' => 'yes|yep|ok|sure',
            'callback' => function () {
              $this->say('Ok, Appointment Scheduled');
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
    $this->askProblemDetails();
  }


}
