<?php


namespace App\Service;

use App\Entity\Game;
use App\Enum\GameState;
use App\Repository\GameRepository;

class GameService {

  public function __construct(protected readonly GameRepository $gameRepository)
  {
    
  }

  public function getOpenGames(){
    return $this->gameRepository->getOpenGames();
  }

  public function createGame(Game $game): Game{
    $game->setCreatedAt(new \DateTimeImmutable());
    $game->setState(GameState::OPEN->value);
    return $this->gameRepository->save($game);
  }

  public function findBydId(int $id): ?Game{
    return $this->gameRepository->findOneById($id);
  }

}