<?php

namespace AlbumBundle\Form;


use AlbumBundle\Entity\Album;
use AlbumBundle\Entity\AlbumCover;
use AlbumBundle\Entity\AlbumFont;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            'label' => 'Rodzaj',

            // use the User.username property as the visible option string
            'choice_label' => 'name',
            'expanded' => true,
        ));

        $builder->add('cover', EntityType::class, array(
            // query choices from this entity
            'class' => 'AlbumBundle:AlbumCover',
            'label' => 'Okładka',

            // use the User.username property as the visible option string
            'choice_label' => 'name',
            'expanded' => true,

            'choice_attr' => function (AlbumCover $entity) {
                return array(
                    'data' => $entity,
                );
            },
        ));

        $builder->add('font', EntityType::class, array(
            // query choices from this entity
            'class' => 'AlbumBundle:AlbumFont',
            'label' => 'Napis',

            // use the User.username property as the visible option string
            'choice_label' => 'name',
            'expanded' => true,

            'choice_attr' => function (AlbumFont $entity) {
                return array(
                    'font' => $entity->getFont(),
                );
            },
        ));

        // Other data

        $builder->add('first_name', TextType::class, array(
            // query choices from this entity
            'label' => 'Imię',
            'label_attr' => array(
                'class' => 'col-sm-2'
            ),
        ));

        $builder->add('last_name', TextType::class, array(
            // query choices from this entity
            'label' => 'Nazwisko',
            'label_attr' => array(
                'class' => 'col-sm-2'
            ),
        ));

        $builder->add('save', SubmitType::class, array(
            'label' => 'Wyślij',

            'attr' => array(
                'class' => 'btn btn-primary btn-lg',
            ),
            )
        );

    }

    public function configureOptions (OptionsResolver  $resolver)
    {
        $resolver->setDefaults(array('data_class' => Album::class));
    }
}