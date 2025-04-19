<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StatutApiTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $entityManager = $container->get('doctrine')->getManager();
        $passwordHasher = $container->get('security.password_hasher');

        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        $fixture = new \App\DataFixtures\AppFixtures($passwordHasher);
        $fixture->load($entityManager);
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

    public function testGetStatuts(): void
    {
        $token = $this->authenticate('user@gmail.com', '@Password1234!');

        $this->client->request('GET', '/api/statuts', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('@context', $responseData);
        $this->assertArrayHasKey('@id', $responseData);
        $this->assertArrayHasKey('@type', $responseData);
        $this->assertArrayHasKey('totalItems', $responseData);
        $this->assertArrayHasKey('member', $responseData);
        $this->assertCount(4, $responseData['member']); // 4 statuts créés dans les fixtures
    }
    public function testGetById(): void
    {
        $token = $this->authenticate('user@gmail.com', '@Password1234!');

        $this->client->request('GET', '/api/statuts/1', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('En attente', $responseData['libelle']);
    }
    public function testCreateStatutAsAdmin(): void
    {
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('POST', '/api/statuts', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'libelle' => 'Nouveau Statut',
        ]));

        $this->assertResponseStatusCodeSame(201);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('libelle', $responseData);
        $this->assertSame('Nouveau Statut', $responseData['libelle']);
    }

    public function testCreateStatutAsUser(): void
    {
        $token = $this->authenticate('user@gmail.com', '@Password1234!');

        $this->client->request('POST', '/api/statuts', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'libelle' => 'Statut Interdit',
        ]));

        $this->assertResponseStatusCodeSame(403);
    }
    public function testUpdate(): void
    {
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('PUT', '/api/statuts/1', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'libelle' => 'Statut Modifié',
        ]));

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('Statut Modifié', $responseData['libelle']);
    }
    public function testDelete(): void
    {
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('DELETE', '/api/statuts/1', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $this->assertResponseStatusCodeSame(204);

        $this->client->request('GET', '/api/statuts/1', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);
        $this->assertResponseStatusCodeSame(404);
    }
    public function testLibelleVide(): void
    {
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('POST', '/api/statuts', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'libelle' => '',
        ]));

        $this->assertResponseStatusCodeSame(422);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('violations', $responseData);
        $this->assertSame('libelle', $responseData['violations'][0]['propertyPath']);
        $this->assertSame('Le libellé ne peut pas être vide', $responseData['violations'][0]['message']);
    }
    public function testLibelleTropCourt(): void
    {
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('POST', '/api/statuts', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'libelle' => 'A',
        ]));

        $this->assertResponseStatusCodeSame(422);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('violations', $responseData);
        $this->assertSame('libelle', $responseData['violations'][0]['propertyPath']);
        $this->assertSame('Le libellé doit contenir au moins 2 caractères', $responseData['violations'][0]['message']);
    }
    public function testLibelleNonUnique(): void
    {
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        // Tenter de créer un second statut avec le même libellé
        $this->client->request('POST', '/api/statuts', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'libelle' => 'En attente',
        ]));

        $this->assertResponseStatusCodeSame(422);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('violations', $responseData);
        $this->assertSame('libelle', $responseData['violations'][0]['propertyPath']);
        $this->assertSame('Ce libellé est déjà utilisé. Veuillez en choisir un autre.', $responseData['violations'][0]['message']);
    }
}