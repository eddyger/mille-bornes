<?php

namespace App\Engine;

use App\DTO\AllocatedCard;
use App\Enum\CardType;
use App\Exception\CardNotFoundException;
use App\Exception\NotAnAttackException;

class Player{

  protected int $distance;
  protected array $cardInHands;
  protected array $weaponOnTable;
  protected ?AllocatedCard $attackByOpponent;
  protected array $cardsPlayed;
  protected ?AllocatedCard $lastCardOnTable;
  protected int $id;
  
  public function __construct(int $id, array $initialDistributedCards)
  {
    $this->id = $id;
    $this->cardInHands = $initialDistributedCards;
    $this->attackByOpponent = null;
    $this->lastCardOnTable = null;
    $this->distance = 0;
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

  public function setAttackByOpponent(AllocatedCard $card): void{
    if ($card->getType() === CardType::ATTACK){
      $this->attackByOpponent = $card;
    }
    else {
      throw new NotAnAttackException();
    }
  }

  public function isBlocked(): bool{
    return $this->attackByOpponent !== null;
  }

  public function putCardOnTable(AllocatedCard $card): void {
     $this->cardsPlayed[] = $card;
     $this->lastCardOnTable = $card;
     if ($card->getType() === CardType::DISTANCE){
        $this->distance += $card->getDistance();
     }
  }

  public function getLastCardOnTable(): AllocatedCard {
    return $this->lastCardOnTable;
  }
  
}