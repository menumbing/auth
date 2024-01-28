<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-extension/auth.
 *
 * @link     https://github.com/hyperf-extension/auth
 * @contact  admin@ilover.me
 * @license  https://github.com/hyperf-extension/auth/blob/master/LICENSE
 */
namespace HyperfExtension\Auth\Annotations;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD)]
class Auth extends AbstractAnnotation
{
    public function __construct(public readonly array|string|null $guards = null, public readonly bool|null $passable = false)
    {
    }
}
