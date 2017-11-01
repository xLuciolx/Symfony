<?php

namespace OC\PlatformBundle\Beta;
// use OC\PlatformBundle\Beta\BetaHTMLAdder;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class BetaListener
{
    protected $betaHtml;

    protected $endDate;

    public function __construct(BetaHTMLAdder $betaHtml, $endDate)
    {
        $this->betaHtml = $betaHtml;
        $this->endDate  = new \DateTime($endDate);
    }

    public function processBeta(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $remainingDays = $this->endDate->diff(new \DateTime())->days;

        if ($remainingDays <= 0) {
            return;
        }

        $response = $this->betaHtml->addBeta($event->getResponse(), $remainingDays);

        $event->setResponse($response);
    }
}
