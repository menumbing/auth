<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-extension/auth.
 *
 * @link     https://github.com/hyperf-extension/auth
 * @contact  admin@ilover.me
 * @license  https://github.com/hyperf-extension/auth/blob/master/LICENSE
 */
namespace HyperfExtension\Auth\Middlewares;

use HyperfExtension\Auth\Contracts\AuthManagerInterface;
use HyperfExtension\Auth\Exceptions\AuthenticationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AbstractAuthenticateMiddleware implements MiddlewareInterface
{
    /**
     * The authentication factory instance.
     *
     * @var \HyperfExtension\Auth\Contracts\AuthManagerInterface
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     */
    public function __construct(AuthManagerInterface $auth)
    {
        $this->auth = $auth;
    }

    /**
     * {@inheritdoc}
     * @throws \HyperfExtension\Auth\Exceptions\AuthenticationException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->authenticate($request, $this->guards($request));

        return $handler->handle($request);
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @throws \HyperfExtension\Auth\Exceptions\AuthenticationException
     */
    protected function authenticate(ServerRequestInterface $request, array $guards): void
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                $this->auth->shouldUse($guard);
                return;
            }
        }

        ! $this->passable() and $this->unauthenticated($request, $guards);
    }

    /**
     * Handle an unauthenticated user.
     *
     * @throws \HyperfExtension\Auth\Exceptions\AuthenticationException
     */
    protected function unauthenticated(ServerRequestInterface $request, array $guards): void
    {
        throw new AuthenticationException(
            'Unauthenticated.',
            $guards,
            $this->redirectTo($request)
        );
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(ServerRequestInterface $request): ?string
    {
        return null;
    }

    /**
     * Determines whether an unauthenticated user can pass the auth guard.
     * In general, this should be return FALSE. However in some special cases,
     * return TRUE would be useful.
     */
    protected function passable(): bool
    {
        return false;
    }

    /**
     * Get guard names.
     *
     * @param ServerRequestInterface $request
     *
     * @return string[]
     */
    abstract protected function guards(ServerRequestInterface $request): array;
}
