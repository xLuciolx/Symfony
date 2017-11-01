<?php

namespace OC\PlatformBundle\Event;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

class MessagePostEvent extends Event
{
    protected $message;
    protected $user;

    public function __construct($message, UserInterface $user)
    {
        $this->message = $message;
        $this->user    = $user;
    }

    // The listener must access the message
    public function getMessage()
    {
        return $this->message;
    }

    // The listener can mofify the message
    public function setMessage($message)
    {
        return $this->message = $message;
    }

    // The listener must access the user
    public function getUser()
    {
        return $this->user;
    }
}
