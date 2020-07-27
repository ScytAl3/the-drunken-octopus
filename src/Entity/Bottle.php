<?php

namespace App\Entity;

use App\Repository\BottleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
// Validates that a particular field (or fields) in a Doctrine entity is (are) unique
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=BottleRepository::class)
 * @ORM\Table(name="bottles")
 * @UniqueEntity("capacity")
 */
class Bottle
{
    /**
     * The identifier of the bottle capacity.
     * 
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * The capacity of the bottle.
     * 
     * @var float
     * @ORM\Column(type="float")
     * @Assert\NotBlank
     * @Assert\Positive
     * @Assert\Type(type="float", message="The value {{ value }} must be {{ type }}")
     * 
     */
    private $capacity;

    /**
     * Products in this capacity.
     * 
     * @var Collection|Product[]
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="bottle")
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

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

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setBottle($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getBottle() === $this) {
                $product->setBottle(null);
            }
        }

        return $this;
    }

    /**
     * Permet de recupérer le nom des Styles
     * @return string 
     */
    public function __toString()
    {
        return (string) $this->capacity;
    }
}
