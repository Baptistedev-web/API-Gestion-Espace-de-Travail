<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\ReservationEquipement;
use App\Entity\Statut;
use App\Entity\User;
use App\Entity\Equipement;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ReservationEquipement::class)]
class ReservationEquipementTest extends TestCase
{
    public function testGetSetId(): void
    {
        $reservation = new ReservationEquipement();
        $reflection = new \ReflectionProperty(ReservationEquipement::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($reservation, 1);

        $this->assertSame(1, $reservation->getId());
    }

    public function testGetSetDateReservation(): void
    {
        $reservation = new ReservationEquipement();
        $date = new \DateTime('2023-12-01');

        $reservation->setDateReservation($date);

        $this->assertSame($date, $reservation->getDateReservation());
    }

    public function testGetSetHeureDebut(): void
    {
        $reservation = new ReservationEquipement();
        $heureDebut = new \DateTime('10:00');

        $reservation->setHeureDebut($heureDebut);

        $this->assertSame($heureDebut, $reservation->getHeureDebut());
    }

    public function testGetSetHeureFin(): void
    {
        $reservation = new ReservationEquipement();
        $heureFin = new \DateTime('12:00');

        $reservation->setHeureFin($heureFin);

        $this->assertSame($heureFin, $reservation->getHeureFin());
    }

    public function testGetSetStatut(): void
    {
        $reservation = new ReservationEquipement();
        $statut = $this->createMock(Statut::class);

        $reservation->setStatut($statut);

        $this->assertSame($statut, $reservation->getStatut());
    }

    public function testGetSetUser(): void
    {
        $reservation = new ReservationEquipement();
        $user = $this->createMock(User::class);

        $reservation->setUser($user);

        $this->assertSame($user, $reservation->getUser());
    }

    public function testGetSetEquipement(): void
    {
        $reservation = new ReservationEquipement();
        $equipement = $this->createMock(Equipement::class);

        $reservation->setEquipement($equipement);

        $this->assertSame($equipement, $reservation->getEquipement());
    }

    public function testGetLinks(): void
    {
        $reservation = new ReservationEquipement();
        $reflection = new \ReflectionProperty(ReservationEquipement::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($reservation, 1);

        $expectedLinks = [
            'self' => '/api/reservation_equipements/1',
            'update' => '/api/reservation_equipements/1',
            'delete' => '/api/reservation_equipements/1',
        ];

        $this->assertSame($expectedLinks, $reservation->getLinks());
    }

    public function testValidateNoOverlapNoConflict(): void
    {
        $equipement = $this->createMock(Equipement::class);
        $reservation = new ReservationEquipement();
        $reservation->setEquipement($equipement);
        $reservation->setDateReservation(new \DateTime('2024-06-01'));
        $reservation->setHeureDebut(new \DateTime('10:00'));
        $reservation->setHeureFin(new \DateTime('12:00'));

        // Simule aucune réservation existante
        $equipement->method('getReservationEquipements')->willReturn(new ArrayCollection());

        $context = $this->createMock(\Symfony\Component\Validator\Context\ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $reservation->validateNoOverlap($context);
    }

    public function testValidateNoOverlapWithConflict(): void
    {
        $equipement = $this->createMock(Equipement::class);
        $reservation = new ReservationEquipement();
        $reservation->setEquipement($equipement);
        $reservation->setDateReservation(new \DateTime('2024-06-01'));
        $reservation->setHeureDebut(new \DateTime('10:00'));
        $reservation->setHeureFin(new \DateTime('12:00'));

        $other = new ReservationEquipement();
        $other->setEquipement($equipement);
        $other->setDateReservation(new \DateTime('2024-06-01'));
        $other->setHeureDebut(new \DateTime('11:00'));
        $other->setHeureFin(new \DateTime('13:00'));

        $equipement->method('getReservationEquipements')->willReturn(new ArrayCollection([$other]));

        $violationBuilder = $this->getMockBuilder(\Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $violationBuilder->expects($this->once())->method('atPath')->with('heureDebut')->willReturnSelf();
        $violationBuilder->expects($this->once())->method('addViolation');

        $context = $this->createMock(\Symfony\Component\Validator\Context\ExecutionContextInterface::class);
        $context->expects($this->once())->method('buildViolation')->willReturn($violationBuilder);

        $reservation->validateNoOverlap($context);
    }

    public function testValidateNoOverlapWithNullEquipement(): void
    {
        $reservation = new ReservationEquipement();
        $reservation->setEquipement(null);

        $context = $this->createMock(\Symfony\Component\Validator\Context\ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $reservation->validateNoOverlap($context);
    }

    public function testValidateNoOverlapWithSelfReservation(): void
    {
        $equipement = $this->createMock(Equipement::class);
        $reservation = new ReservationEquipement();
        $reservation->setEquipement($equipement);
        $reservation->setDateReservation(new \DateTime('2024-06-01'));
        $reservation->setHeureDebut(new \DateTime('10:00'));
        $reservation->setHeureFin(new \DateTime('12:00'));

        // La collection contient la réservation elle-même
        $equipement->method('getReservationEquipements')->willReturn(new \Doctrine\Common\Collections\ArrayCollection([$reservation]));

        $context = $this->createMock(\Symfony\Component\Validator\Context\ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $reservation->validateNoOverlap($context);
    }

    public function testValidateNoOverlapWithDifferentDate(): void
    {
        $equipement = $this->createMock(Equipement::class);
        $reservation = new ReservationEquipement();
        $reservation->setEquipement($equipement);
        $reservation->setDateReservation(new \DateTime('2024-06-01'));
        $reservation->setHeureDebut(new \DateTime('10:00'));
        $reservation->setHeureFin(new \DateTime('12:00'));

        $other = new ReservationEquipement();
        $other->setEquipement($equipement);
        $other->setDateReservation(new \DateTime('2024-06-02')); // Date différente
        $other->setHeureDebut(new \DateTime('11:00'));
        $other->setHeureFin(new \DateTime('13:00'));

        $equipement->method('getReservationEquipements')->willReturn(new \Doctrine\Common\Collections\ArrayCollection([$other]));

        $context = $this->createMock(\Symfony\Component\Validator\Context\ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $reservation->validateNoOverlap($context);
    }
}
