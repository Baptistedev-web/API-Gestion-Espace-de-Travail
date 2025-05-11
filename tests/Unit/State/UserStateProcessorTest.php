<?php

namespace Tests\Unit\State;

use App\Entity\User;
use App\Security\PasswordHasher;
use App\State\UserStateProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use ApiPlatform\Metadata\Operation;

class UserStateProcessorTest extends TestCase
{
    /** @phpstan-ignore-next-line */
    private EntityManagerInterface $entityManager;
    /** @phpstan-ignore-next-line */
    private PasswordHasher $passwordHasher;
    /** @phpstan-ignore-next-line */
    private UserStateProcessor $processor;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->passwordHasher = $this->createMock(PasswordHasher::class);
        $this->processor = new UserStateProcessor($this->entityManager, $this->passwordHasher);
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(UserStateProcessor::class, $this->processor);
    }

    public function testProcessThrowsExceptionForInvalidData(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected instance of User.');
        /** @phpstan-ignore-next-line */
        $this->processor->process(new \stdClass(), $this->createMock(Operation::class));
    }

    public function testProcessCreatesUser(): void
    {
        $user = new User();
        $user->setPlainPassword('password123');

        /** @phpstan-ignore-next-line */
        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->with($user, 'password123')
            ->willReturn('hashed_password');

        /** @phpstan-ignore-next-line */
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($user);

        /** @phpstan-ignore-next-line */
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->processor->process($user, $this->createMock(Operation::class));

        $this->assertEquals('hashed_password', $user->getPassword());
        $this->assertSame(['ROLE_USER'], $user->getRoles());
        $this->assertInstanceOf(User::class, $result);
    }

    public function testProcessUpdatesUser(): void
    {
        $user = new User();
        $reflection = new \ReflectionClass($user);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($user, 1); // Simule un ID défini par Doctrine

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

        $result = $this->processor->process($user, $this->createMock(Operation::class));

        $this->assertEquals('existing_hashed_password', $user->getPassword());
        $this->assertInstanceOf(User::class, $result);
    }
}