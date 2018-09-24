<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class Account.
 *
 * @ORM\Table(name="account",
 *     uniqueConstraints={@ORM\UniqueConstraint(
 *         name="unique_email",
 *         columns={"email_canonical"}
 *     ),
 *     @ORM\UniqueConstraint(
 *         name="unique_user",
 *         columns={"username"}
 *     )
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 */
class Account implements UserInterface, SerializerInterface
{
    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(name="id", type="string", nullable=false)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="display_name", type="string", nullable=true)
     */
    private $displayName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="email_canonical", type="string", nullable=false)
     */
    private $emailCanonical;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", nullable=true)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="string", nullable=false)
     */
    private $roles;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="deletedAt", type="datetimetz_immutable", nullable=true)
     */
    private $deletedAt;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="expired_at", type="datetimetz_immutable", nullable=true)
     */
    private $expiredAt;

    /**
     * @var string
     *
     * @ORM\Column(name="reset_token", type="string", nullable=true)
     */
    private $resetToken;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="reset_requested_at", type="datetimetz_immutable", nullable=true)
     */
    private $resetRequestedAt;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="created_at", type="datetimetz_immutable", nullable=false)
     */
    private $createdAt;

    /**
     * Account constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->enabled = true;
    }

    public function serialize($data, $format, array $context = [])
    {
        return json_encode(
            [
                'id'       => $this->id,
                'username' => $this->username,
                'password' => $this->password,
                'salt'     => $this->getSalt()
            ]
        );
    }

    public function deserialize($data, $type, $format, array $context = [])
    {
        return json_decode($data);
    }

    public function isExpired(): bool
    {
        return $this->expiredAt instanceof \DateTimeImmutable &&
               $this->expiredAt <= new \DateTimeImmutable();
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt instanceof \DateTimeImmutable &&
               $this->deletedAt <= new \DateTimeImmutable();
    }

    public function getRoles()
    {
        // TODO: Implement getRoles() method.
    }

    public function getSalt()
    {
        return '';
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEmailCanonical(): ?string
    {
        return $this->emailCanonical;
    }

    public function setEmailCanonical(string $emailCanonical): self
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setRoles(string $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getResetRequestedAt(): ?\DateTimeImmutable
    {
        return $this->resetRequestedAt;
    }

    public function setResetRequestedAt(?\DateTimeImmutable $resetRequestedAt): self
    {
        $this->resetRequestedAt = $resetRequestedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getExpiredAt(): ?\DateTimeImmutable
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(\DateTimeImmutable $expiredAt): self
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
