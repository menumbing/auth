<?php

declare(strict_types=1);

namespace HyperfExtension\Auth\UserProviders;

use BadMethodCallException;
use Hyperf\Collection\Collection;
use HyperfExtension\Auth\Contracts\AuthenticatableInterface;
use HyperfExtension\Auth\Contracts\UserProviderInterface;
use HyperfExtension\Auth\GenericUser;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class GenericUserProvider implements UserProviderInterface
{
    protected Collection $users;

    public function __construct(array $options = [])
    {
        $this->users = new Collection(
            array_map(
                fn(array $user) => new GenericUser($user),
                $options['users'] ?? []
            )
        );
    }

    public function retrieveById($identifier): ?AuthenticatableInterface
    {
        return $this->users->first(fn(GenericUser $user) => $user->getAuthIdentifier() === $identifier);
    }

    public function retrieveByToken($identifier, string $token): ?AuthenticatableInterface
    {
        throw new BadMethodCallException();
    }

    public function updateRememberToken(AuthenticatableInterface $user, string $token): void
    {
        throw new BadMethodCallException();
    }

    public function retrieveByCredentials(array $credentials): ?AuthenticatableInterface
    {
        $users = $this->users;

        foreach ($credentials as $key => $value) {
            $users = $users->filter(fn (GenericUser $user) => $user->{$key} === $value);
        }

        return $users->first();
    }

    public function validateCredentials(AuthenticatableInterface $user, array $credentials): bool
    {
        foreach ($credentials as $key => $value) {
            if ($user->{$key} !== $value) {
                return false;
            }
        }

        return true;
    }
}
