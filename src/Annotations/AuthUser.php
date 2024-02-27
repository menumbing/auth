<?php

declare(strict_types=1);

namespace HyperfExtension\Auth\Annotations;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[Attribute(Attribute::TARGET_METHOD)]
class AuthUser extends AbstractAnnotation
{
    public function __construct(public string $for, public string|array|null $guards = null)
    {
    }
}
