<?php

namespace App\Entity;

use App\Repository\WebLinkTesterRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WebLinkTesterRepository::class)]
class WebLinkTester
{
    CONST LENGTH_LINK = 2048;
    CONST LENGTH_SLUG = 255;
    CONST LENGTH_HEADERS = 8192;
    CONST LENGTH_CONTENT = 1281920;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(type: 'text', length: self::LENGTH_LINK)]
    #[Assert\Url]
    protected string $link;

    #[ORM\Column(type: 'text', length: self::LENGTH_SLUG)]
    protected string $slug;

    #[ORM\Column(type: 'datetime')]
    protected ?\DateTimeInterface $createdDate;

    #[ORM\Column(type: 'text', length: self::LENGTH_HEADERS, nullable: true)]
    protected ?string $headers;

    #[ORM\Column(type: 'text', length: self::LENGTH_CONTENT, nullable: true)]
    protected ?string $content;

    #[ORM\Column(type: 'integer')]
    protected int $statusCode;

    public function __construct(){
        $this->createdDate = new \DateTime('now');
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(?\DateTimeInterface $createdDate): void
    {
        $this->createdDate = $createdDate;
    }

    public function getHeaders(): ?string
    {
        return $this->headers;
    }

    public function setHeaders(string $headers): void
    {
        $this->headers = $headers;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

}