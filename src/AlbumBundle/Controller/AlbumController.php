<?php

namespace AlbumBundle\Controller;

use AlbumBundle\Entity\Album;
use AlbumBundle\Form\AlbumType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AlbumController extends Controller {

    public function showAction()
    {

    }

    /**
     * @param Request $request
     * @return array
     */
    public function orderFormAction(Request $request)
    {
        $entity = new Album();

        $form = $this->createForm(
            AlbumType::class,
            $entity
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->get('doctrine')->getManager();

            $manager->persist($entity);

            $manager->flush();

            dump($entity);

            exit();

            return $this->redirectToRoute('task_success');
        }

        return $this->render(
            'AlbumBundle:Album:order-form.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function ajaxFamilyAction(Request $request, $id)
    {
        $manager = $this->get('doctrine')->getManager();

        $family = $manager->getRepository('AlbumBundle:AlbumFamily')->find($id);

        $covers = $manager->getRepository('AlbumBundle:AlbumCover')->findBy(array(
            'family' => $family,
        ));

        foreach ($covers as &$cover) {
            $cover = array(
                'id' => $cover->getId(),
                'image' => $cover->getImage(),
                'image_big' => $cover->getImageBig(),
            );
        }

        $response = array(
            'status' => 200,
            'response' => array(
                'id' => $family->getId(),
                'image' => $family->getImage(),
                'label' => $family->getLabel(),
                'description' => nl2br($family->getDescription()),
                'covers' => $covers,
            ),
        );

        return new JsonResponse($response);
    }
} 