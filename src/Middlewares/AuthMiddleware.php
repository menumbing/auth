<?php

declare(strict_types=1);

namespace HyperfExtension\Auth\Middlewares;

use Hyperf\HttpServer\Router\Dispatched;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AuthMiddleware extends AbstractAuthenticateMiddleware
{
    protected function guards(ServerRequestInterface $request): array
    {
        $dispatched = $request->getAttribute(Dispatched::class);

        return $dispatched->handler?->options['guard'] ?? [];
    }
}
