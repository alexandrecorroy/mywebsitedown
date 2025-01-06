<?php

namespace App\Entity;

use App\Repository\WebLinkScheduleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WebLinkScheduleRepository::class)]
class WebLinkSchedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 54)]
    #[Assert\Length(
        min: 5,
        max: 50,
        minMessage: 'Name must be at least {{ limit }} characters long',
        maxMessage: 'Name cannot be longer than {{ limit }} characters',
    )]
    private ?string $name = null;

    #[ORM\Column(length: 2048)]
    #[Assert\Url]
    private ?string $link = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Choice([2, 3, 4, 5])]
    private ?int $statusCodeVerifying = null;

    #[ORM\Column]
    #[Assert\Choice([300, 3600, 43200, 86400])]
    private ?int $cronInSeconds = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column]
    private ?bool $emailAlert = null;

    /**
     * @var Collection<int, WebLink>
     */
    #[ORM\OneToMany(targetEntity: WebLink::class, mappedBy: 'webLinkSchedule', orphanRemoval: true)]
    private Collection $webLinks;

    #[ORM\ManyToOne(inversedBy: 'webLinkSchedules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->emailAlert = true;
        $this->webLinks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return Collection<int, WebLink>
     */
    public function getWebLinks(): Collection
    {
        return $this->webLinks;
    }

    public function addWebLink(WebLink $webLink): static
    {
        if (!$this->webLinks->contains($webLink)) {
            $this->webLinks->add($webLink);
            $webLink->setWebLinkSchedule($this);
        }

        return $this;
    }

    public function removeWebLink(WebLink $webLink): static
    {
        if ($this->webLinks->removeElement($webLink)) {
            // set the owning side to null (unless already changed)
            if ($webLink->getWebLinkSchedule() === $this) {
                $webLink->setWebLinkSchedule(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->getUser();
    }

    public function getStatusCodeVerifying(): ?int
    {
        return $this->statusCodeVerifying;
    }

    public function setStatusCodeVerifying(?int $statusCodeVerifying): void
    {
        $this->statusCodeVerifying = $statusCodeVerifying;
    }

    public function getCronInSeconds(): ?int
    {
        return $this->cronInSeconds;
    }

    public function setCronInSeconds(?int $cronInSeconds): void
    {
        $this->cronInSeconds = $cronInSeconds;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): void
    {
        $this->active = $active;
    }

    public function getEmailAlert(): ?bool
    {
        return $this->emailAlert;
    }

    public function setEmailAlert(?bool $emailAlert): void
    {
        $this->emailAlert = $emailAlert;
    }

}
