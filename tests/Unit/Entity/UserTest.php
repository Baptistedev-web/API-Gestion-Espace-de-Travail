<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\User;
use App\Entity\ReservationEquipement;
use App\Entity\ReservationEspace;
use PHPUnit\Framework\Attributes\CoversClass; // Correction de l'import
use PHPUnit\Framework\TestCase;

#[CoversClass(User::class)]
class UserTest extends TestCase
{
    public function testUserInitialization(): void
    {
        $user = new User();

        $this->assertSame(0, $user->getId());
        $this->assertSame('', $user->getEmail());
        $this->assertSame(['ROLE_USER'], $user->getRoles()); // ROLE_USER est ajouté par défaut
        $this->assertSame('', $user->getNom());
        $this->assertSame('', $user->getPrenom());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $user->getReservationEquipements());
    }
    public function testSetAndGetEmail(): void
    {
        $user = new User();
        $email = 'test@example.com';

        $user->setEmail($email);

        $this->assertSame($email, $user->getEmail());
    }
    public function testSetAndGetRoles(): void
    {
        $user = new User();
        $roles = ['ROLE_ADMIN'];

        $user->setRoles($roles);

        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles()); // ROLE_USER est ajouté par défaut
    }
    public function testSetAndGetPassword(): void
    {
        $user = new User();
        $password = 'hashed_password';

        $user->setPassword($password);

        $this->assertSame($password, $user->getPassword());
    }
    public function testSetAndGetNom(): void
    {
        $user = new User();
        $nom = 'Dupont';

        $user->setNom($nom);

        $this->assertSame($nom, $user->getNom());
    }
    public function testSetAndGetPrenom(): void
    {
        $user = new User();
        $prenom = 'Jean';

        $user->setPrenom($prenom);

        $this->assertSame($prenom, $user->getPrenom());
    }
    public function testAddAndRemoveReservationEquipement(): void
    {
        $user = new User();
        $reservation = $this->createMock(ReservationEquipement::class);

        $user->addReservationEquipement($reservation);

        $this->assertCount(1, $user->getReservationEquipements());
        $this->assertTrue($user->getReservationEquipements()->contains($reservation));

        $user->removeReservationEquipement($reservation);

        $this->assertCount(0, $user->getReservationEquipements());
    }
    public function testAddAndRemoveReservationEspace(): void
    {
        $user = new User();
        $reservation = $this->createMock(ReservationEspace::class);

        $reservation->expects($this->once())
            ->method('setUser')
            ->with($user);

        $user->addReservationEspace($reservation);

        $this->assertCount(1, $user->getReservationEspaces());
        $this->assertTrue($user->getReservationEspaces()->contains($reservation));

        $reservation->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("Impossible de supprimer l'utilisateur d'une réservation.");

        $user->removeReservationEspace($reservation);
    }
    public function testSetAndGetPlainPassword(): void
    {
        $user = new User();
        $plainPassword = 'plain_password';

        $user->setPlainPassword($plainPassword);

        $this->assertSame($plainPassword, $user->getPlainPassword());
    }
    public function testEraseCredentials(): void
    {
        $user = new User();
        $user->setPlainPassword('plain_password');

        $user->eraseCredentials();

        $this->assertNull($user->getPlainPassword());
    }
    public function testGetUsername(): void
    {
        $user = new User();
        $email = 'test@example.com';
        $user->setEmail($email);

        $this->assertSame($email, $user->getUsername());
    }
    public function testGetUserIdentifier(): void
    {
        $user = new User();
        $email = 'test@example.com';
        $user->setEmail($email);

        $this->assertSame($email, $user->getUserIdentifier());
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
    public function testRemoveReservationEquipementThrowsException(): void
    {
        $user = new User();
        $reservation = $this->createMock(ReservationEquipement::class);
        $reservation->method('getUser')->willReturn($user);

        $user->addReservationEquipement($reservation);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("Impossible de supprimer l'utilisateur d'une réservation.");

        $user->removeReservationEquipement($reservation);
    }
}

