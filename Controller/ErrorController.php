<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Controller;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Renders error or exception pages from a given FlattenException.
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 * @author Matthias Pigulla <mp@webfactory.de>
 */
class ErrorController
{
    public function __construct(
        private ErrorRendererInterface $errorRenderer,
    ) {
    }

    public function __invoke(\Throwable $exception): Response
    {
        $exception = $this->errorRenderer->render($exception);

        return new Response(
            status: $exception->getStatusCode(),
            headers: $exception->getHeaders(),
            body: $exception->getAsString()
        );
    }
}
