<?php

namespace App\Repository;

use App\Entity\Game;
use App\Enum\GameState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 *
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

   /**
    * @return Game[] Returns an array of Game objects
    */
   public function getOpenGames(): array
   {
       return $this->createQueryBuilder('g')
           ->andWhere('g.state = :state')
           ->setParameter('state', GameState::OPEN->value)
           ->orderBy('g.createdAt', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }

   public function save(Game $game): Game{
        $this->_em->persist($game);
        $this->_em->flush();
        return $game;
   }

}
