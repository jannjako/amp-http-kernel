<?php

declare(strict_types=1);

namespace Symfony\Component\HttpKernel\Examples\SimpleKernel\Controller;

use Amp\Http\HttpStatus;
use Amp\Http\Server\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Examples\SimpleKernel\Data\TodoItem;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/todo', name: 'todo')]
final class HelloController
{
    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] TodoItem $todoItem,
    ): Response
    {

        return new Response(
            status: HttpStatus::OK,
            headers: ['Content-Type' => 'application/json'],
            body: sprintf('{ message: "Thanks for this item: %s" }', $todoItem->name),
        );
    }
}