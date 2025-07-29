<?php

namespace App\Factory;

use App\Entity\TKUser;
use App\Repository\TKUserRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @extends ModelFactory<TKUser>
 *
 * @method        TKUser|Proxy create(array|callable $attributes = [])
 * @method static TKUser|Proxy createOne(array $attributes = [])
 * @method static TKUser|Proxy find(object|array|mixed $criteria)
 * @method static TKUser|Proxy findOrCreate(array $attributes)
 * @method static TKUser|Proxy first(string $sortedField = 'id')
 * @method static TKUser|Proxy last(string $sortedField = 'id')
 * @method static TKUser|Proxy random(array $attributes = [])
 * @method static TKUser|Proxy randomOrCreate(array $attributes = [])
 * @method static TKUserRepository|RepositoryProxy repository()
 * @method static TKUser[]|Proxy[] all()
 * @method static TKUser[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static TKUser[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static TKUser[]|Proxy[] findBy(array $attributes)
 * @method static TKUser[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static TKUser[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class TKUserFactory extends ModelFactory
{
    private UserPasswordHasherInterface $passwordHasher;
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->email(),
            'firstName' => self::faker()->firstName(),
            'roles' => [],
            'secondName' => self::faker()->lastName(),
            'plainPassword' => 'tata'
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(function(TKUser $tkUser): void {
                if ($tkUser->getPlainPassword()) {
                    $tkUser->setPassword($this->passwordHasher->hashPassword($tkUser, $tkUser->getPlainPassword()));
                }
            })
        ;
    }

    protected static function getClass(): string
    {
        return TKUser::class;
    }
}
