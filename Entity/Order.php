<?php

namespace Draw\PaymentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
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
class Order
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
     * @Assert\Choice({"NEW"}, groups={"apply_coupon"}, payload={"ugm":{"code":"ORDER_INVALID_STATE"}}))
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
     * @Assert\EqualTo(0, groups={"pay_free"}, payload={"ugm":{"code":"PAY_FREE_TOTAL_NOT_ZERO"}})
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
     */
    private $billingAddress;

    /**
     * The address of shipping
     *
     * @var Address
     *
     * @ORM\Embedded(class = Address::class)
     */
    private $shippingAddress;

    public function __construct()
    {
        $this->orderTaxes = new ArrayCollection();
        $this->orderProducts = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->orderDiscounts = new ArrayCollection();
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
        foreach ($this->getOrderTaxes() as $tax) {
            $amountTax = round($tax->getRate() * $totalBeforeTax, 2);
            $tax->setTotal($amountTax);
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
        foreach ($this->getOrderProducts() as $orderProduct) {
            $total += $orderProduct->getTotalPrice();
        }

        foreach ($this->getOrderDiscounts() as $orderDiscount) {
            $total -= $orderDiscount->getTotalPrice();
        }

        return round($total, 2);
    }

    /**
     * @return int
     */
    private function computesTaxesTotal()
    {
        $total = 0;
        foreach ($this->getOrderTaxes() as $orderTax) {
            $total += $orderTax->getTotal();
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
     * @param string $culture
     */
    public function setCulture($culture)
    {
        $this->culture = $culture;
    }

    public function getCulture()
    {
        return $this->culture;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode($countryCode)
    {
        //@todo clean this up
        if($countryCode instanceof Country) {
            $countryCode = $countryCode->getCode();
        }
        $this->countryCode = $countryCode;
    }

    /**
     * @param string $regionCode
     */
    public function setRegionCode($regionCode)
    {
        //@todo clean this up
        if($regionCode instanceof Region) {
            $regionCode = $regionCode->getCode();
        }
        $this->regionCode = $regionCode;
    }

    /**
     * @return OrderTax[]
     */
    public function getOrderTaxes()
    {
        return $this->orderTaxes;
    }

    /**
     * @return Payment[]
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Tax are added by the TaxService it is in charge of verified the taxes not the order.
     * @param OrderTax $orderTax
     */
    public function addOrderTax(OrderTax $orderTax)
    {
        if (!$this->orderTaxes->contains($orderTax)) {
            $this->orderTaxes[] = $orderTax;
        }
        $this->totalIsDirty = true;
    }

    /**
     * @return ArrayCollection|OrderProduct[]
     */
    public function getOrderProducts()
    {
        return $this->orderProducts->toArray();
    }

    public function addOrderProduct(OrderProduct $orderProduct)
    {
        $orderProduct->setOrder($this);
        $this->orderProducts->add($orderProduct);
        $this->totalIsDirty = true;
    }

    public function removeOrderProduct(OrderProduct $orderProduct)
    {
        $this->orderProducts->removeElement($orderProduct);
        $this->totalIsDirty = true;
    }

    /**
     * @return OrderDiscount[]
     */
    public function getOrderDiscounts()
    {
        return $this->orderDiscounts->toArray();
    }

    public function addOrderDiscount(OrderDiscount $orderDiscount)
    {
        $orderDiscount->setOrder($this);
        $this->orderDiscounts->add($orderDiscount);
        $this->totalIsDirty = true;
    }

    public function removeOrderDiscount(OrderDiscount $orderDiscount)
    {
        $this->orderDiscounts->removeElement($orderDiscount);
        $this->totalIsDirty = true;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @return string
     */
    public function getRegionCode()
    {
        return $this->regionCode;
    }

    public function addPayment(Payment $payment)
    {
        if(!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setOrder($this);
        }

        $this->totalIsDirty = true;
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
        $this->computeTotals();
    }

    /**
     * @Assert\Callback(groups={"create_order", "update_order"}, payload={"ugm":{"code":"DUPLICATE_PRODUCT_CODE"}})
     */
    public function validateUniqueOrderProduct(ExecutionContextInterface $executionContext)
    {
        $currentCodes = [];
        foreach($this->orderProducts as $index => $orderProduct) {
            if(in_array($orderProduct->getProductCode(), $currentCodes)) {
                $executionContext->buildViolation("order.duplicate-product-code")
                    ->atPath('orderProducts[' . $index . '].productCode')
                    ->addViolation();
                continue;
            }
            $currentCodes[] = $orderProduct->getProductCode();
        }
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

    public function __toString()
    {
        return $this->getUniqueId();
    }
}
