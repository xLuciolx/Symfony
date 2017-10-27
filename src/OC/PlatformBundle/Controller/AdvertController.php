<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;

// use OC\PlatformBundle\Entity\Advert;
// use OC\PlatformBundle\Entity\Image;
// use OC\PlatformBundle\Entity\AdvertSkill;
// use OC\PlatformBundle\Entity\Application;
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

        $nbPerPage = 3;

        $em = $this->getDoctrine()->getManager();
        $listAdverts = $em->getRepository('OCPlatformBundle:Advert')
                            ->getAdverts($page, $nbPerPage);

        // Number of page:
        $nbPages = ceil(count($listAdverts)/ $nbPerPage);

        if ($page > $nbPages) {
            throw new NotFoundHttpException('Page "' . $page . '" inexistante.');
        }


        return $this->render('OCPlatformBundle:Advert:index.html.twig', ['listAdverts' => $listAdverts,
         'nbPages'     => $nbPages,
         'page'        => $page
        ]);
    }

    public function viewAction($id, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('OCPlatformBundle:Advert')
                     ->find($id);

        //Check if the Entity exist
        if ($advert === null) {
            throw new NotFoundHttpException('L\'annonce d\'id ' .$id . ' n\'existe pas');
        }

        $listApplication = $em->getRepository('OCPlatformBundle:Application')
            ->findBy(array('advert' => $advert));

        $listAdvertSkills = $em->getRepository('OCPlatformBundle:AdvertSkill')
            ->findBy(array('advert' => $advert));

        return $this->render(
            'OCPlatformBundle:Advert:view.html.twig',
            array(
                'advert'       => $advert,
                'applications' => $listApplication,
                'skills'       => $listAdvertSkills,
            )
        );
    }


    public function addAction(Request $request)
    {
        // Get the EntityManager
        $em = $this->getDoctrine()->getManager();

        // If method POST
        if ($request->isMethod('POST')) {
            $request->getSession()
                    ->getFlashBag()
                    ->add('notice', 'Annonce enregistrée.');
            // redirect to the advert view
            return $this->redirectToRoute('oc_platform_view', ['id' => $advert->getId()]);
        }
        // Display form
        return $this->render('OCPlatformBundle:Advert:add.html.twig');

    }

    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Retrieve the advert with the id
        $advert = $em->getRepository('OCPlatformBundle:Advert')
                     ->find($id);

        if ($advert === null) {
            throw new NotFoundHttpException('L\'annonce d\'id ' . $id . ' n\'existe pas.');
        }

        if ($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('notice', 'L\'annonce a été modifiée');
            return $this->redirectToRoute('OCPlatformBundle:Advert:view.html.twig');
        }

        return $this->render('OCPlatformBundle:Advert:edit.html.twig', ['advert' => $advert]);
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // Retrieve advert from id
        $advert = $em->getRepository('OCPlatformBundle:Advert')
                     ->find($id);

         if ($advert === null) {
             throw new NotFoundHttpException('L\'annonce d\'id ' . $id . ' n\'existe pas.');
         }

        //  Remove category
        foreach ($advert->getCategories() as $category) {
            $advert->removeCategory($category);
        }

        $em->flush();

        return $this->render('OCPlatformBundle:Advert:delete.html.twig');
    }

    public function menuAction($limit)
    {
        $em = $this->getDoctrine()->getManager();
        $listAdverts = $em->getRepository('OCPlatformBundle:Advert')
                            ->findBy(
                                array(),
                                array('date' => 'desc'),
                                $limit
                            );

        return $this->render('OCPlatformBundle:Advert:menu.html.twig', ['listAdverts' => $listAdverts]);
    }
}
