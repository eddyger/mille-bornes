<?php

namespace App\DataFixtures;

use App\Entity\Card;
use App\Enum\CardType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->createAttackCards($manager);
        $this->createDefenseCards($manager);
        $this->createWeaponCards($manager);
        $this->createDistanceCards($manager);
    }

    protected function createAttackCards(ObjectManager $manager){
        /**
         * Les cartes « attaques » :
            3 cartes « Accident de la route »
            3 cartes « Panne d’essence »
            3 cartes « Crevaison »
            4 cartes « Limitation de vitesse »
            5 cartes « Feu rouge »
         */
        $card = new Card();
        $card->setCode('ROAD_ACCIDENT');
        $card->setType(CardType::ATTACK);
        $card->setNumberInGame(3);
        $manager->persist($card);

        $card = new Card();
        $card->setCode('OUT_OF_FUEL');
        $card->setType(CardType::ATTACK);
        $card->setNumberInGame(3);
        $manager->persist($card);

        $card = new Card();
        $card->setCode('FLAT_TIRE');
        $card->setType(CardType::ATTACK);
        $card->setNumberInGame(3);
        $manager->persist($card);

        $card = new Card();
        $card->setCode('SPEED_LIMIT');
        $card->setType(CardType::ATTACK);
        $card->setNumberInGame(4);
        $manager->persist($card);

        $card = new Card();
        $card->setCode('RED_LIGHT');
        $card->setType(CardType::ATTACK);
        $card->setNumberInGame(5);
        $manager->persist($card);
    
        $manager->flush();
    }

    protected function createDefenseCards(ObjectManager $manager) :void {
        /**
         * Les cartes « défense » :
            6 cartes « Réparations »
            6 cartes « Essence »
            6 cartes « Roue de secours »
            6 cartes « Fin de limitation de vitesse »
            14 cartes « Feu vert »
         */

        $card = new Card();
        $card->setCode('REPAIR');
        $card->setType(CardType::DEFENSE);
        $card->setNumberInGame(6);
        $manager->persist($card);

        $card = new Card();
        $card->setCode('FUEL');
        $card->setType(CardType::DEFENSE);
        $card->setNumberInGame(6);
        $manager->persist($card);

        $card = new Card();
        $card->setCode('SPARE_WHEEL');
        $card->setType(CardType::DEFENSE);
        $card->setNumberInGame(6);
        $manager->persist($card);

        $card = new Card();
        $card->setCode('END_OF_SPEED_LIMIT');
        $card->setType(CardType::DEFENSE);
        $card->setNumberInGame(6);
        $manager->persist($card);

        $card = new Card();
        $card->setCode('GREEN_LIGHT');
        $card->setType(CardType::DEFENSE);
        $card->setNumberInGame(14);
        $manager->persist($card);
    
        $manager->flush();

    }

    protected function createWeaponCards(ObjectManager $manager): void{
        /**
         * Les cartes « Bottes » :
            1 carte As du volant
            1 carte Camion-Citerne
            1 carte Increvable
            1 carte Prioritaire
         */

        $card = new Card();
        $card->setCode('ACE_DRIVER');
        $card->setType(CardType::WEAPON);
        $card->setNumberInGame(1);
        $manager->persist($card);

        $card = new Card();
        $card->setCode('FUEL_TANKER');
        $card->setType(CardType::WEAPON);
        $card->setNumberInGame(1);
        $manager->persist($card);

        $card = new Card();
        $card->setCode('PUNCTURE_PROOF');
        $card->setType(CardType::WEAPON);
        $card->setNumberInGame(1);
        $manager->persist($card);

        $card = new Card();
        $card->setCode('TOP_PRIORITY');
        $card->setType(CardType::WEAPON);
        $card->setNumberInGame(1);
        $manager->persist($card);

        $manager->flush();

    }

    protected function createDistanceCards(ObjectManager $manager): void {
        /**
         * Les cartes « distance » :
            10 cartes « 25kms » (escargot)
            10 cartes « 50 kms » (canard)
            10 cartes « 75 kms » (papillon)
            12 cartes « 100 kms » (lièvre)
            4 cartes « 200 kms » (hirondelle)
         */

        $card = new Card();
        $card->setCode('SNAIL');
        $card->setType(CardType::DISTANCE);
        $card->setNumberInGame(10);
        $card->setDistance(25);
        $manager->persist($card);

        $card = new Card();
        $card->setCode('DUCK');
        $card->setType(CardType::DISTANCE);
        $card->setNumberInGame(10);
        $card->setDistance(50);
        $manager->persist($card);

        $card = new Card();
        $card->setCode('BUTTER_FLY');
        $card->setType(CardType::DISTANCE);
        $card->setNumberInGame(10);
        $card->setDistance(75);
        $manager->persist($card);

        $card = new Card();
        $card->setCode('HARE');
        $card->setType(CardType::DISTANCE);
        $card->setNumberInGame(12);
        $card->setDistance(100);
        $manager->persist($card);

        $card = new Card();
        $card->setCode('SWALLOW'); //Hirondelle
        $card->setType(CardType::DISTANCE);
        $card->setNumberInGame(4);
        $card->setDistance(200);
        $manager->persist($card);

        $manager->flush();
        
    }
}
