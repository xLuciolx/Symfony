<?php

namespace CoreBundle\Controller;

use Swift_Message;
use CoreBundle\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Entity\Contact;

class CoreController extends Controller
{
    public function indexAction()
    {
        return $this->render('CoreBundle:Core:index.html.twig');
    }

    public function contactAction(Request $request)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($contact);

                $message = new Swift_Message();
                $message->setSubject($contact->getSubject())
                        ->setFrom($contact->getEmail())
                        // ->setTo('lgallay@orange.fr')
                        ->setBody($this->renderView('CoreBundle:Core:mail.html.twig', [
                            'name'    => $contact->getName(),
                            'message' => $contact->getContent()
                        ]), 'text/html');
                if ($this->get('mailer')->send($message)) {
                    $em->flush();
                    $request->getSession()
                            ->getFlashBag()
                            ->add('notice', 'Votre message a été envoyé');

                    return $this->redirectToRoute('core_homepage');
                }
            }
        }

        return $this->render('CoreBundle:Core:contact.html.twig', [
            'form' => $form->createView()
        ]);
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
