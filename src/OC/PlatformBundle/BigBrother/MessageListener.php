<?php

namespace OC\PlatformBundle\BigBrother;

use OC\PlatformBundle\Event\MessagePostEvent;

class MessageListener
{
    protected $notificator;
    protected $listUsers = array();

    public function __construct(MessageNotificator $notificator, $listUsers)
    {
        $this->notificator = $notificator;
        $this->listUsers   = $listUsers;
    }

    public function processMessage(MessagePostEvent $event)
    {
        // On active la surveillance si l'auteur du message est dans la liste
        if (in_array($event->getUser()->getUsername(), $this->listUsers)) {
            // On envoie un e-mail à l'administrateur
            $this->notificator->notifyByEmail($event->getMessage(), $event->getUser());
        }
    }
}
