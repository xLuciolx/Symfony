<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
        if ($page < 1) {
            throw new NotFoundHttpException('Page "' . $page . '" inexistante.');

        }

        $listAdverts = array(
      array(
        'title'   => 'Recherche développpeur Symfony',
        'id'      => 1,
        'author'  => 'Loïc',
        'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
        'date'    => new \Datetime()),
      array(
        'title'   => 'Mission de webmaster',
        'id'      => 2,
        'author'  => 'Hugo',
        'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
        'date'    => new \Datetime()),
      array(
        'title'   => 'Offre de stage webdesigner',
        'id'      => 3,
        'author'  => 'Mathieu',
        'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
        'date'    => new \Datetime())
    );

        return $this->render('OCPlatformBundle:Advert:index.html.twig', ['listAdverts' => $listAdverts]);
    }

    public function viewAction($id)
    {
        //Get the repository
        $repository = $this->getDoctrine()
                            ->getManager()
                            ->getRepository('OCPlatformBundle:Advert');

        //Get the Entity matching the id
        $advert = $repository->find($id);

        //Check if the Entity exist
        if ($advert === null) {
            throw new NotFoundHttpException('L\'annonce d\'id ' .$id . ' n\'existe pas');
        }
        return $this->render(
            'OCPlatformBundle:Advert:view.html.twig',
            array(
                'advert'  => $advert
            )
        );
    }


    public function addAction(Request $request)
    {
        // Create Entity
        $advert = new Advert();
        $advert->setTitle('Recherche développeur Symfony.');
        $advert->setAuthor('Loïc');
        $advert->setContent('Nous cherchons un développeur Symfony débutant sur Lyon. Etc...');

        // Get the EntityManager
        $em = $this->getDoctrine()->getManager();

        # Step 1: persist the Entity
        $em->persist($advert);

        ## Step 2: flush
        $em->flush();

        // If method POST
        if ($request->isMethod('POST')) {
            $request->getSession()
                    ->getFlashBag()
                    ->add('notice', 'Annonce enregistrée.');
            // redirect to the advert view
            return $this->redirectToRoute('oc_platform_view', ['id' => $advert->getId()]);
        }
        // Display form
        return $this->render('OCPlatformBundle:Advert:add.html.twig', array('advert' => $advert));

    }

    public function editAction($id, Request $request)
    {
        $advert = array(
            'title'   => 'Recherche développeur Symfony',
            'id'      => $id,
            'author'  => 'Loïc',
            'content' => 'Nous cherchons un développeur Symfony débutant sur Lyon. Etc...',
            'date'    => new \Datetime()
        );

        return $this->render('OCPlatformBundle:Advert:edit.html.twig', ['advert' => $advert]);
    }

    public function deleteAction($id, Request $request)
    {
        $request->getSession()
                ->getFlashBag()
                ->add('notice', 'L\'annonce n° ' . $id . ' a été supprimée');
        return $this->redirectToRoute('core_homepage');
        // return $this->render('OCPlatformBundle:Advert:delete.html.twig');
    }

    public function menuAction($limit)
    {
        $listAdverts = array(
            array(
                'id'    => 2,
                'title' => 'Recherche développeur Symfony'
            ),
            array(
                'id'    => 5,
                'title' => 'Mission de webmaster'
            ),
            array(
                'id'    => 9,
                'title' => 'Offre de stage webdesigner'
            )
        );

        return $this->render('OCPlatformBundle:Advert:menu.html.twig', ['listAdverts' => $listAdverts]);
    }
}
