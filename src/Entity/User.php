<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Validator\UniqueEmail;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 250, unique: true)]
    #[Assert\Email]
    #[UniqueEmail]
    private ?string $email = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(nullable: true)]
    #[Assert\Length(
        min: 8,
        max: 50
    )]
    #[Assert\PasswordStrength(
        minScore: PasswordStrength::STRENGTH_WEAK,
    )]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tokenVerifiy = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $tokenVerifyDateCreated = null;

    /**
     * @var Collection<int, WebLinkSchedule>
     */
    #[ORM\OneToMany(targetEntity: WebLinkSchedule::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $webLinkSchedules;

    public function __construct()
    {
        $this->webLinkSchedules = new ArrayCollection();
        $this->active = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getTokenVerifiy(): ?string
    {
        return $this->tokenVerifiy;
    }

    public function setTokenVerifiy(?string $tokenVerifiy): static
    {
        $this->tokenVerifiy = $tokenVerifiy;

        return $this;
    }

    public function getTokenVerifyDateCreated(): ?\DateTimeInterface
    {
        return $this->tokenVerifyDateCreated;
    }

    public function setTokenVerifyDateCreated(?\DateTimeInterface $tokenVerifyDateCreated): static
    {
        $this->tokenVerifyDateCreated = $tokenVerifyDateCreated;

        return $this;
    }

    public function isGranted($role): bool
    {
        return in_array('ROLE_'.$role, $this->getRoles());
    }

    /**
     * @return Collection<int, WebLinkSchedule>
     */
    public function getWebLinkSchedules(): Collection
    {
        return $this->webLinkSchedules;
    }

    public function addWebLinkSchedule(WebLinkSchedule $webLinkSchedule): static
    {
        if (!$this->webLinkSchedules->contains($webLinkSchedule)) {
            $this->webLinkSchedules->add($webLinkSchedule);
            $webLinkSchedule->setUser($this);
        }

        return $this;
    }

    public function removeWebLinkSchedule(WebLinkSchedule $webLinkSchedule): static
    {
        if ($this->webLinkSchedules->removeElement($webLinkSchedule)) {
            // set the owning side to null (unless already changed)
            if ($webLinkSchedule->getUser() === $this) {
                $webLinkSchedule->setUser(null);
            }
        }

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): void
    {
        $this->active = $active;
    }
}
