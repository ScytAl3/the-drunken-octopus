<?php

namespace App\Entity;

use App\Repository\BootleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BootleRepository::class)
 */
class Bootle
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $capacity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCapacity(): ?float
    {
        return $this->capacity;
    }

    public function setCapacity(float $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }
}
