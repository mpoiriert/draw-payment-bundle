<?php

namespace Draw\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Draw\PaymentBundle\Application\ProductInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @ORM\Table("draw_payment__item")
 */
class Item
{
    /**
     * A unique id for the entity
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * Order associated to this orderProduct
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="Draw\PaymentBundle\Entity\Order", inversedBy="items")
     * @ORM\JoinColumn(name="order_id", onDelete="CASCADE", nullable=false)
     *
     * @Assert\NotNull()
     * @Assert\Type(Order::class)
     */
    private $order;

    /**
     * The product stock keeping unit
     *
     * @var string
     *
     * @ORM\Column(type="string", length=40, nullable=false)
     *
     * @Assert\NotNull()
     * @Assert\Type("string")
     */
    private $sku;

    /**
     * @var ProductInterface
     *
     * @ORM\ManyToOne(targetEntity="Draw\PaymentBundle\Application\ProductInterface")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $applicationProduct;

    /**
     * The quantity of the item product in the order
     *
     * @ORM\Column(type="integer")
     * @Assert\NotNull()
     * @Assert\Type("int")
     */
    private $quantity;

    /**
     * The unit price of the product
     *
     * @ORM\Column(type="float")
     * @Assert\NotNull()
     * @Assert\Type("float")
     */
    private $unitPrice;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getTotalPrice()
    {
        return round($this->quantity * $this->unitPrice, 2);
    }

    /**
     * @return float
     */
    public function getUnitPrice()
    {
        return round($this->unitPrice, 2);
    }

    /**
     * @param float $unitPrice
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = round($unitPrice, 2);
    }

    /**
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param integer $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    /**
     * @return ProductInterface
     */
    public function getApplicationProduct()
    {
        return $this->applicationProduct;
    }

    /**
     * @param ProductInterface $applicationProduct
     */
    public function setApplicationProduct(ProductInterface $applicationProduct = null)
    {
        $this->applicationProduct = $applicationProduct;
        if($applicationProduct) {
            $this->setSku($applicationProduct->getApplicationSku());
        }
    }
}