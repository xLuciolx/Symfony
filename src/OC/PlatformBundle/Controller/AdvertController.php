<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Event\PlatformEvents;
use OC\PlatformBundle\Event\MessagePostEvent;
use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\AdvertEditType;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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

    /**
     *
     * @param Request $request
     */
    public function addAction(Request $request)
    {
        $user = $request->getUser();
        // Create an Advert object
        $advert = new Advert();
        $advert->setUser($this->getUser());

        // Create FormBuilder from the form factory  service
        $form = $this->createForm(AdvertType::class, $advert);

        // If method POST
        if ($request->isMethod('POST')) {
            // Now we link $request and $form, from now on $advert has the values from the form.
            $form->handleRequest($request);
            if ($form->isValid()) {

                // Create the events
                $event = new MessagePostEvent($advert->getContent(), $advert->getUser());

                // Trigger the event
                $this->get('event_dispatcher')->dispatch(PlatformEvents::POST_MESSAGE, $event);

                $advert->setContent($event->getMessage());

                // Get the EntityManager
                $em = $this->getDoctrine()->getManager();
                $em->persist($advert);
                $em->flush();

                $request->getSession()
                        ->getFlashBag()
                        ->add('notice', 'Annonce enregistrée');

                return $this->redirectToRoute('oc_platform_view', [
                    'id'   => $advert->getId(),
                    'slug' => $advert->getSlug()
                ]
            );
            }
        }
        // If form is not valid
        return $this->render('OCPlatformBundle:Advert:add.html.twig', ['form' => $form->createView()]);

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

        $form = $this->createForm(AdvertEditType::class, $advert);

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                // No need to persist, Doctrine already knows the entity
                $em->flush();
            }
            $request->getSession()->getFlashBag()->add('notice', 'L\'annonce a été modifiée');
            return $this->redirectToRoute('oc_platform_view', [
                'id' => $advert->getId(),
                'slug' => $advert->getSlug()
            ]);
        }

        return $this->render('OCPlatformBundle:Advert:edit.html.twig', [
            'advert' => $advert,
            'form'   => $form->createView()
        ]);
    }

    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Retrieve advert from id
        $advert = $em->getRepository('OCPlatformBundle:Advert')
                     ->find($id);

         if ($advert === null) {
             throw new NotFoundHttpException('L\'annonce d\'id ' . $id . ' n\'existe pas.');
         }

         $form = $this->get('form.factory')->create();

         if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
             $em->remove($advert);
             $em->flush();

             $request->getSession()->getFlashBag()->add('notice', 'L\'annonce a été supprimée');

             return $this->redirectToRoute('oc_platform_home');
         }

        return $this->render('OCPlatformBundle:Advert:delete.html.twig',
    [
        'advert' => $advert,
        'form'   => $form->createView()
    ]);
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
