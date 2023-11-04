<?php


namespace App\Service;

use App\DTO\AllocatedCard;
use App\Entity\Game;
use App\Entity\User;
use App\Enum\GameState;
use App\Exception\GameAlreadyStartException;
use App\Exception\MaxPlayerReachedException;
use App\Repository\CardRepository;
use App\Repository\GameRepository;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Security\Core\Security;

class GameService {

  public function __construct(
    protected readonly GameRepository $gameRepository, 
    protected readonly Security $security,
    protected readonly HubInterface $hub,
    protected readonly CardRepository $cardRepository
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
       if ($game->getPlayers()->count() < Game::MAX_PLAYERS){
        $game->addPlayer($user);
        $this->gameRepository->save($game); 
        $this->notifyPlayers(
            $game->getId(),
            [
              'event' => 'NewUserHasJoinedEvent',
              'user'  => $user,
              'nbPlayers' => $game->getPlayers()->count()
            ]
        );
       }else {
         throw new MaxPlayerReachedException();
       }
       
    }
    return $game;
  }

  public function startNewGame(Game $game){
    if ($game->getState() === GameState::OPEN->value){
      // 1. we load all the cards
      $allCardsToPicks = $this->allocateCards($this->cardRepository->findAll());
      
      // 2. we shuffle the cards
      $allCardsToPicks = $this->shuffle($allCardsToPicks);

      // 3. Card distribution. We start with 6 cards by player
      $distributionByUser = [];
      for ($i = 1; $i <= 6 ; $i++){
        foreach($game->getPlayers() as $player){
          $distributionByUser[$player->getId()][] = array_pop($allCardsToPicks);
        }
      }

      // 4. change game state
      $game->setState(GameState::PLAY_IN_PROGRESS->value);
      $this->gameRepository->save($game);

      // 5. notify players
      $this->notifyPlayers($game->getId(),[
        'event' => 'GameIsStartedEvent',
        'cardsByUser' => $distributionByUser
      ]);

    }else{
      throw new GameAlreadyStartException();
    }
  }

  /**
   *
   * @param $cards Card[] 
   */
  protected function  allocateCards($cards): array{
    $allocatedCards = [];

    foreach($cards as $card){
      for ($i = 1 ; $i <= $card->getNumberInGame() ; $i++){
        $allocatedCards[] = new AllocatedCard($card); 
      }
    }

    return $allocatedCards;
  }

  protected function shuffle($cards): array{
    $keys = array_keys($cards);
    shuffle($keys);
    $shuffledCards = [];
    foreach($keys as $key){
      $shuffledCards[] = $cards[$key];
    }

    return $shuffledCards;
  }

  protected function notifyPlayers(int $gameId, mixed $message): void{

    $update = new Update('#play-'.$gameId,json_encode($message));
    $this->hub->publish($update);

  }

}