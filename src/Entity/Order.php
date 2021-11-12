<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", unique=true)
     */
    private $order_id;
    
     /**
     * @ORM\Column(type="integer")
     */
    private $order_quantity;

    /**
     * @ORM\Column(type="integer")
     */
    private $order_total;

    /**
     * @ORM\Column(type="integer")
     */
    private $order_discount;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $shipping_address;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $province;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $postal_code;

     /**
     * @var Customer|null
     * 
     * @ORM\ManyToOne(targetEntity=Customer::class, fetch="EAGER")
     */
    private $customer;
    
    /**
     * @var Collection|Item[]
     *
     * @ORM\ManyToMany(targetEntity=Item::class, fetch="EAGER")
     */
    private $item;

    /**
     * @ORM\ManyToOne(targetEntity=Status::class, fetch="EAGER")
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): ?int
    {
        return $this->order_id;
    }

    public function setOrderId(int $order_id): self
    {
        $this->order_id = $order_id;

        return $this;
    }

    public function getOrderTotal(): ?int
    {
        return $this->order_total;
    }

    public function setOrderTotal(int $order_total): self
    {
        $this->order_total = $order_total;

        return $this;
    }

    public function getOrderDiscount(): ?int
    {
        return $this->order_discount;
    }

    public function setOrderDiscount(int $order_discount): self
    {
        $this->order_discount = $order_discount;

        return $this;
    }

    /**
     * @return \DateTime|null
    */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }
    
    /**
     * @param \DateTime|null $dateTime
    */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    
    /**
     * @return \DateTime|null
    */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime|null $dateTime
    */
    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getShippingAddress(): ?string
    {
        return $this->shipping_address;
    }

    public function setShippingAddress(string $shipping_address): self
    {
        $this->shipping_address = $shipping_address;

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

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(string $province): self
    {
        $this->province = $province;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(string $postal_code): self
    {
        $this->postal_code = $postal_code;

        return $this;
    }
    
    /**
    * @param Customer|null $customer
    */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }
    
    /**
    * @param Customer|null $customer
    */
    public function setCustomer(?Customer $customer): void
    {
        $this->customer = $customer;
    }
    
    /**
    * @return Item[]|Collection
    */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param Item[]|Collection $item
     */
    public function setItem($item): void
    {
        $this->item = $item;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getOrderQuantity(): ?int
    {
        return $this->order_quantity;
    }

    public function setOrderQuantity(int $order_quantity): self
    {
        $this->order_quantity = $order_quantity;

        return $this;
    }
    
}