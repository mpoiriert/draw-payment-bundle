<?php

namespace Draw\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @ORM\Table(name="draw_payment__transaction")
 */
class Transaction
{
    /**
     * Pending mean that the call is on the gateway side
     */
    const STATE_PENDING = 'PENDING';

    /**
     * Success mean that the call have been returned with success. Id does not mean that the response is success just
     * that no exception occur during the process.
     */
    const STATE_SUCCESS = 'SUCCESS';

    /**
     * Error mean that a exception occur during the process of the call
     */
    const STATE_ERROR = 'ERROR';

    /**
     * A unique id for the entity
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Payment associated to this transaction
     *
     * @var Payment
     *
     * @ORM\ManyToOne(targetEntity="Draw\PaymentBundle\Entity\Payment", inversedBy="transactions")
     * @ORM\JoinColumn(name="payment_id", onDelete="CASCADE", nullable=false)
     *
     * @Assert\NotNull()
     * @Assert\Type(Payment::class)
     */
    private $payment;

    /**
     * This is the type of call that have been made to the gateway
     *
     * @var string
     *
     * @ORM\Column(type="string", length=40)
     *
     * @Assert\Length(min="1", max="40")
     * @Assert\NotNull()
     */
    private $type;

    /**
     * The transaction state
     *
     * @var string
     *
     * @ORM\Column(type="string", length=20)
     *
     * @Assert\Choice({Transaction::STATE_PENDING ,Transaction::STATE_SUCCESS, Transaction::STATE_ERROR})
     * @Assert\NotNull()
     */
    private $state;

    /**
     * The error code returned by the payment provider if any
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $errorCode;

    /**
     * The error text returned from the payment provider if any
     *
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $errorText;

    /**
     * The data sent to the gateway
     *
     * @var array
     *
     * @ORM\Column(type="json_array", nullable=false)
     *
     * @Assert\NotNull()
     */
    private $requestData;

    /**
     * The data receive from the gateway
     *
     * @var array
     *
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $responseData;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array|null
     */
    public function getResponseData()
    {
        return $this->responseData;
    }

    /**
     * @param array $responseData
     */
    public function setResponseData(array $responseData)
    {
        $this->responseData = $responseData;
    }

    /**
     * @return Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param Payment $payment
     */
    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param string $errorCode
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
    }

    /**
     * @return string
     */
    public function getErrorText()
    {
        return $this->errorText;
    }

    /**
     * @param string $errorText
     */
    public function setErrorText($errorText)
    {
        $this->errorText = $errorText;
    }

    /**
     * @return array|null
     */
    public function getRequestData()
    {
        return $this->requestData;
    }

    /**
     * @param array $requestData
     */
    public function setRequestData(array $requestData)
    {
        $this->requestData = $requestData;
        $this->encryptSensibleData();
    }

    /**
     * @ORM\PostLoad()
     */
    public function encryptSensibleData()
    {
        if(!empty($this->requestData['ACCT'])) {
            $acct = substr($this->requestData['ACCT'], -4);
            $acct = str_pad($acct, strlen($this->requestData['ACCT']), 'X', STR_PAD_LEFT);
            $this->requestData['ACCT'] = $acct;
        }

        if(!empty($this->requestData['CVV2'])) {
            $this->requestData['CVV2'] = str_pad("", strlen($this->requestData['CVV2']), 'X');
        }
    }

    public function __toString()
    {
        return (string)$this->getId();
    }
}