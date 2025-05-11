<?php

namespace App\Tests\DataPersister;

use ApiPlatform\Metadata\Operation;
use App\DataPersister\UserDataPersister;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[CoversClass(UserDataPersister::class)]
class UserDataPersisterTest extends TestCase
{
    /** @phpstan-ignore-next-line */
    private EntityManagerInterface $entityManager;
    /** @phpstan-ignore-next-line */
    private UserPasswordHasherInterface $passwordHasher;
    /** @phpstan-ignore-next-line */
    private UserDataPersister $dataPersister;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->dataPersister = new UserDataPersister($this->entityManager, $this->passwordHasher);
    }
    public function testConstructor(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $dataPersister = new UserDataPersister($entityManager, $passwordHasher);

        $this->assertInstanceOf(UserDataPersister::class, $dataPersister);
    }
    public function testProcessWithPlainPassword(): void
    {
        $user = new User();
        $user->setPlainPassword('password123');

        /** @phpstan-ignore-next-line */
        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->with($this->isInstanceOf(User::class), 'password123')
            ->willReturn('hashed_password');

        /** @phpstan-ignore-next-line */
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(User::class));

        /** @phpstan-ignore-next-line */
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->dataPersister->process($user, $this->createMock(Operation::class));

        $this->assertEquals('hashed_password', $user->getPassword());
        $this->assertInstanceOf(User::class, $result);
    }
    public function testProcessWithoutPlainPassword(): void
    {
        $user = new User();

        $reflection = new \ReflectionClass($user);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($user, 1);

        $existingUser = new User();
        $existingUser->setPassword('existing_hashed_password');

        $userRepository = $this->createMock(\Doctrine\ORM\EntityRepository::class);

        $userRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($existingUser);

        /** @phpstan-ignore-next-line */
        $this->entityManager
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($userRepository);

        /** @phpstan-ignore-next-line */
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($user);

        /** @phpstan-ignore-next-line */
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->dataPersister->process($user, $this->createMock(Operation::class));

        $this->assertEquals('existing_hashed_password', $user->getPassword());
        $this->assertInstanceOf(User::class, $result);
    }
    public function testProcessWithInvalidData(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Data must be an instance of User.');

        /**
         * @phpstan-ignore-next-line
         */
        $this->dataPersister->process(new \stdClass(), $this->createMock(Operation::class));
    }
}