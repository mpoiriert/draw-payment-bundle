<?php

namespace Draw\PaymentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Draw\DrawBundle\Security\OwnedInterface;
use Draw\DrawBundle\Security\OwnerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @ORM\Table(
 *     name="draw_payment__order",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="uniqueId", columns={"uniqueId"})},
 *     indexes={
 *          @ORM\Index(name="clientEmail", columns={"clientEmail"}),
 *          @ORM\Index(name="clientId", columns={"clientId"}),
 *          @ORM\Index(name="state", columns={"state"}),
 *     }
 * )
 *
 * @ORM\HasLifecycleCallbacks()
 * @Serializer\ExclusionPolicy("all")
 */
class Order implements OwnedInterface
{
    /**
     * When the order is created but not sent to any payment provider
     */
    const STATE_NEW = "NEW";

    /**
     * When the order is currently in payment process
     */
    const STATE_IN_PROCESS = "IN_PROCESS";

    /**
     * Payment have been start but is not completed. Can be for credit card error, or cancel by user.
     */
    const STATE_INCOMPLETE = "INCOMPLETE";

    /**
     * Payment have been accepted
     */
    const STATE_PAID = "PAID";

    /**
     * Payment is accepted and order is completed. This is the last state of a regular order.
     */
    const STATE_COMPLETE = "COMPLETED";

    /**
     * A partial refund have been done on the order.
     */
    const STATE_PARTIALLY_REFUNDED = "PARTIALLY_REFUNDED";

    /**
     * The order is completely refunded
     */
    const STATE_REFUNDED = "REFUNDED";

    /**
     * The order is void. This state can be set at any point in the flow and the order should not be changed from there.
     */
    const STATE_VOID = "VOID";


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
     * @Serializer\Groups({"draw-order:read"})
     */
    private $id;

    /**
     * The unique id to identify the order not base on the auto increment
     *
     * @var string
     *
     * @ORM\Column(type="string", length=50)
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"draw-order:read"})
     */
    private $uniqueId;

    /**
     * The currency code
     *
     * @var string
     *
     * @ORM\Column(type="string", length=3)
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"draw-order:read"})
     */
    private $currencyCode;

    /**
     * The client id for this order.
     *
     * There is not relation on this since we don't want a cascade delete for order
     *
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $clientId;

    /**
     * The client email at the moment this order was created
     *
     * @var string
     *
     * @ORM\Column(type="string", nullable=true, length=255)
     * @Assert\Email()
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"draw-order:read", "draw-order:update", "draw-order:create"})
     */
    private $clientEmail;

    /**
     * Client name flattened
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Type("string")
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"draw-order:read", "draw-order:update", "draw-order:create"})
     */
    private $clientName;

    /**
     * Client's culture when he created the order. Default en
     * @var string
     *
     * @ORM\Column(type="string", length=5, nullable=false, options={"default": "en"})
     *
     * @Assert\Language()
     * @Assert\NotNull()
     *
     * @Serializer\Expose()
     */
    private $locale = 'en_US';

    /**
     * Country from client when he created the order
     * @var string
     *
     * @ORM\Column(type="string", length=5, nullable=true)
     *
     * @Assert\Country()
     *
     * @Serializer\Expose()
     */
    private $country;

    /**
     * Region from the client when he created the order
     * @var string
     *
     * @ORM\Column(type="string", length=5, nullable=true)
     *
     * @Assert\Type("string")
     * @Assert\Length(min=2, max=5)
     *
     * @Serializer\Expose()
     */
    private $regionCode;

    /**
     * Product(s) from the order
     * @var Item[]
     *
     * @ORM\OneToMany(
     *  targetEntity="Draw\PaymentBundle\Entity\Item",
     *  mappedBy="order",
     *  cascade={"persist"},
     *  orphanRemoval=true
     * )
     *
     * @Assert\Valid()
     */
    private $items;

    /**
     * Taxes computed from this order
     * @var Tax[]
     *
     * @ORM\OneToMany(
     *    targetEntity="Draw\PaymentBundle\Entity\Tax",
     *    mappedBy="order",
     *    cascade={"persist"},
     *    orphanRemoval=true
     * )
     *
     * @Serializer\Expose()
     */
    private $taxes;

    /**
     * Payment(s) for this order
     *
     * @var Payment[]
     *
     * @ORM\OneToMany(
     *    targetEntity="Draw\PaymentBundle\Entity\Payment",
     *    mappedBy="order",
     *    cascade={"persist"},
     *    orphanRemoval=true
     * )
     */
    private $payments;

    /**
     * This is the state of the order
     *
     * @var string
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     *
     * @Assert\Choice({"NEW", "IN_PROCESS", "INCOMPLETE", "PAID", "COMPLETED", "PARTIALLY_REFUNDED", "REFUNDED", "VOID", "ARCHIVED"})
     *
     * @Serializer\Expose()
     */
    private $state;

    /**
     * The total before the taxes
     *
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $totalWithoutTaxes;

    /**
     * The total that the client must pay
     *
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $totalWithTaxes;

    /**
     * The total that the client have been refunded
     *
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $totalRefunded;

    private $totalIsDirty = false;

    /**
     * The address of billing
     *
     * @var Address
     *
     * @ORM\Embedded(class = Address::class)
     *
     * @Serializer\Expose()
     * @Serializer\Type(Address::class)
     * @Serializer\Groups({"draw-order:read", "draw-order:update", "draw-order:create"})
     */
    private $billingAddress;

    /**
     * The address of shipping
     *
     * @var Address
     *
     * @ORM\Embedded(class = Address::class)
     *
     * @Serializer\Expose()
     * @Serializer\Type(Address::class)
     * @Serializer\Groups({"draw-order:read", "draw-order:update", "draw-order:create"})
     */
    private $shippingAddress;

    public function __construct()
    {
        $this->taxes = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->billingAddress = new Address();
        $this->shippingAddress = new Address();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    /**
     * @param string $uniqueId
     */
    public function setUniqueId($uniqueId)
    {
        $this->uniqueId = $uniqueId;
    }

    /**
     * @return string
     */
    public function getClientEmail()
    {
        return $this->clientEmail;
    }

    /**
     * @param string $clientEmail
     */
    public function setClientEmail($clientEmail)
    {
        $this->clientEmail = $clientEmail;
    }

    /**
     * @return string
     */
    public function getClientName()
    {
        return $this->clientName;
    }

    /**
     * @param string $clientName
     */
    public function setClientName($clientName)
    {
        $this->clientName = $clientName;
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param mixed $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @param string $currencyCode
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
    }

    public function computeTotals($force = false)
    {
        switch(true) {
            case $force:
            case $this->totalIsDirty:
            case is_null($this->totalWithoutTaxes):
                break;
            default:
                return;
        }

        $this->totalWithoutTaxes = $totalBeforeTax = $this->computeTotalBeforeTax();
        foreach ($this->taxes as $tax) {
            $tax->setTotal(round($tax->getRate() * $totalBeforeTax, 2));
        }

        $this->totalWithTaxes = round($totalBeforeTax + $this->computesTaxesTotal(), 2);
        $this->totalRefunded = $this->computeRefund();
        $this->totalIsDirty = false;
    }

    private function computeRefund()
    {
        $amount = 0.0;
        foreach ($this->payments as $payment) {
            if (!$payment->getIsRefund()) {
                continue;
            }

            if ($payment->getState() == Payment::STATE_SUCCESS) {
                $amount += round($payment->getAmount(), 2);
            }
        }

        return round($amount, 2);
    }

    /**
     * Get total products - discounts (no taxes)
     * @return int|string
     */
    private function computeTotalBeforeTax()
    {
        $total = 0.0;
        foreach ($this->items as $item) {
            $total += $item->getTotalPrice();
        }

        return round($total, 2);
    }

    /**
     * @return int
     */
    private function computesTaxesTotal()
    {
        $total = 0;
        foreach ($this->taxes as $tax) {
            $total += $tax->getTotal();
        }

        return round($total, 2);
    }

    public function getTaxesTotal()
    {
        return round($this->getTotalWithTaxes() - $this->getTotalWithoutTaxes(), 2);
    }

    /**
     * @return float
     */
    public function getTotalWithoutTaxes()
    {
        return $this->totalWithoutTaxes;
    }

    /**
     * @return float
     */
    public function getTotalWithTaxes()
    {
        return $this->totalWithTaxes;
    }

    /**
     * Return the final total of the order.
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->getTotalWithTaxes();
    }

    /**
     * Return the total this order have been refunded
     *
     * @return float
     */
    public function getTotalRefunded()
    {
        return $this->totalRefunded;
    }

    /**
     * @param string $regionCode
     */
    public function setRegionCode($regionCode)
    {
        $this->regionCode = $regionCode;
    }

    /**
     * @return string
     */
    public function getRegionCode()
    {
        return $this->regionCode;
    }

    /**
     * @return Tax[]
     */
    public function getTaxes()
    {
        return $this->taxes->toArray();
    }

    /**
     * Tax are added by the TaxService it is in charge of verified the taxes not the order.
     * @param Tax $tax
     */
    public function addTax(Tax $tax)
    {
        if (!$this->taxes->contains($tax)) {
            $this->taxes->add($tax);
            $tax->setOrder($this);
            $this->totalIsDirty = true;
        }
    }

    /**
     * @return Item[]
     */
    public function getItems()
    {
        return $this->items->toArray();
    }

    public function addItem(Item $item)
    {
        if(!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setOrder($this);
            $this->totalIsDirty = true;
        }
    }

    public function removeItem(Item $item)
    {
        if($this->items->contains($item)) {
            $this->items->removeElement($item);
            $this->totalIsDirty = true;
        }
    }

    /**
     * @return Payment[]
     */
    public function getPayments()
    {
        return $this->payments->toArray();
    }

    public function addPayment(Payment $payment)
    {
        if(!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setOrder($this);
            $this->totalIsDirty = true;
        }
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
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     * @ORM\PreFlush()
     */
    public function computeOnSave()
    {
        if(is_null($this->uniqueId)) {
            $this->uniqueId = uniqid();
        }
        $this->computeTotals();
    }

    /**
     * @return Address
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * @param Address $billingAddress
     */
    public function setBillingAddress($billingAddress)
    {
        $this->billingAddress = $billingAddress;
    }

    /**
     * @return Address
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * @param Address $shippingAddress
     */
    public function setShippingAddress($shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
    }

    public function isOwnedBy(OwnerInterface $possibleOwner)
    {
        return $possibleOwner->getOwnerId() == $this->clientId;
    }


    public function __toString()
    {
        return $this->getUniqueId();
    }
}
