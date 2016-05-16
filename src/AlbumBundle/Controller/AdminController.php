<?php

namespace AlbumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller {

    public function familyAction()
    {
        $entities = $this->get('doctrine')->getEntityManager()->getRepository('AlbumBundle:AlbumFamily')->findAll();

        return $this->render(
            'AlbumBundle:Admin:AlbumFamily/index.html.twig',
            array(
                'entities' => $entities,
            )
        );
    }
} 