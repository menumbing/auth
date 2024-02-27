<?php

declare(strict_types=1);

namespace HyperfExtension\Auth\Aspect;

use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use HyperfExtension\Auth\Annotations\AuthUser;
use HyperfExtension\Auth\Contracts\AuthenticatableInterface;
use HyperfExtension\Auth\Contracts\AuthManagerInterface;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AuthUserAspect extends AbstractAspect
{
    public array $annotations = [
        AuthUser::class,
    ];

    public function __construct(protected AuthManagerInterface $authManager)
    {
    }

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $arguments = $proceedingJoinPoint->arguments['keys'];
        $annotations = $proceedingJoinPoint->getAnnotationMetadata();

        /** @var AuthUser $authUserAnnotation */
        $authUserAnnotation = $annotations->method[AuthUser::class];

        if (!array_key_exists($authUserAnnotation->for, $arguments)) {
            throw new RuntimeException(sprintf('Argument "%s" was not found.', $authUserAnnotation->for));
        }

        $arguments[$authUserAnnotation->for] = $this->getUser($authUserAnnotation->guards ?? [null]);

        $proceedingJoinPoint->arguments['keys'] = $arguments;

        return $proceedingJoinPoint->process();
    }

    protected function getUser(array $guards): ?AuthenticatableInterface
    {
        foreach ($guards as $guard) {
            if (null !== $user = $this->authManager->guard($guard)->user()) {
                return $user;
            }
        }

        return null;
    }
}
