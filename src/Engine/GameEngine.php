<?php

namespace App\Engine;

use App\DTO\AllocatedCard;
use App\Entity\Game;
use App\Exception\CardNotFoundException;
use App\Exception\NotPlayerTurnException;
use App\Exception\TooManyCardsInHandsException;

class GameEngine {

  public const DISTANCE_TO_REACH = 1000; 
  public const MAX_CARDS_IN_HANDS = 7; // maximum 7 cards in hands before play
  protected int $id;
  protected array $cardsDeck;
  protected $players = [];
  protected $distributedCards;

  protected int $currentPlayer;
  

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
    $this->currentPlayer = 0;
  }

  public function getCurrentPlayer(): Player{
    return $this->players[$this->currentPlayer];
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

  public function trashCard(int $playerId, string $cardCode): void{
    $currentPlayer = $this->getCurrentPlayer();
    if ($currentPlayer->getId() === $playerId){
      $card = $currentPlayer->removeCardInHand($cardCode);
      array_unshift($this->cardsDeck, $card);
    }
  }

  public function getPlayers() : array {
    return $this->players;
  }

}