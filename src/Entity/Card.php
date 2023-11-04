<?php

namespace App\Entity;

use App\Enum\CardType;
use App\Repository\CardRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255,unique:true)]
    private ?string $code = null;

    #[ORM\Column]
    private ?int $numberInGame = null;

    #[ORM\Column(type:"string", length: 255, enumType: CardType::class)]
    private ?CardType $type = null;

    #[ORM\Column(nullable: true)]
    private ?int $distance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getNumberInGame(): ?int
    {
        return $this->numberInGame;
    }

    public function setNumberInGame(int $numberInGame): static
    {
        $this->numberInGame = $numberInGame;

        return $this;
    }

    public function getType(): ?CardType
    {
        return $this->type;
    }

    public function setType(CardType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(?int $distance): static
    {
        $this->distance = $distance;

        return $this;
    }
}
