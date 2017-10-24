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
    public function __construct(\Swift_Mailer $mailer, $locale, $minLength)
    {
        $this->mailer    = $mailer;
        $this->locale    = $locale;
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
}
