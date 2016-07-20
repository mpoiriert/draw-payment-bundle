<?php

namespace Draw\PaymentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @ORM\Table("draw_payment__tax_configuration")
 */
class TaxConfiguration
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
     * A unique configuration name
     *
     * @var string
     *
     * @ORM\Column(type="string", length=40, unique=true)
     */
    private $configurationName;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=true)
     *
     * @Assert\Length(min=1, max=50)
     * @Assert\Type("string")
     */
    private $taxName;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=true)
     *
     * @Assert\Length(min=1, max=50)
     * @Assert\Type("string")
     */
    private $taxNumber;

    /**
     * @var TaxConfigurationInTime[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Draw\PaymentBundle\Entity\TaxConfigurationInTime",
     *     mappedBy="taxConfiguration",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     */
    private $taxConfigurationInTimes;

    public function __construct()
    {
        $this->taxConfigurationInTimes = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getConfigurationName()
    {
        return $this->configurationName;
    }

    /**
     * @param string $configurationName
     */
    public function setConfigurationName($configurationName)
    {
        $this->configurationName = $configurationName;
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
     * @return TaxConfigurationInTime[]
     */
    public function getTaxConfigurationInTimes()
    {
        $taxConfigurationInTimes = $this->taxConfigurationInTimes->toArray();
        usort($taxConfigurationInTimes, function(TaxConfigurationInTime $a, TaxConfigurationInTime $b) {
           return $a->compareTo($b);
        });

        return $taxConfigurationInTimes;
    }

    public function addTaxConfigurationInTime(TaxConfigurationInTime $taxConfigurationInTime)
    {
        if(!$this->taxConfigurationInTimes->contains($taxConfigurationInTime)) {
            $this->taxConfigurationInTimes->add($taxConfigurationInTime);
            $taxConfigurationInTime->setTaxConfiguration($this);
        }
    }

    public function removeTaxConfigurationInTime(TaxConfigurationInTime $taxConfigurationInTime)
    {
        if($this->taxConfigurationInTimes->contains($taxConfigurationInTime)) {
            $this->taxConfigurationInTimes->removeElement($taxConfigurationInTime);
        }
    }

    public function __toString()
    {
        return $this->getConfigurationName();
    }
}