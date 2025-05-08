<?php

namespace App\DataPersister;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @implements ProcessorInterface<User, User|null>
 */
class UserDataPersister implements ProcessorInterface
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Process the provided data (creation or update of a User entity).
     *
     * @param User $data
     * @param Operation $operation
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     * @return User
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        // Vérifiez que $data est bien une instance de l'entité User
        if (!($data instanceof User)) {
            throw new \InvalidArgumentException('Data must be an instance of User.');
        }

        // Si l'utilisateur est nouveau, on définit les rôles et hash le mot de passe
        if (null === $data->getId()) {
            $data->setRoles(['ROLE_USER']);
        }

        $plainPassword = $data->getPlainPassword();
        if ($plainPassword) {
            $hashedPassword = $this->passwordHasher->hashPassword($data, $plainPassword);
            $data->setPassword($hashedPassword);
            $data->eraseCredentials();
        } else {
            $existingUser = $this->entityManager->getRepository(User::class)->find($data->getId());
            if ($existingUser && $existingUser->getPassword() !== null) {
                $data->setPassword($existingUser->getPassword());
            }
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}