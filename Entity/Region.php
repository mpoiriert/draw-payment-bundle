<?php

namespace Draw\PaymentBundle\Entity;

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
}