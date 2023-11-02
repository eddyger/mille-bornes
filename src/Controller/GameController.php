<?php

namespace App\Controller;

use App\Entity\Game;
use App\Exception\MaxPlayerReachedException;
use App\Form\NewGameFormType;
use App\Service\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/game')]
class GameController extends AbstractController {

  public function __construct(protected readonly GameService $gameService)
  {
    
  }

  #[Route('/list', name:'app_game_list')]
  public function list(): Response {

    return $this->render('game/list.html.twig', ['games' => $this->gameService->getOpenGames()]);

  }
  
  #[Route('/new', name:'app_game_new')]
  public function new(Request $request): Response {
    $gameForm = $this->createForm(NewGameFormType::class);

    $gameForm->handleRequest($request);

    if ($gameForm->isSubmitted() && $gameForm->isValid()){
      $game = $this->gameService->createGame($gameForm->getData());
      return $this->redirectToRoute('app_game_play', ['id' => $game->getId()]);
    }

    return $this->renderForm('game/new.html.twig', ['form' => $gameForm]);
  }

  #[Route('/play/{id}', name:'app_game_play')]
  public function play(int $id): Response {
    try{
      $game = $this->gameService->joinGame($id,$this->getUser()); 
      if (null === $game){
        throw $this->createNotFoundException('Game not found');
      }
    }catch(MaxPlayerReachedException $e){
      $this->addFlash('error' , 'maximum players reached !!!');
      return $this->redirectToRoute('app_home');
    }
     
    return $this->render('game/play.html.twig', ['game' => $game]);

  }
  

}