<?php

namespace AlbumBundle\Form;


use AlbumBundle\Entity\Album;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlbumType extends AbstractType {

    protected $view_timezone;
    protected $timezone;
    protected $language;
    protected $user;
    protected $translator;

    function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('family', EntityType::class, array(
            // query choices from this entity
            'class' => 'AlbumBundle:AlbumFamily',

            // use the User.username property as the visible option string
            'choice_label' => 'name',
        ));
    }

    public function configureOptions (OptionsResolver  $resolver)
    {
        $resolver->setDefaults(array('data_class' => Album::class));
    }
}