<?php

declare(strict_types=1);

namespace Symfony\Component\HttpKernel\Examples\SimpleKernel\Data;

use Symfony\Component\Validator\Constraints as Assert;

readonly final class TodoItem
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 5, max: 50)]
        public string $name,

        #[Assert\NotBlank]
        public bool $done,
    ) {
    }
}