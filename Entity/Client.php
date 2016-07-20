<?php

namespace Draw\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Draw\PaymentBundle\Application\UserInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @ORM\Table(name="draw_payment__client")
 */
class Client
{
    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column()
     */
    private $id;

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
    private $email;

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
    private $name;

    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="Draw\PaymentBundle\Application\UserInterface")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $applicationUser;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $applicationUserReferenceId;

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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     * @return UserInterface|null
     */
    public function getApplicationUser()
    {
        return $this->applicationUser;
    }

    /**
     * @param UserInterface $user
     */
    public function setApplicationUser(UserInterface $user = null)
    {
        $this->applicationUser = $user;
        $this->applicationUserReferenceId = $user->getApplicationReferenceId();
    }
}