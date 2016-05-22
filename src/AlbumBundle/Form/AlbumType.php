<?php

namespace AlbumBundle\Form;


use AlbumBundle\Entity\Album;
use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver ;
use Symfony\Component\Translation\TranslatorInterface;

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
        $builder->add(
            $builder->create(
                'album_family',
                'choice',
                array(
                    'disabled' => false,
                    'attr' => array('autocomplete' => 'off', 'placeholder' => 'Cms.voucher.shop.placeholder')
                )
            ));
    }

    public function configureOptions (OptionsResolver  $resolver)
    {
        $resolver->setDefaults(array('data_class' => Album::class));
    }
}