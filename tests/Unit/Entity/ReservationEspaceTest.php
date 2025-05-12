<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\ReservationEspace;
use App\Entity\Statut;
use App\Entity\User;
use App\Entity\EspaceTravail;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ReservationEspace::class)]
class ReservationEspaceTest extends TestCase
{
    public function testGetSetId(): void
    {
        $reservation = new ReservationEspace();
        $reflection = new \ReflectionProperty(ReservationEspace::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($reservation, 1);

        $this->assertSame(1, $reservation->getId());
    }

    public function testGetSetDateReservation(): void
    {
        $reservation = new ReservationEspace();
        $date = new \DateTime('2023-12-01');

        $reservation->setDateReservation($date);

        $this->assertSame($date, $reservation->getDateReservation());
    }

    public function testGetSetHeureDebut(): void
    {
        $reservation = new ReservationEspace();
        $heureDebut = new \DateTime('10:00');

        $reservation->setHeureDebut($heureDebut);

        $this->assertSame($heureDebut, $reservation->getHeureDebut());
    }

    public function testGetSetHeureFin(): void
    {
        $reservation = new ReservationEspace();
        $heureFin = new \DateTime('12:00');

        $reservation->setHeureFin($heureFin);

        $this->assertSame($heureFin, $reservation->getHeureFin());
    }

    public function testGetSetStatut(): void
    {
        $reservation = new ReservationEspace();
        $statut = $this->createMock(Statut::class);

        $reservation->setStatut($statut);

        $this->assertSame($statut, $reservation->getStatut());
    }

    public function testGetSetUser(): void
    {
        $reservation = new ReservationEspace();
        $user = $this->createMock(User::class);

        $reservation->setUser($user);

        $this->assertSame($user, $reservation->getUser());
    }

    public function testGetSetEspaceTravail(): void
    {
        $reservation = new ReservationEspace();
        $espaceTravail = $this->createMock(EspaceTravail::class);

        $reservation->setEspaceTravail($espaceTravail);

        $this->assertSame($espaceTravail, $reservation->getEspaceTravail());
    }

    public function testGetLinks(): void
    {
        $reservation = new ReservationEspace();
        $reflection = new \ReflectionProperty(ReservationEspace::class, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($reservation, 1);

        $expectedLinks = [
            'self' => '/api/reservation_espaces/1',
            'update' => '/api/reservation_espaces/1',
            'delete' => '/api/reservation_espaces/1',
        ];

        $this->assertSame($expectedLinks, $reservation->getLinks());
    }

    public function testValidateNoOverlapNoConflict(): void
    {
        $espaceTravail = $this->createMock(EspaceTravail::class);
        $reservation = new ReservationEspace();
        $reservation->setEspaceTravail($espaceTravail);
        $reservation->setDateReservation(new \DateTime('2024-06-01'));
        $reservation->setHeureDebut(new \DateTime('10:00'));
        $reservation->setHeureFin(new \DateTime('12:00'));

        // Simule aucune réservation existante
        $espaceTravail->method('getReservationEspaces')->willReturn(new ArrayCollection());

        $context = $this->createMock(\Symfony\Component\Validator\Context\ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $reservation->validateNoOverlap($context);
    }

    public function testValidateNoOverlapWithConflict(): void
    {
        $espaceTravail = $this->createMock(EspaceTravail::class);
        $reservation = new ReservationEspace();
        $reservation->setEspaceTravail($espaceTravail);
        $reservation->setDateReservation(new \DateTime('2024-06-01'));
        $reservation->setHeureDebut(new \DateTime('10:00'));
        $reservation->setHeureFin(new \DateTime('12:00'));

        $other = new ReservationEspace();
        $other->setEspaceTravail($espaceTravail);
        $other->setDateReservation(new \DateTime('2024-06-01'));
        $other->setHeureDebut(new \DateTime('11:00'));
        $other->setHeureFin(new \DateTime('13:00'));

        $espaceTravail->method('getReservationEspaces')->willReturn(new ArrayCollection([$other]));

        $violationBuilder = $this->getMockBuilder(\Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $violationBuilder->expects($this->once())->method('atPath')->with('heureDebut')->willReturnSelf();
        $violationBuilder->expects($this->once())->method('addViolation');

        $context = $this->createMock(\Symfony\Component\Validator\Context\ExecutionContextInterface::class);
        $context->expects($this->once())->method('buildViolation')->willReturn($violationBuilder);

        $reservation->validateNoOverlap($context);
    }

    public function testValidateNoOverlapWithNullEspaceTravail(): void
    {
        $reservation = new ReservationEspace();
        $reservation->setEspaceTravail(null);

        $context = $this->createMock(\Symfony\Component\Validator\Context\ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $reservation->validateNoOverlap($context);
    }

    public function testValidateNoOverlapWithSelfReservation(): void
    {
        $espaceTravail = $this->createMock(EspaceTravail::class);
        $reservation = new ReservationEspace();
        $reservation->setEspaceTravail($espaceTravail);
        $reservation->setDateReservation(new \DateTime('2024-06-01'));
        $reservation->setHeureDebut(new \DateTime('10:00'));
        $reservation->setHeureFin(new \DateTime('12:00'));

        // La collection contient la réservation elle-même
        $espaceTravail->method('getReservationEspaces')->willReturn(new \Doctrine\Common\Collections\ArrayCollection([$reservation]));

        $context = $this->createMock(\Symfony\Component\Validator\Context\ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $reservation->validateNoOverlap($context);
    }

    public function testValidateNoOverlapWithDifferentDate(): void
    {
        $espaceTravail = $this->createMock(EspaceTravail::class);
        $reservation = new ReservationEspace();
        $reservation->setEspaceTravail($espaceTravail);
        $reservation->setDateReservation(new \DateTime('2024-06-01'));
        $reservation->setHeureDebut(new \DateTime('10:00'));
        $reservation->setHeureFin(new \DateTime('12:00'));

        $other = new ReservationEspace();
        $other->setEspaceTravail($espaceTravail);
        $other->setDateReservation(new \DateTime('2024-06-02')); // Date différente
        $other->setHeureDebut(new \DateTime('11:00'));
        $other->setHeureFin(new \DateTime('13:00'));

        $espaceTravail->method('getReservationEspaces')->willReturn(new \Doctrine\Common\Collections\ArrayCollection([$other]));

        $context = $this->createMock(\Symfony\Component\Validator\Context\ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $reservation->validateNoOverlap($context);
    }
}
