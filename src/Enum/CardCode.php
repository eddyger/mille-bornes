<?php

namespace App\Enum;

enum CardCode : string
{
  case ROAD_ACCIDENT = 'ROAD_ACCIDENT';
  case OUT_OF_FUEL = 'OUT_OF_FUEL';
  case FLAT_TIRE = 'FLAT_TIRE';
  case SPEED_LIMIT = 'SPEED_LIMIT';
  case RED_LIGHT = 'RED_LIGHT';
  
  case REPAIR = 'REPAIR';
  case FUEL = 'FUEL';
  case SPARE_WHEEL = 'SPARE_WHEEL';
  case END_OF_SPEED_LIMIT = 'END_OF_SPEED_LIMIT';
  case GREEN_LIGHT = 'GREEN_LIGHT';

  case ACE_DRIVER = 'ACE_DRIVER';
  case FUEL_TANKER = 'FUEL_TANKER';
  case PUNCTURE_PROOF = 'PUNCTURE_PROOF';
  case TOP_PRIORITY = 'TOP_PRIORITY';

  case SNAIL = 'SNAIL';
  case DUCK = 'DUCK';
  case BUTTER_FLY = 'BUTTER_FLY';
  case HARE = 'HARE';
  case SWALLOW = 'SWALLOW'; //Hirondelle
  
}