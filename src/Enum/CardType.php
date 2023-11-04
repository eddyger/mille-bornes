<?php

namespace App\Enum;

enum CardType : string {

  case ATTACK = 'attack';
  case DEFENSE = 'defense';
  case WEAPON = 'weapon';
  case DISTANCE = 'distance';

}