<?php

declare(strict_types=1);

namespace Symfony\Component\HttpKernel\Routing\Matcher;

use Symfony\Component\Routing\Matcher\RedirectableUrlMatcher as SymfonyRedirectableUrlMatcher;

final class RedirectableUrlMatcher extends SymfonyRedirectableUrlMatcher
{
    public function redirect(string $path, string $route, ?string $scheme = null): array
    {
        // The parent matcher uses these keys to build the redirect URL
        $params = ['_canonical_path' => $path];

        if ($scheme !== null) {
            $params['_canonical_scheme'] = $scheme;
        }

        return $params;
    }
}