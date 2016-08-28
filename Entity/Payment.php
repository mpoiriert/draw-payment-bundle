<?php

namespace Draw\PaymentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @ORM\Table(
 *     name="draw_payment__payment",
 *     indexes={
 *         @ORM\Index(name="gatewayName", columns={"gatewayName"}),
 *         @ORM\Index(name="providerTransactionReference", columns={"providerTransactionReference"})
 *      }
 * )
 *
 * @Serializer\ExclusionPolicy("all")
 */
class Payment
{
    const STATE_PENDING = 'PENDING';
    const STATE_PENDING_USER = 'PENDING_USER';
    const STATE_SUCCESS = 'SUCCESS';
    const STATE_CANCEL = 'CANCEL';
    const STATE_ERROR = 'ERROR';


    /**
     * A unique id for the entity
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Expose()
     */
    private $id;

    /**
     * Order associated to this payment
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="Draw\PaymentBundle\Entity\Order", inversedBy="payments")
     * @ORM\JoinColumn(name="order_id", onDelete="CASCADE", nullable=false)
     *
     * @Assert\NotNull()
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"draw-payment:create", "draw-payment:read"})
     */
    private $order;

    /**
     * Return the payment state
     *
     * @var string
     *
     * @ORM\Column(type="string", length=20, nullable=false)
     *
     * @Assert\Choice({"PENDING" ,"PENDING_USER", "SUCCESS", "ERROR", "CANCEL"})
     * @Assert\NotNull()
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"draw-payment:read"})
     */
    private $state;

    /**
     * The gateway name
     *
     * @var string
     *
     * @ORM\Column(type="string", length=40, nullable=false)
     * @Assert\NotNull()
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"draw-payment:create", "draw-payment:read"})
     */
    private $gatewayName;

    /**
     * The gateway transaction reference
     *
     * @var string
     *
     * @ORM\Column(type="string", length=40, nullable=true)
     *
     * @Serializer\Expose()
     */
    private $providerTransactionReference;

    /**
     * The amount of this payment. Always a positive value, if it's a refund, the isRefund property will be true.
     *
     * @var float
     *
     * @ORM\Column(type="float", nullable=false)
     * @Assert\NotNull()
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"draw-payment:read"})
     */
    private $amount;

    /**
     * Is it a refund ?
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default":false})
     * @Assert\NotNull()
     *
     * @Serializer\Expose()
     */
    private $isRefund = false;

    /**
     * The redirect to the payment gateway to complete the payment if the status is PENDING_USER
     *
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Url()
     *
     * @Serializer\Expose()
     */
    private $redirectUrl;

    /**
     * Product(s) from the order
     * @var Transaction[]
     *
     * @ORM\OneToMany(
     *  targetEntity="Draw\PaymentBundle\Entity\Transaction",
     *  mappedBy="payment",
     *  cascade={"persist","remove","merge"},
     *  orphanRemoval=true
     * )
     */
    private $transactions;

    /**
     * Product(s) from the order
     * @var array
     *
     * @ORM\Column(type="json_array")
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"draw-payment:create"})
     */
    private $data = [];

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
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
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getGatewayName()
    {
        return $this->gatewayName;
    }

    /**
     * @param string $providerName
     */
    public function setGatewayName($gatewayName)
    {
        $this->gatewayName = $gatewayName;
    }

    /**
     * @return string
     */
    public function getProviderTransactionReference()
    {
        return $this->providerTransactionReference;
    }

    /**
     * @param string $providerTransactionReference
     */
    public function setProviderTransactionReference($providerTransactionReference)
    {
        $this->providerTransactionReference = $providerTransactionReference;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return round($this->amount, 2);
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = round($amount, 2);
    }

    /**
     * @return boolean
     */
    public function getIsRefund()
    {
        return $this->isRefund;
    }

    /**
     * @param boolean $isRefund
     */
    public function setIsRefund($isRefund)
    {
        $this->isRefund = $isRefund;
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * @param string $redirectUrl
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * @return Transaction[]
     */
    public function getTransactions()
    {
        return $this->transactions->toArray();
    }

    /**
     * @param Transaction $transaction
     */
    public function addTransaction(Transaction $transaction)
    {
        if(!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setPayment($this);
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function __toString()
    {
        return $this->getGatewayName() . ' (' . $this->getId() . ')';
    }
}