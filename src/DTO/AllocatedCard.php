<?php

namespace App\DTO;

use App\Entity\Card;
use App\Enum\CardType;
use JsonSerializable;

class AllocatedCard implements JsonSerializable {

  protected CardType $type;
  protected string $code;
  protected int $distance;
  
  public function __construct(Card $card){
    $this->code = $card->getCode();
    $this->type = $card->getType();
    if (null !== $card->getDistance()){
      $this->distance = $card->getDistance();
    }
   }

  public function __toString()
  {
    return $this->code;
  }

  public function getCode(): string{
    return $this->code;
  }

  public function getType(): CardType{
    return $this->type;
  }

  public function getDistance(): int{
    return $this->distance;
  }

  public function jsonSerialize() {
    return ['code' => $this->code, 'type' => $this->type];
  }
  
}