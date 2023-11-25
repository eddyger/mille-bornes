<?php

namespace App\Engine;

use App\DTO\AllocatedCard;
use App\Enum\CardCode;
use App\Enum\CardType;
use App\Exception\CardNotFoundException;
use App\Exception\NotAnAttackException;

class Player{

  protected int $distance;
  protected array $cardInHands;
  /**
   * Array<AllocatedCard>
   */
  protected array $weaponOnTable;
  protected ?AllocatedCard $attackByOpponent;
  protected array $cardsPlayed;
  protected ?AllocatedCard $lastCardOnTable;
  protected int $id;
  protected string $username;
  protected bool $blocked;
  
  public function __construct(int $id, array $initialDistributedCards)
  {
    $this->id = $id;
    $this->cardInHands = $initialDistributedCards;
    $this->attackByOpponent = null;
    $this->lastCardOnTable = null;
    $this->distance = 0;
    $this->weaponOnTable = [];
    $this->blocked = true; // Player must put a GREEN_LIGHT to move forward
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

  public function getCardInHand(string $cardCode): AllocatedCard {
    foreach($this->cardInHands as $key => $card){
      if ((string)$card === $cardCode){
        return $card;
      }
    }
   throw new CardNotFoundException();
  
  }

  public function setAttackByOpponent(AllocatedCard $card): void{
    if ($card->getType() === CardType::ATTACK){
      $this->attackByOpponent = $card;
      $this->blocked = true;
    }
    else {
      throw new NotAnAttackException();
    }
  }

  public function getAttackByOpponentCard(): ?AllocatedCard{
    return $this->attackByOpponent;
  }

  public function isBlocked(): bool{
    return $this->blocked;
  }

  public function putCardOnTable(AllocatedCard $card): void {
     $this->cardsPlayed[] = $card;
     if ($card->getType() === CardType::DISTANCE){
        $this->lastCardOnTable = $card;
        $this->distance += $card->getDistance();
     }
     if ($card->getType() === CardType::WEAPON){
        $this->weaponOnTable[] = $card;
        if ($this->hasWeaponForCurrentAttack()){
          $this->attackByOpponent = null;
          $this->blocked = false; // No need GREEN_LIGHT to move forward
        }
     }
     if ($card->getType() === CardType::DEFENSE){
        // remove current attack
        $this->attackByOpponent = null;
        $this->lastCardOnTable = $card;
     }
     if ($card->getCode() === CardCode::GREEN_LIGHT->value){
      $this->blocked = false;
     }
  }

  public function getLastCardOnTable(): ?AllocatedCard {
    return $this->lastCardOnTable;
  }
  
  public function isFirstPlay(): bool{
    return \count($this->cardsPlayed) === 0;
  }

  /**
   * @throw NotAnAttackException
   */
  public function hasWeaponForAttack(string $cardCode){
    $weaponByAttack = [
      CardCode::RED_LIGHT->value => CardCode::TOP_PRIORITY->value,
      CardCode::SPEED_LIMIT->value => CardCode::TOP_PRIORITY->value,
      CardCode::OUT_OF_FUEL->value => CardCode::FUEL_TANKER->value,
      CardCode::FLAT_TIRE->value => CardCode::PUNCTURE_PROOF->value,
      CardCode::ROAD_ACCIDENT->value => CardCode::ACE_DRIVER->value,
    ];
    
    if (!isset($weaponByAttack[$cardCode])){
      throw new NotAnAttackException();
    }

    $searchedWeapon = $weaponByAttack[$cardCode];
    foreach($this->weaponOnTable as $weaponCard){
        if ((string)$weaponCard === $searchedWeapon){
          return true;
        }
    }
    return false;
  }

  public function hasWeaponForCurrentAttack(){
    if ($this->attackByOpponent !== null){
      $weaponByAttack = [
        CardCode::RED_LIGHT->value => CardCode::TOP_PRIORITY->value,
        CardCode::SPEED_LIMIT->value => CardCode::TOP_PRIORITY->value,
        CardCode::OUT_OF_FUEL->value => CardCode::FUEL_TANKER->value,
        CardCode::FLAT_TIRE->value => CardCode::PUNCTURE_PROOF->value,
        CardCode::ROAD_ACCIDENT->value => CardCode::ACE_DRIVER->value,
      ];
      
  
      $searchedWeapon = $weaponByAttack[$this->attackByOpponent->getCode()];
      foreach($this->weaponOnTable as $weaponCard){
          if ((string)$weaponCard === $searchedWeapon){
            return true;
          }
      }
      
    }
    return false;
  }

  public function getWeaponOnTable(): array{
    return $this->weaponOnTable;
  }

  public function setUsername(string $name): void{
    $this->username = $name;
  }

  public function getUsername(): string{
    return $this->username;
  }
  
}