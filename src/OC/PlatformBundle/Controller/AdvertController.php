<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Entity\Application;
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
        'date'    => new \DateTime()),
      array(
        'title'   => 'Mission de webmaster',
        'id'      => 2,
        'author'  => 'Hugo',
        'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
        'date'    => new \DateTime()),
      array(
        'title'   => 'Offre de stage webdesigner',
        'id'      => 3,
        'author'  => 'Mathieu',
        'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
        'date'    => new \DateTime())
    );

        return $this->render('OCPlatformBundle:Advert:index.html.twig', ['listAdverts' => $listAdverts]);
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

        // Create Advert Entity
        $advert = new Advert();
        $advert->setTitle('Recherche développeur Symfony.');
        $advert->setAuthor('Loïc');
        $advert->setContent('Nous cherchons un développeur Symfony débutant sur Lyon. Etc...');
        $advert->setEmail('lgallay@orange.fr');

        // Create Image Entity
        $image = new Image();
        $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
        $image->setAlt('Job de rêve');

        $advert->setImage($image);

        // Create Application Entities
        $application1 = new Application();
        $application1->setAuthor('Laura');
        $application1->setContent('J\'ai toutes les qualités requises.');

        $application2 = new Application();
        $application2->setAuthor('Pierre');
        $application2->setContent('Je suis très motivé.');

        // Link applications to advert
        $application1->setAdvert($advert);
        $application2->setAdvert($advert);

        // Retrieve all skill in database
        $listSkills = $em->getRepository('OCPlatformBundle:Skill')
                         ->findAll();

        // For each skill
        foreach ($listSkills as $skill) {
            // Create relation between an advert and a skill
            $advertSkill = new AdvertSkill();
            // Link to an advert
            $advertSkill->setAdvert($advert);
            // link to a skill
            $advertSkill->setSkill($skill);

            $advertSkill->setLevel('Expert');

            $em->persist($advertSkill);
        }




        # Step 1: persist the Entities
        $em->persist($advert);
        $em->persist($application1);
        $em->persist($application2);

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
        $em = $this->getDoctrine()->getManager();

        // Retrieve the advert with the id
        $advert = $em->getRepository('OCPlatformBundle:Advert')
                     ->find($id);

        if ($advert === null) {
            throw new NotFoundHttpException('L\'annonce d\'id ' . $id . ' n\'existe pas.');
        }

        // Retrieve all the categories in the database
        $listCategories = $em->getRepository('OCPlatformBundle:Category')
                           ->findAll();

        // Link categories to advert
        foreach ($listCategories as $category) {
            $advert->addCategory($category);
        }

        $em->flush();

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
                            ->findAll();
        // $listAdverts = array(
        //     array(
        //         'id'    => 2,
        //         'title' => 'Recherche développeur Symfony'
        //     ),
        //     array(
        //         'id'    => 5,
        //         'title' => 'Mission de webmaster'
        //     ),
        //     array(
        //         'id'    => 9,
        //         'title' => 'Offre de stage webdesigner'
        //     )
        // );

        return $this->render('OCPlatformBundle:Advert:menu.html.twig', ['listAdverts' => $listAdverts]);
    }
}
