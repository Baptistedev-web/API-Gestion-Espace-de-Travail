<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserDataPersister implements ContextAwareDataPersisterInterface
{
    private $decorated;
    private $passwordHasher;

    public function __construct(ContextAwareDataPersisterInterface $decorated, UserPasswordHasherInterface $passwordHasher)
    {
        $this->decorated = $decorated;
        $this->passwordHasher = $passwordHasher;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    public function persist($data, array $context = []): object
    {
        if ($data instanceof User) {
            if (isset($context['collection_operation_name']) && $context['collection_operation_name'] === 'post') {
                if ($data->getPassword()) {
                    $data->setPassword($this->passwordHasher->hashPassword($data, $data->getPassword()));
                }

                $data->setRoles(['ROLE_USER']);
            }

            if (isset($context['item_operation_name']) && $context['item_operation_name'] === 'put') {
                $existingPassword = $this->decorated->persist($data, $context)->getPassword();
                if ($data->getPassword() && $data->getPassword() !== $existingPassword) {
                    $data->setPassword($this->passwordHasher->hashPassword($data, $data->getPassword()));
                } else {
                    $data->setPassword($existingPassword);
                }

                $data->setRoles($data->getRoles());
            }
        }
        return $this->decorated->persist($data, $context);
    }

    public function remove($data, array $context = []): void
    {
        $this->decorated->remove($data, $context);
    }
}