<?php

namespace Draw\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

/**
 * @ORM\Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @ORM\Table("draw_payment__tax_configuration_in_time")
 */
class TaxConfigurationInTime
{
    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var TaxConfiguration
     *
     * @ORM\ManyToOne(targetEntity="Draw\PaymentBundle\Entity\TaxConfiguration")
     */
    private $taxConfiguration;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $activeFrom;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $activeTo;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=false)
     *
     * @Assert\Type("float")
     */
    private $rate;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return TaxConfiguration
     */
    public function getTaxConfiguration()
    {
        return $this->taxConfiguration;
    }

    /**
     * @param TaxConfiguration $taxConfiguration
     */
    public function setTaxConfiguration(TaxConfiguration $taxConfiguration)
    {
        $this->taxConfiguration = $taxConfiguration;
        $taxConfiguration->addTaxConfigurationInTime($this);
    }

    /**
     * @return DateTime
     */
    public function getActiveFrom()
    {
        return $this->activeFrom;
    }

    /**
     * @param DateTime $activeFrom
     */
    public function setActiveFrom($activeFrom)
    {
        $this->activeFrom = $activeFrom;
    }

    /**
     * @return DateTime
     */
    public function getActiveTo()
    {
        return $this->activeTo;
    }

    /**
     * @param DateTime $activeTo
     */
    public function setActiveTo($activeTo)
    {
        $this->activeTo = $activeTo;
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

    public function compareTo(TaxConfigurationInTime $taxConfigurationInTime)
    {
        if(!$this->isValid() && !$taxConfigurationInTime->isValid()) {
            return 0;
        }

        if($this->isValid() && !$taxConfigurationInTime->isValid()) {
            return -1;
        }

        if(!$this->isValid() && $taxConfigurationInTime->isValid()) {
            return 1;
        }

        if(!$this->getActiveFrom() && $taxConfigurationInTime->getActiveFrom()) {
            return 1;
        }

        if($this->getActiveFrom() && !$taxConfigurationInTime->getActiveFrom()) {
            return -1;
        }

        if($this->getActiveFrom() && $taxConfigurationInTime->getActiveFrom()) {
            if($compare = strcmp($taxConfigurationInTime->getActiveFrom()->format('U'), $this->getActiveFrom()->format('U'))) {
                return $compare;
            }
        }

        if(!$this->getActiveTo() && $taxConfigurationInTime->getActiveTo()) {
            return 1;
        }

        if($this->getActiveTo() && !$taxConfigurationInTime->getActiveTo()) {
            return -1;
        }

        if($this->getActiveTo() && $taxConfigurationInTime->getActiveTo()) {
            return strcmp($this->getActiveFrom()->format('U'), $taxConfigurationInTime->getActiveFrom()->format('U'));
        }

        return 0;
    }

    private function isValid()
    {
        if($this->getActiveFrom() && $this->getActiveFrom() > new DateTime()) {
            return false;
        }

        if($this->getActiveTo() && $this->getActiveTo() < new DateTime()) {
            return false;
        }

        return true;
    }
}