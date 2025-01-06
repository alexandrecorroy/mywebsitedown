<?php

namespace App\Entity;

use App\Repository\WebLinkDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WebLinkDetailRepository::class)]
class WebLinkDetail
{
    CONST LENGTH_HEADERS = 8192;
    CONST LENGTH_CONTENT = 1281920;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(type: 'text', length: self::LENGTH_HEADERS, nullable: true)]
    protected ?string $headers;

    #[ORM\Column(type: 'text', length: self::LENGTH_CONTENT, nullable: true)]
    protected ?string $content;

    #[ORM\OneToOne(inversedBy: 'webLinkDetail', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?WebLink $webLink = null;

    public function getHeaders(): ?string
    {
        return $this->headers;
    }

    public function setHeaders(string $headers): void
    {
        $this->headers = $headers;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getWebLink(): ?WebLink
    {
        return $this->webLink;
    }

    public function setWebLink(WebLink $webLink): static
    {
        $this->webLink = $webLink;

        return $this;
    }
}