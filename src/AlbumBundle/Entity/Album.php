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
     * @Assert\NotBlank()
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
}
