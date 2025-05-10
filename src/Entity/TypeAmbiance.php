<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\TypeAmbianceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TypeAmbianceRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            description: 'Récupère une collection de ressources TypeAmbiance.',
            normalizationContext: ['groups' => ['getTypeAmbiance']]
        ),
        new Get(
            description: 'Récupère une ressource TypeAmbiance.',
            normalizationContext: ['groups' => ['getTypeAmbiance']]
        ),
        new Post(
            description: 'Crée une ressource TypeAmbiance.',
            denormalizationContext: ['groups' => ['getTypeAmbiance']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Put(
            description: 'Met à jour une ressource TypeAmbiance.',
            denormalizationContext: ['groups' => ['getTypeAmbiance']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Delete(
            description: 'Supprime une ressource TypeAmbiance.',
            security: "is_granted('ROLE_ADMIN')"
        )
    ],
    normalizationContext: ['groups' => ['getTypeAmbiance']],
    denormalizationContext: ['groups' => ['getTypeAmbiance']],
    formats: ['jsonld', 'json'],
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 100,
    cacheHeaders: [
        'max_age' => 3600, // Cache pour 1 heure
        'shared_max_age' => 3600,
        'vary' => ['Authorization', 'Accept-Language'],
    ],
)]
class TypeAmbiance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getTypeAmbiance'])]
    private int $id = 0;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Veuillez indiquer le libellé du type d'ambiance.")]
    #[Assert\Length(
        min: 5,
        max: 50,
        minMessage: 'Le libellé doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le libellé ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-ZÀ-ÿ\s\-]+$/u',
        message: 'Le libellé ne peut contenir que des lettres, espaces et tirets.'
    )]
    #[Groups(['getTypeAmbiance'])]
    private string $libelle = '';

    /**
     * @var Collection<int, EspaceCollaboration>
     */
    #[ORM\OneToMany(targetEntity: EspaceCollaboration::class, mappedBy: 'typeAmbiance', orphanRemoval: true)]
    #[Groups(['getTypeAmbiance'])]
    private Collection $EspaceCollaboration;

    public function __construct()
    {
        $this->EspaceCollaboration = new ArrayCollection();
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
     * @return Collection<int, EspaceCollaboration>
     */
    public function getEspaceCollaboration(): Collection
    {
        return $this->EspaceCollaboration;
    }

    public function addEspaceCollaboration(EspaceCollaboration $espaceCollaboration): static
    {
        if (!$this->EspaceCollaboration->contains($espaceCollaboration)) {
            $this->EspaceCollaboration->add($espaceCollaboration);
            $espaceCollaboration->setTypeAmbiance($this);
        }

        return $this;
    }

    public function removeEspaceCollaboration(EspaceCollaboration $espaceCollaboration): static
    {
        if ($this->EspaceCollaboration->removeElement($espaceCollaboration)) {
            // set the owning side to null (unless already changed)
            if ($espaceCollaboration->getTypeAmbiance() === $this) {
                throw new \LogicException('Impossible de supprimer l\'espace de collaboration car il est encore référencé.');
            }
        }

        return $this;
    }
    /**
     * @return array<string, string>
     */
    #[Groups(['getTypeAmbiance'])]
    public function getLinks(): array
    {
        return [
            'self' => '/api/type_ambiances/' . $this->id,
            'update' => '/api/type_ambiances/' . $this->id,
            'delete' => '/api/type_ambiances/' . $this->id,
        ];
    }
}
