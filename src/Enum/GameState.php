<?php

namespace App\Enum;

enum GameState : string {

  case OPEN = 'open';
  case PLAY_IN_PROGRESS = 'play_in_progress';
  case TERMINATED = 'terminated';

}
