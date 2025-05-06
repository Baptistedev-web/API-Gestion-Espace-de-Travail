<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Security\PasswordHasher;
use Doctrine\ORM\EntityManagerInterface;

class UserStateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PasswordHasher $passwordHasher
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        if ($data instanceof User) {
            // Cas de la création (POST)
            if (!$data->getId()) {
                $data->setRoles(['ROLE_USER']);
                if ($plainPassword = $data->getPlainPassword()) {
                    $hashedPassword = $this->passwordHasher->hashPassword($data, $plainPassword);
                    $data->setPassword($hashedPassword);
                    $data->eraseCredentials();
                }
            }
            // Cas de la modification (PUT/PATCH)
            else {
                $existingUser = $this->entityManager->getRepository(User::class)->find($data->getId());
                if (!$existingUser) {
                    throw new \RuntimeException('Utilisateur non trouvé');
                }

                // Mise à jour uniquement si le mot de passe a été modifié
                if ($plainPassword = $data->getPlainPassword()) {
                    $hashedPassword = $this->passwordHasher->hashPassword($data, $plainPassword);
                    $data->setPassword($hashedPassword);
                    $data->eraseCredentials();
                } else {
                    // Conserver l'ancien mot de passe si non modifié
                    $data->setPassword($existingUser->getPassword());
                }
            }

            $this->entityManager->persist($data);
            $this->entityManager->flush();
        }

        return $data;
    }
}