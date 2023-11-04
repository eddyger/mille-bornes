<?php

namespace App\DTO;

use App\Entity\Card;

class AllocatedCard{
  
  public function __construct(protected Card $card){}

  public function getCard(): Card{
    return $this->card;
  }

}