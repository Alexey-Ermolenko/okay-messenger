<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserFriendsRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: UserFriendsRequestRepository::class)]
#[ORM\Table(name: 'user_friends_request')]
class UserFriendsRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: 'user_id', type: Types::INTEGER)]
    private int $user_id;

    #[ORM\Column(name: 'friend_id', type: Types::INTEGER)]
    private int $friend_id;

    #[ORM\Column(name: 'requested_at', type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $requestedAt;

    #[ORM\Column(name: 'responded_at', type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $respondedAt;

    #[ORM\Column(type: Types::STRING)]
    private string $accepted;

    #[MaxDepth(1)]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'sentRequests')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public User $user;

    #[MaxDepth(1)]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'receivedRequests')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public User $friend;

    public function __construct()
    {
        $this->setRequestedAt(new \DateTimeImmutable());

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getRequestedAt(): \DateTimeImmutable
    {
        return $this->requestedAt;
    }

    public function setRequestedAt(\DateTimeImmutable $requestedAt): static
    {
        $this->requestedAt = $requestedAt;

        return $this;
    }

    public function getRespondedAt(): \DateTimeImmutable
    {
        return $this->respondedAt;
    }

    public function setRespondedAt(\DateTimeImmutable $respondedAt): static
    {
        $this->respondedAt = $respondedAt;

        return $this;
    }

    public function setAccepted(string $accepted): void
    {
        $this->accepted = $accepted;
    }

    public function getAccepted(): string
    {
        return $this->accepted;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function getFriendId(): int
    {
        return $this->friend_id;
    }

    public function setFriendId(int $friend_id): void
    {
        $this->friend_id = $friend_id;
    }
}
