<?php

namespace OC\PlatformBundle\Validator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Antiflood extends Constraint
{
    public $message = "Votre message est considéré comme flood";

    public function validateBy()
    {
        return 'oc_platform_antiflood';
    }
}
