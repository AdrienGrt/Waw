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

    #[ORM\OneToMany(mappedBy: 'vehicle', targetEntity: RoadTrip::class)]
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
            if ($roadTrip->getVehicle() === $this) {
                $roadTrip->setVehicle(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        // Retourne une représentation textuelle de l'objet
        return $this->type ?? 'Unknown Vehicle';
    }
}
