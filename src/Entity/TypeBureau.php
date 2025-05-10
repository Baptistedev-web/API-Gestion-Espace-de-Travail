<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\TypeBureauRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TypeBureauRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/type_bureaux',
            description: 'Récupère une collection de ressources TypeBureau.',
            normalizationContext: ['groups' => ['getTypeBureau']]
        ),
        new Get(
            uriTemplate: '/type_bureaux/{id}',
            description: 'Récupère une ressource TypeBureau.',
            normalizationContext: ['groups' => ['getTypeBureau']]
        ),
        new Post(
            uriTemplate: '/type_bureaux',
            description: 'Crée une ressource TypeBureau.',
            denormalizationContext: ['groups' => ['getTypeBureau']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Put(
            uriTemplate: '/type_bureaux/{id}',
            description: 'Met à jour une ressource TypeBureau.',
            denormalizationContext: ['groups' => ['getTypeBureau']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Delete(
            uriTemplate: '/type_bureaux/{id}',
            description: 'Supprime une ressource TypeBureau.',
            security: "is_granted('ROLE_ADMIN')"
        )
    ],
    normalizationContext: ['groups' => ['getTypeBureau']],
    denormalizationContext: ['groups' => ['getTypeBureau']],
    formats: ['jsonld', 'json'],
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 100,
    cacheHeaders: [
        'max_age' => 3600, // Cache pour 1 heure
        'shared_max_age' => 3600,
        'vary' => ['Authorization', 'Accept-Language'],
    ],
)]
class TypeBureau
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getTypeBureau'])]
    private int $id = 0;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Veuillez indiquer le libellé du type de bureau.")]
    #[Assert\Length(
        min: 5,
        max: 50,
        minMessage: "Le libellé doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le libellé ne doit pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-ZÀ-ÿ\s\-]+$/u',
        message: 'Le libellé ne peut contenir que des lettres, espaces et tirets.'
    )]
    #[Groups(['getTypeBureau'])]
    private string $libelle = '';

    /**
     * @var Collection<int, Bureau>
     */
    #[ORM\OneToMany(targetEntity: Bureau::class, mappedBy: 'typeBureau', orphanRemoval: true)]
    #[Groups(['getTypeBureau'])]
    private Collection $Bureau;

    public function __construct()
    {
        $this->Bureau = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, Bureau>
     */
    public function getBureau(): Collection
    {
        return $this->Bureau;
    }

    public function addBureau(Bureau $bureau): static
    {
        if (!$this->Bureau->contains($bureau)) {
            $this->Bureau->add($bureau);
            $bureau->setTypeBureau($this);
        }

        return $this;
    }

    public function removeBureau(Bureau $bureau): static
    {
        if ($this->Bureau->removeElement($bureau)) {
            // set the owning side to null (unless already changed)
            if ($bureau->getTypeBureau() === $this) {
                throw new \LogicException('Impossible de supprimer le type de bureau car il est encore utilisé par un bureau.');
            }
        }

        return $this;
    }
    /**
     * @return array<string, string>
     */
    #[Groups(['getTypeBureau'])]
    public function getLinks(): array
    {
        return [
            'self' => "/api/type_bureaux/".$this->id,
            'update' => "/api/type_bureaux/".$this->id,
            'delete' => "/api/type_bureaux/".$this->id,
        ];
    }
}
