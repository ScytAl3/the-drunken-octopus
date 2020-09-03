<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;
use App\Repository\ProductRepository;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;
// Validates that a particular field (or fields) in a Doctrine entity is (are) unique
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
// Link the upload mapping to Product entity
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ORM\Table(name="products")
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("title")
 */
class Product
{
    use Timestampable;

    /**
     * The identifier of the product.
     * 
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * The name of the product.
     * 
     * @var string
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
     * The description of the product.
     * 
     * @var string
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
     * The color of the product.
     * 
     * @var string
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
     * The ibu value of the product.
     * 
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\PositiveOrZero
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $ibu;

    /**
     * Alcool By Volume of the product.
     * 
     * @var string
     * @ORM\Column(type="decimal", precision=4, scale=2)
     * @Assert\NotBlank
     * @Assert\Positive
     */
    private $alcohol;

    /**
     * The price of the product.
     * 
     * @var string
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Assert\NotBlank
     * @Assert\Positive
     * 
     */
    private $price;

    /**
     * The stock quantity of the product.
     * 
     * @var int
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
     * Product availability
     * 
     * @var bool
     * @ORM\Column(type="boolean", options={"default": true})
     */
    private $availability = true;

    /**
     * Style of the product
     * 
     * @var Style
     * @ORM\ManyToOne(targetEntity=Style::class, inversedBy="products")
     */
    private $style;

    /**
     * Country of the product
     * 
     * @var Country
     * @ORM\ManyToOne(targetEntity=Country::class, inversedBy="products")
     */
    private $country;

    /**
     * Brewery of the product
     * 
     * @var Brewery
     * @ORM\ManyToOne(targetEntity=Brewery::class, inversedBy="products")
     */
    private $brewery;

    /**
     * Bottle capacity yof the product
     * 
     * @var Bottle
     * @ORM\ManyToOne(targetEntity=Bottle::class, inversedBy="products")
     */
    private $bottle;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="product_image", fileNameProperty="imageName")
     * @Assert\Image(
     *      maxSize="1M",
     * )
     * 
     * @var File|null
     */
    private $imageFile;

    /**
     * Image name of the product
     * 
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageName;

    /**
     * @ORM\OneToMany(targetEntity=PurchaseProduct::class, mappedBy="product")
     */
    private $purchaseProducts;

    public function __construct()
    {
        $this->purchaseProducts = new ArrayCollection();
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

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->setUpdatedAt(new \DateTimeImmutable);
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * @return Collection|PurchaseProduct[]
     */
    public function getPurchaseProducts(): Collection
    {
        return $this->purchaseProducts;
    }

    public function addPurchaseProduct(PurchaseProduct $purchaseProduct): self
    {
        if (!$this->purchaseProducts->contains($purchaseProduct)) {
            $this->purchaseProducts[] = $purchaseProduct;
            $purchaseProduct->setProduct($this);
        }

        return $this;
    }

    public function removePurchaseProduct(PurchaseProduct $purchaseProduct): self
    {
        if ($this->purchaseProducts->contains($purchaseProduct)) {
            $this->purchaseProducts->removeElement($purchaseProduct);
            // set the owning side to null (unless already changed)
            if ($purchaseProduct->getProduct() === $this) {
                $purchaseProduct->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * Retourne le nombre de jours entre la date du jour et la date de création
     * d'un produit
     *
     * @return integer
     */
    public function daysLeft(): int
    {
        // Initialisation de la date courante
        $now = (new \DateTimeImmutable())->format('Y-m-d');
        // Calcul de la différence en timestamps 
        $diff = strtotime($now) - strtotime($this->createdAt->format('Y-m-d'));

        // 1 day = 24 hours 
        // 24 * 60 * 60 = 86400 seconds 
        return abs(round($diff / 86400)); 
    }

    /**
     * Retourne TRUE si la date de création du produit est inférieur à 30 jours
     * sinon FALSE
     *
     * @return boolean
     */
    public function isNovelty(): bool
    {
        return ($this->daysLeft() < 30);
    }
}
