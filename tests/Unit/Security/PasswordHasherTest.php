<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Security\PasswordHasher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[CoversClass(PasswordHasher::class)]
class PasswordHasherTest extends TestCase
{
    public function testConstruct(): void
    {
        $passwordHasherMock = $this->createMock(UserPasswordHasherInterface::class);
        $passwordHasher = new PasswordHasher($passwordHasherMock);

        $this->assertInstanceOf(PasswordHasher::class, $passwordHasher);
    }

    public function testHashPassword(): void
    {
        $userMock = $this->createMock(PasswordAuthenticatedUserInterface::class);
        $passwordHasherMock = $this->createMock(UserPasswordHasherInterface::class);

        $passwordHasherMock
            ->expects($this->once())
            ->method('hashPassword')
            ->with($userMock, 'plainPassword')
            ->willReturn('hashedPassword');

        $passwordHasher = new PasswordHasher($passwordHasherMock);
        $result = $passwordHasher->hashPassword($userMock, 'plainPassword');

        $this->assertSame('hashedPassword', $result);
    }
}