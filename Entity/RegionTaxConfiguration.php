<?php

namespace Draw\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @ORM\Table("draw_payment__regions_tax_configuration")
 */
class RegionTaxConfiguration
{
    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    private $taxConfiguration;

    /**
     * The region this tax configuration if for
     *
     * @var Region
     *
     * @ORM\ManyToOne(targetEntity="Draw\PaymentBundle\Entity\Region")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $region;
}