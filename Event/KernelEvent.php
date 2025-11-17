<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Event;

use Amp\Http\Server\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Base class for events dispatched in the HttpKernel component.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class KernelEvent extends Event
{
    public function __construct(
        private HttpKernelInterface $kernel,
        private Request $request,
    ) {
    }

    /**
     * Returns the kernel in which this event was thrown.
     */
    public function getKernel(): HttpKernelInterface
    {
        return $this->kernel;
    }

    /**
     * Returns the request the kernel is currently processing.
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}
