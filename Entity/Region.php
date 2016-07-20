<?php

namespace Draw\PaymentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @ORM\Table("draw_payment__region")
 */
class Region
{
    /**
     * Unique generated id of the region
     *
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * A unique name to identify this region
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var TaxConfiguration[]
     *
     * @ORM\ManyToMany(targetEntity="Draw\PaymentBundle\Entity\TaxConfiguration")
     * @ORM\JoinTable(name="draw_payment__region_tax_configuration")
     */
    private $taxConfigurations;

    public function __construct()
    {
        $this->taxConfigurations = new ArrayCollection();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return TaxConfiguration[]
     */
    public function getTaxConfigurations()
    {
        return $this->taxConfigurations->toArray();
    }

    public function addTaxConfiguration(TaxConfiguration $taxConfiguration)
    {
        if(!$this->taxConfigurations->contains($taxConfiguration)) {
            $this->taxConfigurations->add($taxConfiguration);
        }
    }

    public function removeTaxConfiguration(TaxConfiguration $taxConfiguration)
    {
        if($this->taxConfigurations->contains($taxConfiguration)) {
            $this->taxConfigurations->removeElement($taxConfiguration);
        }
    }

    public function __toString()
    {
        return $this->getName();
    }
}