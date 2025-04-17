<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EquipementApiTest extends WebTestCase
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
    public function testGetEquipements(): void
    {
        $token = $this->authenticate('user@gmail.com', '@Password1234!');

        $this->client->request('GET', '/api/equipements', [], [], [
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
        $this->assertCount(10, $responseData['member']);

        $this->assertSame('/api/equipements/1', $responseData['member'][0]['@id']);
        $this->assertSame("Equipement 1", $responseData['member'][0]['nom']);
        $this->assertSame("Description de l'équipement 1", $responseData['member'][0]['description']);
    }
    public function testGetEquipementById(): void
    {
        $token = $this->authenticate('user@gmail.com', '@Password1234!');

        $this->client->request('GET', '/api/equipements/1', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame(1, $responseData['id']);
        $this->assertSame("Equipement 1", $responseData['nom']);
    }
    public function testCreateEquipementAsAdmin(): void
    {
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('POST', '/api/equipements', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'nom' => 'Nouveau Equipement',
            'description' => 'Description du nouvel équipement',
        ]));

        $this->assertResponseStatusCodeSame(201);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('nom', $responseData);
        $this->assertSame('Nouveau Equipement', $responseData['nom']);
    }
    public function testCreateEquipementAsUser(): void
    {
        $token = $this->authenticate('user@gmail.com', '@Password1234!');

        $this->client->request('POST', '/api/equipements', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'nom' => 'Nouveau Equipement',
            'description' => 'Description du nouvel équipement',
        ]));

        $this->assertResponseStatusCodeSame(403);
    }
    public function testUpdateEquipementAsAdmin(): void
    {
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('PUT', '/api/equipements/1', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'nom' => 'Equipement Modifié',
            'description' => 'Description modifiée',
        ]));

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('nom', $responseData);
        $this->assertSame('Equipement Modifié', $responseData['nom']);
    }
    public function testUpdateEquipementAsUser(): void
    {
        $token = $this->authenticate('user@gmail.com', '@Password1234!');

        $this->client->request('PUT', '/api/equipements/1', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'nom' => 'Equipement Modifié',
            'description' => 'Description modifiée',
        ]));

        $this->assertResponseStatusCodeSame(403);
    }
    public function testDeleteEquipementAsAdmin(): void
    {
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('DELETE', '/api/equipements/1', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $this->assertResponseStatusCodeSame(204);

        $this->client->request('GET', '/api/equipements/1', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);
        $this->assertResponseStatusCodeSame(404);
    }
    public function testDeleteEquipementAsUser(): void
    {
        $token = $this->authenticate('user@gmail.com', '@Password1234!');

        $this->client->request('DELETE', '/api/equipements/1', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $this->assertResponseStatusCodeSame(403);
    }
    public function testNomVide(): void
    {
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('POST', '/api/equipements', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'nom' => '',
            'description' => 'Description valide',
        ]));

        $this->assertResponseStatusCodeSame(422);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('violations', $responseData);
        $this->assertSame('nom', $responseData['violations'][0]['propertyPath']);
        $this->assertSame('Le nom ne doit pas être vide.', $responseData['violations'][0]['message']);
    }
    public function testNomTropCourt(): void
    {
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('POST', '/api/equipements', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'nom' => 'AB',
            'description' => 'Description valide',
        ]));

        $this->assertResponseStatusCodeSame(422);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('violations', $responseData);
        $this->assertSame('nom', $responseData['violations'][0]['propertyPath']);
        $this->assertSame('Le nom doit contenir au moins 3 caractères.', $responseData['violations'][0]['message']);
    }
    public function testNomAvecBalisesHtml(): void
    {
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('POST', '/api/equipements', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'nom' => '<b>Nom invalide</b>',
            'description' => 'Description valide.',
        ]));

        $this->assertResponseStatusCodeSame(422);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('violations', $responseData);
        $this->assertSame('nom', $responseData['violations'][0]['propertyPath']);
        $this->assertSame('Le nom ne doit contenir que des lettres (y compris avec accents), des chiffres et des espaces.', $responseData['violations'][0]['message']);
    }
    public function testNomAvecCaractereInterdit(): void
    {
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('POST', '/api/equipements', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'nom' => 'Nom{}Invalide',
            'description' => 'Description valide.',
        ]));

        $this->assertResponseStatusCodeSame(422);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('violations', $responseData);
        $this->assertSame('nom', $responseData['violations'][0]['propertyPath']);
        $this->assertSame('Le nom ne doit contenir que des lettres (y compris avec accents), des chiffres et des espaces.', $responseData['violations'][0]['message']);
    }
    public function testNomAvecParentheses(): void
    {
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('POST', '/api/equipements', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'nom' => 'Nom (Invalide)',
            'description' => 'Description valide.',
        ]));

        $this->assertResponseStatusCodeSame(422);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('violations', $responseData);
        $this->assertSame('nom', $responseData['violations'][0]['propertyPath']);
        $this->assertSame('Le nom ne doit contenir que des lettres (y compris avec accents), des chiffres et des espaces.', $responseData['violations'][0]['message']);
    }
    public function testDescriptionVide(): void
    {
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('POST', '/api/equipements', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'nom' => 'Nom valide',
            'description' => '',
        ]));

        $this->assertResponseStatusCodeSame(422);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('violations', $responseData);
        $this->assertSame('description', $responseData['violations'][0]['propertyPath']);
        $this->assertSame('La description ne doit pas être vide.', $responseData['violations'][0]['message']);
    }
    public function testDescriptionTropCourte(): void
    {
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('POST', '/api/equipements', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'nom' => 'Nom valide',
            'description' => 'Courte',
        ]));

        $this->assertResponseStatusCodeSame(422);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('violations', $responseData);
        $this->assertSame('description', $responseData['violations'][0]['propertyPath']);
        $this->assertSame('La description doit contenir au moins 10 caractères.', $responseData['violations'][0]['message']);
    }
    public function testDescriptionAvecBalisesHtml(): void
    {
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('POST', '/api/equipements', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'nom' => 'Nom valide',
            'description' => '<b>Description invalide</b>',
        ]));

        $this->assertResponseStatusCodeSame(422);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('violations', $responseData);
        $this->assertSame('description', $responseData['violations'][0]['propertyPath']);
        $this->assertSame('La description ne doit contenir que des lettres (y compris avec accents), des chiffres et des espaces.', $responseData['violations'][0]['message']);
    }
    public function testDescriptionAvecCaractereInterdit(): void
    {
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('POST', '/api/equipements', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'nom' => 'Nom valide',
            'description' => 'Description {} invalide.',
        ]));

        $this->assertResponseStatusCodeSame(422);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('violations', $responseData);
        $this->assertSame('description', $responseData['violations'][0]['propertyPath']);
        $this->assertSame('La description ne doit contenir que des lettres (y compris avec accents), des chiffres et des espaces.', $responseData['violations'][0]['message']);
    }
    public function testDescriptionAvecParentheses(): void
    {
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('POST', '/api/equipements', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'nom' => 'Nom valide',
            'description' => 'Description (invalide).',
        ]));

        $this->assertResponseStatusCodeSame(422);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('violations', $responseData);
        $this->assertSame('description', $responseData['violations'][0]['propertyPath']);
        $this->assertSame('La description ne doit contenir que des lettres (y compris avec accents), des chiffres et des espaces.', $responseData['violations'][0]['message']);
    }
}