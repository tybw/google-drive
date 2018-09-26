<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Changes.
 *
 * @ORM\Table(name="changes")
 * @ORM\Entity(repositoryClass="App\Repository\ChangesRepository")
 */
class Changes
{
    /**
     * @var GuidType|string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="channel_id", type="string", nullable=false)
     */
    private $channelId;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", nullable=false, options={"defaults":""})
     */
    private $token;

    /**
     * @var int
     *
     * @ORM\Column(name="message_number", type="integer", nullable=false)
     */
    private $messageNumber;

    /**
     * @var int
     *
     * @ORM\Column(name="page_token", type="integer", nullable=true)
     */
    private $pageToken;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="expire_at", type="datetimetz_immutable", nullable=false)
     */
    private $expireAt;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="json", nullable=false, options={"jsonb": true})
     */
    private $content;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="created_at", type="datetimetz_immutable", nullable=false)
     */
    private $createdAt;

    public function __construct()
    {
        //$this->id = Uuid::uuid4();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getChannelId()
    {
        return $this->channelId;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getMessageNumber(): int
    {
        return $this->messageNumber;
    }

    public function getPageToken(): ?int
    {
        return $this->pageToken;
    }

    public function getExpireAt(): \DateTimeImmutable
    {
        return $this->expireAt;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setChannelId(string $channelId): self
    {
        $this->channelId = $channelId;

        return $this;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function setMessageNumber(int $number): self
    {
        $this->messageNumber = $number;

        return $this;
    }

    public function setPageToken(?int $pageToken): self
    {
        $this->pageToken = $pageToken;

        return $this;
    }

    public function setExpireAt(\DateTimeImmutable $expireAt): self
    {
        $this->expireAt = $expireAt;

        return $this;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
