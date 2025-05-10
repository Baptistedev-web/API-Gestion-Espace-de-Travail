<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

#[CoversClass(UserRepository::class)]
class UserRepositoryTest extends TestCase
{
    public function testUpgradePassword(): void
    {
        $user = new User();
        $user->setPassword('oldPassword');

        // Mock de ClassMetadata
        $classMetadata = $this->createMock(\Doctrine\ORM\Mapping\ClassMetadata::class);
        $classMetadata->name = User::class;

        // Mock de l'EntityManager
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())->method('persist')->with($user);
        $entityManager->expects($this->once())->method('flush');
        $entityManager->method('getClassMetadata')->with(User::class)->willReturn($classMetadata);

        // Mock du ManagerRegistry
        $managerRegistry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $managerRegistry->method('getManager')->willReturn($entityManager);
        $managerRegistry->method('getManagerForClass')->with(User::class)->willReturn($entityManager);

        // Création du UserRepository
        $repository = new UserRepository($managerRegistry);

        // Appel de la méthode à tester
        $repository->upgradePassword($user, 'newHashedPassword');

        // Vérification
        $this->assertSame('newHashedPassword', $user->getPassword());
    }
    public function testUpgradePasswordWithUnsupportedUser(): void
    {
        $this->expectException(UnsupportedUserException::class);

        $unsupportedUser = $this->createMock(\Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface::class);

        $managerRegistry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new UserRepository($managerRegistry);

        $repository->upgradePassword($unsupportedUser, 'newHashedPassword');
    }
}