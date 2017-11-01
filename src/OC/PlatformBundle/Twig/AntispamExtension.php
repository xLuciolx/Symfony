<?php

namespace OC\PlatformBundle\Twig;

use OC\PlatformBundle\Antispam\OCAntispam;

class AntispamExtension extends \Twig_Extension
{
    /**
     * @var OCAntispam
     */
    private $ocAntispam;

    public function __construct(OCAntispam $oCAntispam)
    {
        $this->ocAntispam = $oCAntispam;
    }

    public function checkIfSpam($text)
    {
        return $this->ocAntispam->isSpam($text);
    }

    // Twig use this function to know which function our service add
    public function getFunctions()
    {
        return array(new \Twig_SimpleFunction('checkIfSpam', array($this, 'checkIfSpam')));
    }
    // Mandatory, identify the extension
    public function getName()
    {
        return 'OCAntispam';
    }
}
