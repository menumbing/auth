<?php

declare(strict_types=1);

namespace HyperfExtension\Auth\Annotations;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Gate extends AbstractAnnotation
{
    public function __construct(public readonly string $ability)
    {
    }
}
