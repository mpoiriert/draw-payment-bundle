<?php

namespace Draw\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @ORM\Table(name="draw_payment__tax")
 */
class Tax
{
    /**
     * The order associated to the tax
     *
     * @var Order
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Draw\PaymentBundle\Entity\Order", inversedBy="orderTaxes")
     * @ORM\JoinColumn(name="order_id", nullable=false, onDelete="CASCADE")
     *
     * @Assert\NotNull()
     */
    private $order;

    /**
     * Name of the tax
     * @var string
     *
     * @ORM\Id()
     * @ORM\Column(type="string", length=25, nullable=false)
     *
     * @Assert\Length(min="1", max="25")
     * @Assert\Type("string")
     *
     * @Serializer\Expose()
     */
    private $taxName;

    /**
     * Total for this tax for this order
     * @var float
     *
     * @ORM\Column(type="float", nullable=false)
     *
     * @Assert\Type("float")
     * @Assert\NotNull()
     *
     * @Serializer\Expose()
     */
    private $total;

    /**
     * Number of the tax (TVQXXXXXX)
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     *
     * @Assert\Type("string")
     * @Assert\Length(max="50", min="1")
     * @Assert\NotNull()
     *
     * @Serializer\Expose()
     */
    private $taxNumber;

    /**
     * The rate of the tax
     *
     * We keep it since the rate can change over time
     *
     * @var float
     *
     * @ORM\Column(type="float", nullable=false)
     * @Assert\NotNull()
     * @Assert\Type("float")
     *
     * @Serializer\Expose()
     */
    private $rate;

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
    public function setOrder($order)
    {
        $this->order = $order;
        $order->addTax($this);
    }

    /**
     * @return string
     */
    public function getTaxNumber()
    {
        return $this->taxNumber;
    }

    /**
     * @param string $taxNumber
     */
    public function setTaxNumber($taxNumber)
    {
        $this->taxNumber = $taxNumber;
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param float $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return string
     */
    public function getTaxName()
    {
        return $this->taxName;
    }

    /**
     * @param string $taxName
     */
    public function setTaxName($taxName)
    {
        $this->taxName = $taxName;
    }

    /**
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param float $rate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    }
}