<?php

namespace App\Engine;

use App\DTO\AllocatedCard;
use App\Entity\Card;
use App\Entity\Game;
use App\Enum\CardCode;
use App\Enum\CardType;
use App\Exception\AnomalyException;
use App\Exception\CannotPlayThisCardException;
use App\Exception\CardNotFoundException;
use App\Exception\NotPlayerTurnException;
use App\Exception\PlayerNotFoundException;
use App\Exception\TooManyCardsInHandsException;
use Exception;

class GameEngine {

  public const DISTANCE_TO_REACH = 1000; 
  public const MAX_CARDS_IN_HANDS = 7; // maximum 7 cards in hands before play
  protected int $id;
  protected array $cardsDeck;
  protected $players = [];
  protected $distributedCards;

  protected int $currentPlayerIndex;
  

  public function __construct(Game $game, array $cardsDeck, array $distributedCards){
    $this->id = $game->getId();
    $this->cardsDeck = $cardsDeck;
    $this->distributedCards = $distributedCards;
    $this->init($game);
  }

  protected function init(Game $game){
    foreach($game->getPlayers() as $p){
      $player = new Player($p->getId(), $this->distributedCards[$p->getId()]);
      $this->players[] = $player;
    }
    $this->currentPlayerIndex = 0;
  }

  public function getCurrentPlayer(): Player{
    return $this->players[$this->currentPlayerIndex];
  }

  public function gameIsOver(): bool{
    foreach($this->players as $player){
      if ($player->getDistance() === self::DISTANCE_TO_REACH){
        return true;
      }
      return false;
    }
  }

  public function getWinner(): ?Player{
    foreach($this->players as $player){
      if ($player->getDistance() === self::DISTANCE_TO_REACH){
        return $player;
      }
      return null;
    }
  }

  public function takeCardInDeck(int $playerId): AllocatedCard{
    $currentPlayer = $this->getCurrentPlayer();
    if ($currentPlayer->getId() === $playerId){
      if (\count($currentPlayer->getCardInHands()) >= self::MAX_CARDS_IN_HANDS){
       throw new TooManyCardsInHandsException();
      }
      $card = array_pop($this->cardsDeck);
      $currentPlayer->addCardInHand($card);
      return $card;
    }
    throw new NotPlayerTurnException();
  }
  
  /**
   * play the cards for current player and return next player
   */
  public function playCard(int $playerId, array $played): Player{
    $currentPlayer = $this->getCurrentPlayer();
    if ($currentPlayer->getId() === $playerId){
      /* Json body : {
        'trash' : 'cardCode',  
        'table': 'cardCode'
        'opponent' : {'card':'cardCode', 'player':'playerId'}
       }
      */
      if (!empty($played['trash'])){
        $this->trashCard($playerId, $played['trash']);
      }
      
      if (!empty($played['table'])){
        $this->putCardInTable($playerId, $played['table']);
      }

      if (isset($played['opponent']['card'])){
        $cardCode = $played['opponent']['card'];
        $card = $currentPlayer->removeCardInHand($cardCode);
        $opponent = $this->findPlayerById($played['opponent']['player']);
        $opponent->setAttackByOpponent($card);
      }

      // Change player
      $this->currentPlayerIndex++;
      if ($this->currentPlayerIndex >= \count($this->players)){
        $this->currentPlayerIndex = 0;
      }

      return $this->getCurrentPlayer();
      
    }else {
      throw new NotPlayerTurnException();
    }
  }

  protected function findPlayerById(int $playerId): Player{
    foreach($this->players as $player){
      if ($player->getId() === $playerId){
        return $player;
      }
    }
    throw new PlayerNotFoundException();
  }

  protected function putCardInTable(int $playerId, string $cardCode): void{
    $currentPlayer = $this->getCurrentPlayer();
    if ($currentPlayer->getId() === $playerId){
      if ($this->canPlayOnTable($currentPlayer,$cardCode)){
        $card = $currentPlayer->removeCardInHand($cardCode);
        $currentPlayer->putCardOnTable($card);
      }else{
        throw new CannotPlayThisCardException();
      }
    }else {
      throw new NotPlayerTurnException();
    }
  }

  protected function canPlayOnTable(Player $player , string $cardCode): bool {
    $card = $player->getCardInHand($cardCode);
    // 1. Card cannot be an attack
    if ($card->getType() === CardType::ATTACK){
      return false;
    }

    // 2. Card is DEFENSE
    if ($card->getType() === CardType::DEFENSE){
      if ($card->getCode() === CardCode::REPAIR->value){
        return $player->getAttackByOpponentCard()?->getCode() === CardCode::ROAD_ACCIDENT->value;
      }

      if ($card->getCode() === CardCode::FUEL->value){
        return $player->getAttackByOpponentCard()?->getCode() === CardCode::OUT_OF_FUEL->value;
      }

      if ($card->getCode() === CardCode::SPARE_WHEEL->value){
        return $player->getAttackByOpponentCard()?->getCode() === CardCode::FLAT_TIRE->value;
      }

      if ($card->getCode() === CardCode::END_OF_SPEED_LIMIT->value){
        return $player->getAttackByOpponentCard()?->getCode() === CardCode::SPEED_LIMIT->value;
      }
      
      if ($card->getCode() === CardCode::GREEN_LIGHT->value){
        return $player->getAttackByOpponentCard()?->getCode() === CardCode::RED_LIGHT->value || $player->isBlocked();
      }
    }

    // 3. Card is weapon
    if ($card->getType() === CardType::WEAPON){
      return true;
    }

    // 4. Card is distance
    if ($card->getType() === CardType::DISTANCE){
      $currentAttack = $player->getAttackByOpponentCard();
      if (null !== $currentAttack){
        if ($currentAttack->getCode() === CardCode::SPEED_LIMIT->value && $card->getCode() === CardCode::SNAIL){
          return true;
        }
        return false;
      }else{
        // No attack
        return !$player->isBlocked();
      }
    }

    throw new AnomalyException();
  }

  protected function trashCard(int $playerId, string $cardCode): void{
    $currentPlayer = $this->getCurrentPlayer();
    if ($currentPlayer->getId() === $playerId){
      $card = $currentPlayer->removeCardInHand($cardCode);
      array_unshift($this->cardsDeck, $card);
    }else {
      throw new NotPlayerTurnException();
    }
  }

  public function getPlayers() : array {
    return $this->players;
  }

}