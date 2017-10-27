<?php

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CoreController extends Controller
{
    public function indexAction()
    {
        return $this->render('CoreBundle:Core:index.html.twig');
    }

    public function contactAction(Request $request)
    {
        $request->getSession()
                ->getFlashBag()
                ->add('notice', 'La page de contact n’est pas encore disponible');

        return $this->redirectToRoute('core_homepage');
    }

    public function testAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        $advert->setAuthor('Laura');
        $em->persist($advert);
        $em->flush();
        return $this->render('CoreBundle:Core:test.html.twig');
    }
}
