<?php

namespace App\Entity;

use App\Repository\WebLinkRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WebLinkRepository::class)]
class WebLink
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $statusCode = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateVisited = null;

    #[ORM\ManyToOne(inversedBy: 'webLinks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?WebLinkSchedule $webLinkSchedule = null;

    #[ORM\OneToOne(mappedBy: 'webLink', cascade: ['persist', 'remove'])]
    private ?WebLinkDetail $webLinkDetail = null;

    public function __construct()
    {
        $this->dateVisited = new \DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function setStatusCode(?int $statusCode): static
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function getDateVisited(): ?\DateTimeInterface
    {
        return $this->dateVisited;
    }

    public function setDateVisited(\DateTimeInterface $dateVisited): static
    {
        $this->dateVisited = $dateVisited;

        return $this;
    }

    public function getWebLinkSchedule(): ?WebLinkSchedule
    {
        return $this->webLinkSchedule;
    }

    public function setWebLinkSchedule(?WebLinkSchedule $webLinkSchedule): static
    {
        $this->webLinkSchedule = $webLinkSchedule;

        return $this;
    }

    public function getWebLinkDetail(): ?WebLinkDetail
    {
        return $this->webLinkDetail;
    }

    public function setWebLinkDetail(WebLinkDetail $webLinkDetail): static
    {
        // set the owning side of the relation if necessary
        if ($webLinkDetail->getWebLink() !== $this) {
            $webLinkDetail->setWebLink($this);
        }

        $this->webLinkDetail = $webLinkDetail;

        return $this;
    }
}
