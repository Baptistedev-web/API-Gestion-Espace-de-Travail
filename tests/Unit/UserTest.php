<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\Operation;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\State\UserStateProcessor;
use App\DataPersister\UserDataPersister;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
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
    public function testProcessUpdatesPasswordWhenModified(): void
    {
        $user = new User();
        $reflection = new \ReflectionClass($user);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($user, 1);
        $user->setPlainPassword('new_password');

        $existingUser = new User();
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($existingUser, 1);
        $existingUser->setPassword('old_hashed_password');

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')
            ->willReturnCallback(function () use ($existingUser) {
                return new class($existingUser) {
                    private $existingUser;
                    public function __construct($existingUser) { $this->existingUser = $existingUser; }
                    public function find($id) { return $this->existingUser; }
                };
            });

        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $operation = $this->createMock(Operation::class);

        $passwordHasher->method('hashPassword')
            ->willReturn('new_hashed_password');

        $entityManager->expects($this->once())->method('persist')->with($user);
        $entityManager->expects($this->once())->method('flush');

        $dataPersister = new UserDataPersister($entityManager, $passwordHasher);
        $result = $dataPersister->process($user, $operation);

        $this->assertSame('new_hashed_password', $result->getPassword());
    }

    public function testProcessKeepsOldPasswordWhenNotModified(): void
    {
        $user = new User();
        $reflection = new \ReflectionClass($user);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($user, 1);

        $existingUser = new User();
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($existingUser, 1);
        $existingUser->setPassword('old_hashed_password');

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->with(1)->willReturn($existingUser);


        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->with(User::class)->willReturn($repository);

        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $operation = $this->createMock(Operation::class);

        $entityManager->expects($this->once())->method('persist')->with($user);
        $entityManager->expects($this->once())->method('flush');

        $dataPersister = new UserDataPersister($entityManager, $passwordHasher);
        $result = $dataPersister->process($user, $operation);

        $this->assertSame('old_hashed_password', $result->getPassword());
    }
    public function testProcessThrowsExceptionForInvalidData(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Data must be an instance of User.');

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $operation = $this->createMock(Operation::class);

        $dataPersister = new UserDataPersister($entityManager, $passwordHasher);
        $dataPersister->process(new \stdClass(), $operation); // Objet non-User
    }
    public function testProcessSetsDefaultRolesForNewUser(): void
    {
        $user = new User();

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $operation = $this->createMock(Operation::class);

        $entityManager->expects($this->once())->method('persist')->with($user);
        $entityManager->expects($this->once())->method('flush');

        $dataPersister = new UserDataPersister($entityManager, $passwordHasher);
        $dataPersister->process($user, $operation);

        $this->assertSame(['ROLE_USER'], $user->getRoles());
    }
    public function testProcessThrowsExceptionWhenUserNotFound(): void
    {
        $user = new User();
        $reflection = new \ReflectionClass($user);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($user, 1);

        $entityRepository = $this->createMock(\Doctrine\ORM\EntityRepository::class);
        $entityRepository->method('find')->with(1)->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->with(User::class)->willReturn($entityRepository);

        $passwordHasher = $this->createMock(\App\Security\PasswordHasher::class);
        $operation = $this->createMock(Operation::class);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Utilisateur non trouvé');

        $stateProcessor = new UserStateProcessor($entityManager, $passwordHasher);
        $stateProcessor->process($user, $operation);
    }
    public function testProcessHashesPasswordWhenPlainPasswordProvided(): void
    {
        $user = new User();
        $reflection = new \ReflectionClass($user);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($user, 1);
        $user->setPlainPassword('new_password');

        $existingUser = new User();
        $property->setValue($existingUser, 1);
        $existingUser->setPassword('old_hashed_password');

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->with(1)->willReturn($existingUser);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->with(User::class)->willReturn($repository);

        $passwordHasher = $this->createMock(\App\Security\PasswordHasher::class);
        $passwordHasher->method('hashPassword')->with($user, 'new_password')->willReturn('new_hashed_password');

        $entityManager->expects($this->once())->method('persist')->with($user);
        $entityManager->expects($this->once())->method('flush');

        $stateProcessor = new UserStateProcessor($entityManager, $passwordHasher);
        $result = $stateProcessor->process($user, $this->createMock(Operation::class));

        $this->assertSame('new_hashed_password', $result->getPassword());
        $this->assertNull($result->getPlainPassword());
    }
    public function testProcessKeepsExistingPasswordWhenPlainPasswordNotProvided(): void
    {
        $user = new User();
        $reflection = new \ReflectionClass($user);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($user, 1);

        $existingUser = new User();
        $property->setValue($existingUser, 1);
        $existingUser->setPassword('old_hashed_password');

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->with(1)->willReturn($existingUser);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->with(User::class)->willReturn($repository);

        $passwordHasher = $this->createMock(\App\Security\PasswordHasher::class);

        $entityManager->expects($this->once())->method('persist')->with($user);
        $entityManager->expects($this->once())->method('flush');

        $stateProcessor = new UserStateProcessor($entityManager, $passwordHasher);
        $result = $stateProcessor->process($user, $this->createMock(Operation::class));

        $this->assertSame('old_hashed_password', $result->getPassword());
    }
}