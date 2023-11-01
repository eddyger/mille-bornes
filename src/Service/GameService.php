<?php


namespace App\Service;

use App\Entity\Game;
use App\Entity\User;
use App\Enum\GameState;
use App\Repository\GameRepository;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Security\Core\Security;

class GameService {

  public function __construct(
    protected readonly GameRepository $gameRepository, 
    protected readonly Security $security,
    protected readonly HubInterface $hub
    )
  {
    
  }

  public function getOpenGames(){
    return $this->gameRepository->getOpenGames();
  }

  public function createGame(Game $game): Game{
    $game->setCreatedAt(new \DateTimeImmutable());
    $game->setState(GameState::OPEN->value);
    $game->setOwner($this->security->getUser());
    $game->addPlayer($this->security->getUser());
    return $this->gameRepository->save($game);
  }

  public function findBydId(int $id): ?Game{
    return $this->gameRepository->findOneById($id);
  }

  public function joinGame(int $id, User $user): ?Game{
    $game = $this->findBydId($id);
    if (null != $game){
       $game->addPlayer($user);
       $this->gameRepository->save($game); 
       $update = new Update(
        '#play-'.$game->getId(),
        json_encode(
          [
            'event' => 'NewUserHasJoinedEvent',
            'user'  => $user
          ]
        )
       );
       $this->hub->publish($update);
    }
    return $game;
  }

}