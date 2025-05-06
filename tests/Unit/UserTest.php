<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class UserTest extends TestCase
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
    public function testGetId(): void
    {
        $user = new User();
        $reflection = new \ReflectionClass($user);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($user, 1);

        $this->assertSame(1, $user->getId());
    }
    public function testSetAndGetEmail(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');

        $this->assertSame('test@example.com', $user->getEmail());
    }
    public function testSetAndGetNom(): void
    {
        $user = new User();
        $user->setNom('Doe');

        $this->assertSame('Doe', $user->getNom());
    }
    public function testSetAndGetPrenom(): void
    {
        $user = new User();
        $user->setPrenom('John');

        $this->assertSame('John', $user->getPrenom());
    }
    public function testSetAndGetRoles(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);

        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles()); // ROLE_USER est ajouté par défaut
    }
    public function testSetAndGetPassword(): void
    {
        $user = new User();
        $user->setPassword('hashedPassword');

        $this->assertSame('hashedPassword', $user->getPassword());
    }
    public function testSetAndGetPlainPassword(): void
    {
        $user = new User();
        $user->setPlainPassword('plainPassword');

        $this->assertSame('plainPassword', $user->getPlainPassword());
    }
    public function testEraseCredentials(): void
    {
        $user = new User();
        $user->setPlainPassword('plainPassword');
        $user->eraseCredentials();

        $this->assertNull($user->getPlainPassword());
    }
    public function testGetUserIdentifier(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');

        $this->assertSame('test@example.com', $user->getUserIdentifier());
    }
    public function testGetLinks(): void
    {
        $user = new User();
        $reflection = new \ReflectionClass($user);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($user, 1);

        $expectedLinks = [
            'self' => '/api/users/1',
            'update' => '/api/users/1',
            'delete' => '/api/users/1',
        ];

        $this->assertSame($expectedLinks, $user->getLinks());
    }
    public function testAddAndRemoveReservationEquipement(): void
    {
        $user = new User();
        $reservation = $this->createMock(\App\Entity\ReservationEquipement::class);

        $reservation->expects($this->exactly(2))
            ->method('setUser')
            ->willReturnSelf();

        $reservation->expects($this->exactly(1))
            ->method('getUser')
            ->willReturn($user);

        $user->addReservationEquipement($reservation);
        $this->assertTrue($user->getReservationEquipements()->contains($reservation));

        $user->removeReservationEquipement($reservation);
        $this->assertFalse($user->getReservationEquipements()->contains($reservation));
    }
}