<?php

namespace Draw\PaymentBundle\Entity;

use CommerceGuys\Addressing\Model\AddressInterface;
use CommerceGuys\Addressing\Validator\Constraints\AddressFormat;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable()
 *
 * @AddressFormat()
 */
class Address implements AddressInterface
{
    /**
     * The two-letter country code.
     *
     * This is a CLDR country code, since CLDR includes additional countries
     * for addressing purposes, such as Canary Islands (IC).
     *
     * @var string
     *
     * @ORM\Column(type="string", length=2, nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Type("string")
     * @Serializer\Groups({"draw-order:create", "draw-order:update", "draw-order:read"})
     */
    private $countryCode;

    /**
     * The administrative area.
     *
     * Called the "state" in the United States, "province" in France and Italy,
     * "county" in Great Britain, "prefecture" in Japan, etc.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Type("string")
     * @Serializer\Groups({"draw-order:create", "draw-order:update", "draw-order:read"})
     */
    private $administrativeArea;

    /**
     * The locality (i.e city).
     *
     * Some countries do not use this field; their address lines are sufficient
     * to locate an address within a sub-administrative area.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Type("string")
     * @Serializer\Groups({"draw-order:create", "draw-order:update", "draw-order:read"})
     */
    private $locality;

    /**
     * The dependent locality (i.e neighbourhood).
     *
     * When representing a double-dependent locality in Great Britain, includes
     * both the double-dependent locality and the dependent locality,
     * e.g. "Whaley, Langwith".
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dependentLocality;

    /**
     * The postal code.
     *
     * The value is often alphanumeric.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Type("string")
     * @Serializer\Groups({"draw-order:create", "draw-order:update", "draw-order:read"})
     */
    private $postalCode;

    /**
     * The sorting code.
     *
     * For example, CEDEX in France.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sortingCode;

    /**
     * The first line of address block.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Type("string")
     * @Serializer\Groups({"draw-order:create", "draw-order:update", "draw-order:read"})
     */
    private $addressLine1;

    /**
     * The second line of address block.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Type("string")
     * @Serializer\Groups({"draw-order:create", "draw-order:update", "draw-order:read"})
     */
    private $addressLine2;

    /**
     * The recipient.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Type("string")
     * @Serializer\Groups({"draw-order:create", "draw-order:update", "draw-order:read"})
     */
    private $recipient;

    /**
     * The organization.
     *
     * @return string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Type("string")
     * @Serializer\Groups({"draw-order:create", "draw-order:update", "draw-order:read"})
     */
    private $organization;

    /**
     * The locale.
     *
     * Allows the initially-selected address format / subdivision translations
     * to be selected and used the next time this address is modified.
     *
     * @return string
     *
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $locale;

    /**
     * @return mixed
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param mixed $countryCode
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return mixed
     */
    public function getAdministrativeArea()
    {
        return $this->administrativeArea;
    }

    /**
     * @param mixed $administrativeArea
     */
    public function setAdministrativeArea($administrativeArea)
    {
        $this->administrativeArea = $administrativeArea;
    }

    /**
     * @return mixed
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * @param mixed $locality
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;
    }

    /**
     * @return mixed
     */
    public function getDependentLocality()
    {
        return $this->dependentLocality;
    }

    /**
     * @param mixed $dependentLocality
     */
    public function setDependentLocality($dependentLocality)
    {
        $this->dependentLocality = $dependentLocality;
    }

    /**
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param mixed $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return mixed
     */
    public function getSortingCode()
    {
        return $this->sortingCode;
    }

    /**
     * @param mixed $sortingCode
     */
    public function setSortingCode($sortingCode)
    {
        $this->sortingCode = $sortingCode;
    }

    /**
     * @return mixed
     */
    public function getAddressLine1()
    {
        return $this->addressLine1;
    }

    /**
     * @param mixed $addressLine1
     */
    public function setAddressLine1($addressLine1)
    {
        $this->addressLine1 = $addressLine1;
    }

    /**
     * @return mixed
     */
    public function getAddressLine2()
    {
        return $this->addressLine2;
    }

    /**
     * @param mixed $addressLine2
     */
    public function setAddressLine2($addressLine2)
    {
        $this->addressLine2 = $addressLine2;
    }

    /**
     * @return mixed
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param mixed $recipient
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * @return mixed
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param mixed $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
}