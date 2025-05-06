<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class UserApiTest extends WebTestCase
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
    private function createUserForTest(string $role = 'ROLE_USER'): array
    {
        $email = 'testuser' . uniqid() . '@example.com';
        $password = '@Password1234!';

        $user = new \App\Entity\User();
        $user->setNom('Test');
        $user->setPrenom('User');
        $user->setEmail($email);
        $user->setRoles([$role]);
        $user->setPassword(
            $this->client->getContainer()->get('security.password_hasher')->hashPassword($user, $password)
        );

        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return ['id' => $user->getId(), 'email' => $email, 'password' => $password];
    }
    public function testCreateUser(): void
    {
        $this->client->request('POST', '/api/users', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
        ], json_encode([
            'email' => 'newuser@example.com',
            'password' => '@Password1234!',
            'nom' => 'New',
            'prenom' => 'User',
        ]));

        $this->assertResponseStatusCodeSame(201);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('newuser@example.com', $responseData['email']);
    }
    public function testUpdateOwnUser(): void
    {
        $userCredentials = $this->createUserForTest('ROLE_USER');
        $token = $this->authenticate($userCredentials['email'], $userCredentials['password']);

        $this->client->request('PUT', '/api/users/' . $userCredentials['id'], [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'email' => 'newemail@example.com',
            'nom' => 'NomModifié',
            'prenom' => 'PrenomModifié',
            'plainPassword' => '@NewPassword1234!',
        ]));

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('NomModifié', $responseData['nom']);
    }
    public function testDeleteOwnUser(): void
    {
        $userCredentials = $this->createUserForTest('ROLE_USER');
        $token = $this->authenticate($userCredentials['email'], $userCredentials['password']);

        $this->client->request('DELETE', '/api/users/' . $userCredentials['id'], [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $this->assertResponseStatusCodeSame(204);
    }
    public function testDeleteAnotherUser(): void
    {
        $userCredentials = $this->createUserForTest('ROLE_USER');
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('DELETE', '/api/users/' . $userCredentials['id'], [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $this->assertResponseStatusCodeSame(403);
    }
    public function testUpdateAnotherUser(): void
    {
        $userCredentials = $this->createUserForTest('ROLE_USER');
        $token = $this->authenticate('admin@icloud.com', '@Password1234!');

        $this->client->request('PUT', '/api/users/' . $userCredentials['id'], [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode([
            'nom' => 'Hacked',
            'prenom' => 'User',
        ]));

        $this->assertResponseStatusCodeSame(403);
    }
    public function testGetUserById(): void
    {
        $token = $this->authenticate('user@gmail.com', '@Password1234!');

        $this->client->request('GET', '/api/users/1', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertSame(1, $responseData['id']);
        $this->assertSame('user@gmail.com', $responseData['email']);
        $this->assertSame('user', $responseData['nom']);
        $this->assertSame('user', $responseData['prenom']);
    }
    public function testGetAnotherUserById(): void
    {
        $user = UserFactory::createOne(['email' => 'testuser@example.com']);
        $token = $this->authenticate('user@gmail.com', '@Password1234!');

        $this->client->request('GET', '/api/users/' . $user->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $this->assertResponseStatusCodeSame(403);
    }
}
