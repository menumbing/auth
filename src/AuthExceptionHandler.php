<?php

declare(strict_types=1);

namespace HyperfExtension\Auth;

use Hyperf\Di\Annotation\Inject;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Contract\RequestInterface;
use HyperfExtension\Auth\Exceptions\AuthenticationException;
use HyperfExtension\Auth\Exceptions\AuthorizationException;
use HyperfExtension\Auth\Resource\AuthErrorResource;
use Menumbing\Resource\Contract\ResourceStrategyInterface;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AuthExceptionHandler extends ExceptionHandler
{
    #[Inject]
    protected RequestInterface $request;

    #[Inject]
    protected ResourceStrategyInterface $resource;

    public function handle(Throwable $throwable, ResponsePlusInterface $response)
    {
        $this->stopPropagation();

        if ($this->resource->supports($throwable)) {
            return $this->resource->render(new AuthErrorResource($throwable));
        }

        return $response
            ->setStatus($this->getCode($throwable))
            ->setBody(new SwooleStream($throwable->getMessage()));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof AuthenticationException || $throwable instanceof AuthorizationException;
    }

    protected function getCode(Throwable $throwable): int
    {
        return $throwable instanceof AuthorizationException ? 403 : 401;
    }
}
