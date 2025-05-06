<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Factory\ReservationEquipementFactory;
use App\Factory\EquipementFactory;
use App\Factory\StatutFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Symfony\Component\HttpFoundation\Response;

class ReservationEquipementApiTest extends WebTestCase
{
    use Factories;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $entityManager = $container->get('doctrine')->getManager();
        $connection = $entityManager->getConnection();

        $schemaManager = $connection->createSchemaManager();
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();

        // Suppression et création du schéma
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        // Chargement des fixtures
        $statutFixtures = new \App\DataFixtures\StatutFixtures();
        $statutFixtures->load($entityManager);

        $userFixtures = new \App\DataFixtures\UserFixtures($container->get('security.password_hasher'));
        $userFixtures->load($entityManager);

        $equipementFixtures = new \App\DataFixtures\EquipementFixtures();
        $equipementFixtures->load($entityManager);

        $reservationEquipementFixtures = new \App\DataFixtures\ReservationEquipementFixtures();
        $reservationEquipementFixtures->load($entityManager);
    }

    private function authenticate(string $email, string $password): string
    {
        $this->client->request('POST', '/api/login_check', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'username' => $email,
            'password' => $password,
        ]));

        $response = json_decode($this->client->getResponse()->getContent(), true);

        return $response['token'] ?? '';
    }
    public function testGetReservationsAsAdmin(): void
    {
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('GET', '/api/reservation_equipements', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $this->assertResponseIsSuccessful();
    }
    public function testGetReservationsAsUser(): void
    {
        $token = $this->authenticate('user@gmail.com', '@Password1234!');

        $this->client->request('GET', '/api/reservation_equipements', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $this->assertResponseStatusCodeSame(403);
    }
    public function testCreateReservation(): void
    {
        $token = $this->authenticate('user@gmail.com', '@Password1234!');

        $user = UserFactory::find(['email' => 'user@gmail.com']);
        $equipement = EquipementFactory::first();
        $statut = StatutFactory::first();

        $dateReservation = (new \DateTime('2025-05-10'))->format('Y-m-d');
        $heureDebut = (new \DateTime('08:00:00'))->format('H:i:s');
        $heureFin = (new \DateTime('10:00:00'))->format('H:i:s');

        $this->client->request('POST', '/api/reservation_equipements', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'User' => '/api/users/' . $user->getId(),
            'Equipement' => '/api/equipements/' . $equipement->getId(),
            'Statut' => '/api/statuts/' . $statut->getId(),
            'dateReservation' => $dateReservation,
            'heureDebut' => $heureDebut,
            'heureFin' => $heureFin,
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testUpdateReservation(): void
    {
        $token = $this->authenticate('user@gmail.com', '@Password1234!');

        $user = UserFactory::find(['email' => 'user@gmail.com']);
        $equipement = EquipementFactory::first();
        $statut = StatutFactory::first();

        $dateReservation = (new \DateTime('2025-05-10'))->format('Y-m-d');
        $heureDebut = (new \DateTime('08:00:00'))->format('H:i:s');
        $heureFin = (new \DateTime('10:00:00'))->format('H:i:s');

        $this->client->request('POST', '/api/reservation_equipements', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'User' => '/api/users/' . $user->getId(),
            'Equipement' => '/api/equipements/' . $equipement->getId(),
            'Statut' => '/api/statuts/' . $statut->getId(),
            'dateReservation' => $dateReservation,
            'heureDebut' => $heureDebut,
            'heureFin' => $heureFin,
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $reservationId = $responseData['id'];

        $newHeureDebut = (new \DateTime('10:00:00'))->format('H:i:s');
        $newHeureFin = (new \DateTime('12:00:00'))->format('H:i:s');

        $this->client->request('PUT', '/api/reservation_equipements/' . $reservationId, [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'User' => '/api/users/' . $user->getId(),
            'Equipement' => '/api/equipements/' . $equipement->getId(),
            'Statut' => '/api/statuts/' . $statut->getId(),
            'dateReservation' => $dateReservation,
            'heureDebut' => $newHeureDebut,
            'heureFin' => $newHeureFin,
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    public function testDeleteReservation(): void
    {
        $token = $this->authenticate('user@gmail.com', '@Password1234!');

        $reservation = ReservationEquipementFactory::new([
            'dateReservation' => new \DateTime('2025-05-10'),
            'heureDebut' => new \DateTime('09:00:00'),
            'heureFin' => new \DateTime('11:00:00'),
            'User' => UserFactory::find(['email' => 'user@gmail.com']),
            'Equipement' => EquipementFactory::first(),
            'Statut' => StatutFactory::first()
        ])->create();

        $this->client->request('DELETE', '/api/reservation_equipements/' . $reservation->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $this->assertResponseStatusCodeSame(204);

        $this->client->request('GET', '/api/reservation_equipements/' . $reservation->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);
        $this->assertResponseStatusCodeSame(404);
    }
}
