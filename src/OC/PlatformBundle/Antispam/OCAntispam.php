<?php

namespace OC\PlatformBundle\Antispam;
use Swift_Mailer;

class OCAntispam
{
    private $mailer;
    private $locale;
    private $minLength;

    /**
     * @param Swift_Mailer $mailer
     * @param mixed $locale
     * @param int $minLength
     */
    public function __construct(\Swift_Mailer $mailer, $minLength)
    {
        $this->mailer    = $mailer;
        $this->minLength = (int) $minLength;
    }

    /**
     * Check if a text is a spam
     * @param  string  $text
     * @return bool
     */
    public function isSpam($text)
    {
        return strlen($text) < $this->minLength;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
}
