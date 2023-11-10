<?php

namespace App\Engine;

use App\DTO\AllocatedCard;
use App\Exception\CardNotFoundException;

class Player{

  protected int $distance;
  protected array $cardInHands;
  protected array $weaponOnTable;
  protected AllocatedCard $attackByOponent;
  protected array $cardsPlayed;
  protected int $id;
  
  public function __construct(int $id, array $initialDistributedCards)
  {
    $this->id = $id;
    $this->cardInHands = $initialDistributedCards;
  }

  public function getDistance() : int {
    return $this->distance;
  }

  public function getId() : int {
    return $this->id;
  }

  public function addCardInHand(AllocatedCard $card) : void {
    $this->cardInHands[] = $card;
  }
  
  public function getCardInHands(): array {
    return $this->cardInHands;
  }

  public function removeCardInHand(string $cardCode): AllocatedCard {
    foreach($this->cardInHands as $key => $card){
      if ((string)$card === $cardCode){
        unset($this->cardInHands[$key]);
        return $card;
      }
    }
   throw new CardNotFoundException();
  
  }
}