<?php

namespace AlbumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AlbumStyle
 *
 * @ORM\Entity(repositoryClass="AlbumBundle\Repository\AlbumCoverRepository")
 * @ORM\Table("album_style")
 */
class AlbumStyle {
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=50, unique=true)
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     */
    private $name;

    /**
     * @var string $image
     *
     * @ORM\Column(name="image", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     */
    private $image;

    /**
     * @var string $image_big
     *
     * @ORM\Column(name="image_big", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     */
    private $image_big;

    /**
     * @ORM\ManyToOne(targetEntity="AlbumFamily")
     * @ORM\JoinColumn(name="family",nullable=false)
     * @Assert\Valid
     */
    public $family;


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
     * Set name
     *
     * @param string $name
     *
     * @return AlbumCover
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return AlbumCover
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set imageBig
     *
     * @param string $imageBig
     *
     * @return AlbumCover
     */
    public function setImageBig($imageBig)
    {
        $this->image_big = $imageBig;

        return $this;
    }

    /**
     * Get imageBig
     *
     * @return string
     */
    public function getImageBig()
    {
        return $this->image_big;
    }

    /**
     * Set family
     *
     * @param \AlbumBundle\Entity\AlbumFamily $family
     *
     * @return AlbumCover
     */
    public function setFamily(\AlbumBundle\Entity\AlbumFamily $family)
    {
        $this->family = $family;

        return $this;
    }

    /**
     * Get family
     *
     * @return \AlbumBundle\Entity\AlbumFamily
     */
    public function getFamily()
    {
        return $this->family;
    }
}
