<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;
use App\Repository\ProductRepository;
use Symfony\Component\Validator\Constraints as Assert;
// Validates that a particular field (or fields) in a Doctrine entity is (are) unique
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ORM\Table(name="products")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("title")
 */
class Product
{
    use Timestampable;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 3,
     *      max = 50,
     *      minMessage = "The name of the product must be at least {{ limit }} characters long",
     *      maxMessage = "The name of the product cannot be longer than {{ limit }} characters",
     *      allowEmptyString = false
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 10,
     *      minMessage = "The description of the product must be at least {{ limit }} characters long",
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
     * @Assert\Positive
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $ibu;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2)
     * @Assert\NotBlank
     * @Assert\Positive
     */
    private $alcohol;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Assert\NotBlank
     * @Assert\Positive
     * 
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\PositiveOrZero
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $quantity;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     */
    private $availability = true;

    /**
     * @ORM\ManyToOne(targetEntity=Style::class, inversedBy="products")
     */
    private $style;

    /**
     * @ORM\ManyToOne(targetEntity=Country::class, inversedBy="products")
     */
    private $country;

    /**
     * @ORM\ManyToOne(targetEntity=Brewery::class, inversedBy="products")
     */
    private $brewery;

    /**
     * @ORM\ManyToOne(targetEntity=Bottle::class, inversedBy="products")
     */
    private $bottle;
    
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

    public function getStyle(): ?Style
    {
        return $this->style;
    }

    public function setStyle(?Style $style): self
    {
        $this->style = $style;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getBrewery(): ?Brewery
    {
        return $this->brewery;
    }

    public function setBrewery(?Brewery $brewery): self
    {
        $this->brewery = $brewery;

        return $this;
    }

    public function getBottle(): ?Bottle
    {
        return $this->bottle;
    }

    public function setBottle(?Bottle $bottle): self
    {
        $this->bottle = $bottle;

        return $this;
    }
}
