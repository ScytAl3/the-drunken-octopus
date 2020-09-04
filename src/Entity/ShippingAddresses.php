<?php

namespace App\Entity;

use App\Repository\ShippingAddressesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
// Validates that a particular field (or fields) in a Doctrine entity is (are) unique
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ShippingAddressesRepository::class)
 */
class ShippingAddresses
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
     * @Assert\Length(
     *      min = 3,
     *      max = 50,
     *      minMessage = "The name of the product must be at least {{ limit }} characters long",
     *      maxMessage = "The name of the product cannot be longer than {{ limit }} characters",
     *      allowEmptyString = false
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 10,
     *      minMessage = "The description of the product must be at least {{ limit }} characters long",
     *      allowEmptyString = false
     * )
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=12)
     */
    private $zipcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     */
    private $sameForBilling;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="shippingAddress")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=PurchaseOrder::class, mappedBy="shippingAddress")
     */
    private $purchaseOrders;

    public function __construct()
    {
        $this->purchaseOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getSameForBilling(): ?bool
    {
        return $this->sameForBilling;
    }

    public function setSameForBilling(bool $sameForBilling): self
    {
        $this->sameForBilling = $sameForBilling;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|PurchaseOrder[]
     */
    public function getPurchaseOrders(): Collection
    {
        return $this->purchaseOrders;
    }

    public function addPurchaseOrder(PurchaseOrder $purchaseOrder): self
    {
        if (!$this->purchaseOrders->contains($purchaseOrder)) {
            $this->purchaseOrders[] = $purchaseOrder;
            $purchaseOrder->setShippingAddress($this);
        }

        return $this;
    }

    public function removePurchaseOrder(PurchaseOrder $purchaseOrder): self
    {
        if ($this->purchaseOrders->contains($purchaseOrder)) {
            $this->purchaseOrders->removeElement($purchaseOrder);
            // set the owning side to null (unless already changed)
            if ($purchaseOrder->getShippingAddress() === $this) {
                $purchaseOrder->setShippingAddress(null);
            }
        }

        return $this;
    }
}
