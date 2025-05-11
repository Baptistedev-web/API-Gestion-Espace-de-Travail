<?php

namespace Tests\Functional\DataFixturesAndFactory;

use App\DataFixtures\UserFixtures;
use App\Factory\UserFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Foundry\Story;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[CoversClass(UserFixtures::class)]
#[CoversClass(UserFactory::class)]
class UserFixturesAndFactoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        UserFactory::repository()->truncate();

        /** @var UserPasswordHasherInterface $userPasswordHasher */
        $userPasswordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);

        /** @var \Doctrine\Persistence\ObjectManager $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');

        $userFixtures = new UserFixtures($userPasswordHasher);
        $userFixtures->load($entityManager);
    }
    public function testUserFixtures(): void
    {
        $this->assertCount(12, UserFactory::repository()->findAll());

        $adminUser = UserFactory::repository()->findOneBy(['email' => 'admin@icloud.com']);
        $this->assertNotNull($adminUser);
        $this->assertContains('ROLE_ADMIN', $adminUser->getRoles());
    }
    public function testUserFactory(): void
    {
        $user = UserFactory::createOne(['email' => 'test@example.com']);

        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertNotEmpty($user->getPassword());
    }
}