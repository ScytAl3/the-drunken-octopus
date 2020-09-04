<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;
use App\Repository\PurchaseOrderRepository;

/**
 * @ORM\Entity(repositoryClass=PurchaseOrderRepository::class)
 * @ORM\Table(name="purchase_orders")
 * @ORM\HasLifecycleCallbacks()
 */
class PurchaseOrder
{
    use Timestampable;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $payement;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="purchaseOrders")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=PurchaseProduct::class, mappedBy="purchaseOrder")
     */
    private $purchaseProducts;

    /**
     * @ORM\Column(type="float")
     */
    private $totalPrice;

    /**
     * @ORM\ManyToOne(targetEntity=ShippingAddresses::class, inversedBy="purchaseOrders")
     */
    private $shippingAddress;

    /**
     * @ORM\ManyToOne(targetEntity=ShippingAddresses::class, inversedBy="purchaseOrders")
     */
    private $billingAddress;

    public function __construct()
    {
        $this->purchaseProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPayement(): ?bool
    {
        return $this->payement;
    }

    public function setPayement(bool $payement): self
    {
        $this->payement = $payement;

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
            $purchaseProduct->setPurchaseOrder($this);
        }

        return $this;
    }

    public function removePurchaseProduct(PurchaseProduct $purchaseProduct): self
    {
        if ($this->purchaseProducts->contains($purchaseProduct)) {
            $this->purchaseProducts->removeElement($purchaseProduct);
            // set the owning side to null (unless already changed)
            if ($purchaseProduct->getPurchaseOrder() === $this) {
                $purchaseProduct->setPurchaseOrder(null);
            }
        }

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getShippingAddress(): ?ShippingAddresses
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(?ShippingAddresses $shippingAddress): self
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    public function getBillingAddress(): ?ShippingAddresses
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?ShippingAddresses $billingAddress): self
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }
}
