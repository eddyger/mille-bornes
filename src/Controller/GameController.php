<?php

namespace App\Controller;

use App\Entity\Game;
use App\Exception\GameAlreadyStartException;
use App\Exception\MaxPlayerReachedException;
use App\Exception\MemoryException;
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
      return $this->redirectToRoute('app_game_join', ['id' => $game->getId()]);
    }

    return $this->renderForm('game/new.html.twig', ['form' => $gameForm]);
  }

  #[Route('/join/{id}', name:'app_game_join')]
  public function join(int $id): Response {
    try{
      $game = $this->gameService->joinGame($id,$this->getUser()); 
      if (null === $game){
        throw $this->createNotFoundException('Game not found');
      }
    }catch(MaxPlayerReachedException $e){
      $this->addFlash('error' , 'maximum players reached !!!');
      return $this->redirectToRoute('app_game_list');
    }
     
    return $this->render('game/play.html.twig', ['game' => $game]);

  }
  
  #[Route('/start/{id}', name:'app_game_start')]
  public function start(int $id): Response {
    try{
      $game = $this->gameService->findBydId($id); 
      if (null === $game){
        throw $this->createNotFoundException('Game not found');
      }
      $engine = $this->gameService->startNewGame($game);
      return $this->render('game/play.html.twig', ['game' => $game, 'engine' => $engine]);
    }catch(GameAlreadyStartException $e){
      $this->addFlash('error' , 'game is already started !!!');
      return $this->redirectToRoute('app_game_list');
    }
    
  }

  #[Route('/take-card-in-deck/{id}', name:'app_game_take_card_in_deck')]
  public function takeCardInDeck(int $id): Response {
    try{
      $game = $this->gameService->findBydId($id); 
      if (null === $game){
        throw $this->createNotFoundException('Game not found');
      }
      $card = $this->gameService->takeCardInDeck($game, $this->getUser());
      $response = new Response(null,200,['Content-type' => 'text/vnd.turbo-stream.html']);
      return $this->render('game/newcard.stream.html.twig', ['game' => $game, 'card' => $card],$response);
    }catch(MemoryException $e){
      $this->addFlash('error' , 'game is unplayable due to memory exception !!!');
      return $this->redirectToRoute('app_game_list');
    }
    
  }

  #[Route('/play-cards/{id}', name:'app_game_play_cards', methods:['POST'])]
  public function playCards(int $id, Request $request): Response {
    // We can play two cards
    // Case 1: 1 card in trash , 1 card in table
    // Case 2: 1 card in trash , 1 card in opponent table attack
    /* Json body : {
        'trash' : 'cardCode',  
        'table': 'cardCode'
        'opponent' : {'card':'cardCode', 'player':'playerId'}
      }
    */
    try{
      $game = $this->gameService->findBydId($id); 
      if (null === $game){
        throw $this->createNotFoundException('Game not found');
      }
      $this->gameService->playCards($game, $this->getUser(), json_decode($request->getContent(),true));
      $response = new Response(null,200,['Content-type' => 'text/vnd.turbo-stream.html']);
      return $this->render('game/playcards.stream.html.twig',[],$response);
    }catch(MemoryException $e){
      $this->addFlash('error' , 'game is unplayable due to memory exception !!!');
      return $this->redirectToRoute('app_game_list');
    }
    
  }



}