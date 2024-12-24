<?php

namespace App\Entity;

use App\Repository\CheckpointRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CheckpointRepository::class)]
class Checkpoint
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'checkpoints')]
    private ?RoadTrip $road_trip = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $coordonnees = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $arriver_date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $depart_date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoadTrip(): ?RoadTrip
    {
        return $this->road_trip;
    }

    public function setRoadTrip(?RoadTrip $road_trip): static
    {
        $this->road_trip = $road_trip;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCoordonnees(): ?string
    {
        return $this->coordonnees;
    }

    public function setCoordonnees(string $coordonnees): static
    {
        $this->coordonnees = $coordonnees;

        return $this;
    }

    public function getArriverDate(): ?\DateTimeInterface
    {
        return $this->arriver_date;
    }

    public function setArriverDate(\DateTimeInterface $arriver_date): static
    {
        $this->arriver_date = $arriver_date;

        return $this;
    }

    public function getDepartDate(): ?\DateTimeInterface
    {
        return $this->depart_date;
    }

    public function setDepartDate(\DateTimeInterface $depart_date): static
    {
        $this->depart_date = $depart_date;

        return $this;
    }
}
