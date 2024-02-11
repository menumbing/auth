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
use Menumbing\Resource\Trait\MergeResponse;
use Psr\Http\Message\ResponseInterface;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AuthExceptionHandler extends ExceptionHandler
{
    use MergeResponse;

    #[Inject]
    protected RequestInterface $request;

    #[Inject]
    protected ResourceStrategyInterface $resource;

    public function handle(Throwable $throwable, ResponsePlusInterface $response)
    {
        if ($this->resource->supports($throwable)) {
            $this->stopPropagation();

            $resource = $this->resource->render(new AuthErrorResource($throwable));

            if ($resource instanceof ResponseInterface) {
                $resource = $this->mergeAll($response, $resource);
            }

            return $resource;
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
