<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;
use Symfony\Component\Validator\Constraints as Assert;
// Validates that a particular field (or fields) in a Doctrine entity is (are) unique
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @UniqueEntity("title")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(min=3)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     *      min=10,
     *      minMessage="La description doit faire {{ limit }} caractères au minimum",
     *      allowEmptyString = false
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Regex(
     *      pattern="/\d/",
     *      match=false,
     *      message="The color cannot contain a number"
     * )
     */
    private $color;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ibu;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2)
     * @Assert\NotBlank
     */
    private $alcohol;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Assert\NotBlank
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     */
    private $quantity;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     */
    private $availability = true;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /*----------------------------------------------------------------------------
                                    Constructeur
    ----------------------------------------------------------------------------*/
    /**
     * constructeur qui initialise la date de creation au Datetime de la creation de l'Event 
     */
    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    /*-----------------------------------------------------------------------------
                                    Getters - Setters 
    -----------------------------------------------------------------------------*/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): string
    {
        return (new Slugify())->slugify($this->title);
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getIbu(): ?int
    {
        return $this->ibu;
    }

    public function setIbu(?int $ibu): self
    {
        $this->ibu = $ibu;

        return $this;
    }

    public function getAlcohol(): ?string
    {
        return $this->alcohol;
    }

    public function setAlcohol(?string $alcohol): self
    {
        $this->alcohol = $alcohol;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Formate le prix du produit
     * 
     * @return string 
     */
    public function getFormatedPrice(): string
    {
        return number_format($this->price, 2, ',', ' ');
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getAvailability(): ?bool
    {
        return $this->availability;
    }

    public function setAvailability(bool $availability): self
    {
        $this->availability = $availability;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
