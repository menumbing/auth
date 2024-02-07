<?php

declare(strict_types=1);

namespace HyperfExtension\Auth\Resource;

use Hyperf\Resource\Json\JsonResource;
use HyperfExtension\Auth\Exceptions\AuthorizationException;
use ReflectionObject;
use Throwable;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AuthErrorResource extends JsonResource
{
    public function __construct(Throwable $throwable)
    {
        parent::__construct($throwable);
    }

    public function toArray(): array
    {
        return [
            'code'    => $this->getCode($this->resource),
            'message' => $this->resource->getMessage(),
            'type'    => $this->getType($this->resource),
        ];
    }

    protected function getType(Throwable $throwable): string
    {
        return (new ReflectionObject($throwable))->getShortName();
    }

    protected function getCode(Throwable $throwable): int
    {
        return !empty($throwable->getCode()) ? $throwable->getCode() : $this->getDefaultCode($throwable);
    }

    protected function getDefaultCode(Throwable $throwable): int
    {
        return $throwable instanceof AuthorizationException ? 403 : 401;
    }
}
