<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * SurrogateListener adds a Surrogate-Control HTTP header when the Response needs to be parsed for Surrogates.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class SurrogateListener implements EventSubscriberInterface
{
    /**
     * Filters the Response.
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        // TODO: Idk, perhaps there should be a real PSR6 cache implementation
        // TODO: or http cache but async so it doesn't block the loop
//        $kernel = $event->getKernel();
//        $surrogate = $this->surrogate;
//        if ($kernel instanceof HttpCache) {
//            $surrogate = $kernel->getSurrogate();
//            if (null !== $this->surrogate && $this->surrogate->getName() !== $surrogate->getName()) {
//                $surrogate = $this->surrogate;
//            }
//        }

//        if (null === $surrogate) {
//            return;
//        }
//
//        $surrogate->addSurrogateControl($event->getResponse());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
