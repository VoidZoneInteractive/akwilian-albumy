<?php

namespace AlbumBundle\Entity;

use AlbumBundle\Entity\AlbumFamily;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Album
 *
 * @ORM\Entity(repositoryClass="AlbumBundle\Repository\AlbumRepository")
 * @ORM\Table("album")
 */
class Album {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\ManyToOne(targetEntity="AlbumFamily")
     * @ORM\JoinColumn(name="family",nullable=false)
     * @Assert\Valid
     */
    public $family;

    /**
     * @ORM\ManyToOne(targetEntity="AlbumCover")
     * @ORM\JoinColumn(name="cover",nullable=false)
     * @Assert\Valid
     */
    public $cover;

    /**
     * @ORM\ManyToOne(targetEntity="AlbumFont")
     * @ORM\JoinColumn(name="font",nullable=false)
     * @Assert\Valid
     */
    public $font;

    /**
     * @var string $name
     *
     * @ORM\Column(name="first_name", type="string", length=50, unique=true)
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     */
    public $first_name;

    /**
     * @var string $name
     *
     * @ORM\Column(name="last_name", type="string", length=50, unique=true)
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     */
    public $last_name;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set family
     *
     * @param AlbumFamily $family
     *
     * @return Album
     */
    public function setFamily(AlbumFamily $family)
    {
        $this->family = $family;

        return $this;
    }

    /**
     * Get family
     *
     * @return AlbumFamily
     */
    public function getFamily()
    {
        return $this->family;
    }

    /**
     * Set cover
     *
     * @param \AlbumBundle\Entity\AlbumCover $cover
     *
     * @return Album
     */
    public function setCover(\AlbumBundle\Entity\AlbumCover $cover)
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * Get cover
     *
     * @return \AlbumBundle\Entity\AlbumCover
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * Set font
     *
     * @param \AlbumBundle\Entity\AlbumFont $font
     *
     * @return Album
     */
    public function setFont(\AlbumBundle\Entity\AlbumFont $font)
    {
        $this->font = $font;

        return $this;
    }

    /**
     * Get font
     *
     * @return \AlbumBundle\Entity\AlbumFont
     */
    public function getFont()
    {
        return $this->cover;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Album
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Album
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }
}
