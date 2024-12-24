<?php

namespace App\Entity;

use App\Repository\RoadTripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoadTripRepository::class)]
class RoadTrip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'roadTrips')]
    private ?User $user = null;

    /**
     * @var Collection<int, Checkpoint>
     */
    #[ORM\OneToMany(targetEntity: Checkpoint::class, mappedBy: 'road_trip')]
    private Collection $checkpoints;

    #[ORM\ManyToOne(inversedBy: 'roadTrips')]
    private ?Vehicle $vehicle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_supplementaire = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $depart_date = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $arriver_date = null;

    public function __construct()
    {
        $this->checkpoints = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }


    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }
    public function getImageSupplementaire(): ?string
    {
        return $this->image_supplementaire;
    }

    public function setImageSupplementaire(string $image): static
    {
        $this->image_supplementaire = $image;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Checkpoint>
     */
    public function getCheckpoints(): Collection
    {
        return $this->checkpoints;
    }

    public function addCheckpoint(Checkpoint $checkpoint): static
    {
        if (!$this->checkpoints->contains($checkpoint)) {
            $this->checkpoints->add($checkpoint);
            $checkpoint->setRoadTrip($this);
        }

        return $this;
    }

    public function removeCheckpoint(Checkpoint $checkpoint): static
    {
        if ($this->checkpoints->removeElement($checkpoint)) {
            // set the owning side to null (unless already changed)
            if ($checkpoint->getRoadTrip() === $this) {
                $checkpoint->setRoadTrip(null);
            }
        }

        return $this;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): static
    {
        $this->vehicle = $vehicle;

        return $this;
    }
    public function getDepartDate(): ?\DateTimeInterface
    {
        return $this->depart_date;
    }

    public function setDepartDate(?\DateTimeInterface $depart_date): static
    {
        $this->depart_date = $depart_date;

        return $this;
    }

    public function getArriverDate(): ?\DateTimeInterface
    {
        return $this->arriver_date;
    }
    public function setArriverDate(?\DateTimeInterface $arriver_date): static
    {
        $this->arriver_date = $arriver_date;

        return $this;
    }

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description_supplementaire = null;

    public function getDescriptionSupplementaire(): ?string
    {
        return $this->description_supplementaire;
    }

    public function setDescriptionSupplementaire(?string $description_supplementaire): static
    {
        $this->description_supplementaire = $description_supplementaire;

        return $this;
    }

    
}
