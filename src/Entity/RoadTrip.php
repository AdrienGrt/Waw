<?php

namespace App\Entity;

use App\Repository\RoadTripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoadTripRepository::class)]
class RoadTrip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_supplementaire = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\NotNull(message: 'La date de départ est obligatoire.')]
    private ?\DateTimeInterface $depart_date = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $arriver_date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $depart_address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $arrive_address = null;

    #[ORM\ManyToOne(inversedBy: 'roadTrips')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'roadTrips')]
    private ?Vehicle $vehicle = null;

    #[ORM\OneToMany(mappedBy: 'road_trip', targetEntity: Checkpoint::class)]
    private Collection $checkpoints;

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

    public function __construct()
    {
        $this->checkpoints = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getImageSupplementaire(): ?string
    {
        return $this->image_supplementaire;
    }

    public function setImageSupplementaire(?string $image_supplementaire): static
    {
        $this->image_supplementaire = $image_supplementaire;

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

    public function getDepartAddress(): ?string
    {
        return $this->depart_address;
    }

    public function setDepartAddress(?string $depart_address): static
    {
        $this->depart_address = $depart_address;

        return $this;
    }

    public function getArriveAddress(): ?string
    {
        return $this->arrive_address;
    }

    public function setArriveAddress(?string $arrive_address): static
    {
        $this->arrive_address = $arrive_address;

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

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): static
    {
        $this->vehicle = $vehicle;

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
            if ($checkpoint->getRoadTrip() === $this) {
                $checkpoint->setRoadTrip(null);
            }
        }

        return $this;
    }

    public function getDuree(): ?int
    {
        if ($this->depart_date && $this->arriver_date) {
            return $this->arriver_date->diff($this->depart_date)->days;
        }

        return null;
    }
}
