<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    /**
     * @var Collection<int, RoadTrip>
     */
    #[ORM\OneToMany(targetEntity: RoadTrip::class, mappedBy: 'vehicle')]
    private Collection $roadTrips;

    public function __construct()
    {
        $this->roadTrips = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, RoadTrip>
     */
    public function getRoadTrips(): Collection
    {
        return $this->roadTrips;
    }

    public function addRoadTrip(RoadTrip $roadTrip): static
    {
        if (!$this->roadTrips->contains($roadTrip)) {
            $this->roadTrips->add($roadTrip);
            $roadTrip->setVehicle($this);
        }

        return $this;
    }

    public function removeRoadTrip(RoadTrip $roadTrip): static
    {
        if ($this->roadTrips->removeElement($roadTrip)) {
            // set the owning side to null (unless already changed)
            if ($roadTrip->getVehicle() === $this) {
                $roadTrip->setVehicle(null);
            }
        }

        return $this;
    }
}
