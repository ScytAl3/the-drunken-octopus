<?php

namespace App\Entity;

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
}
