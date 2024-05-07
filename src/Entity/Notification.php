<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'ok_notification')]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: 'to_user_id', type: Types::INTEGER)]
    private int $to_user_id;

    #[ORM\Column(name: 'from_user_id', type: Types::INTEGER)]
    private int $from_user_id;

//    /** One User have one Notification. */
//    #[ORM\ManyToOne(targetEntity: User::class)]
//    public User $toUser;
//
//    /** One User have one Notification. */
//    #[ORM\ManyToOne(targetEntity: User::class)]
//    public User $fromUser;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $delivered;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE)]
    private DateTimeInterface $createdAt;

    /**
     * @param int $from_user_id
     * @param int $to_user_id
     */
    public function __construct(int $from_user_id, int $to_user_id)
    {
        $this->setCreatedAt(new DateTimeImmutable());
        $this->setFromUserId($from_user_id);
        $this->setToUserId($to_user_id);

        return $this;
    }

    public function sendNotify(): self
    {
        $this->delivered = true;
        return $this;
    }

    /**
     * @return int
     */
    public function getToUserId(): int
    {
        return $this->to_user_id;
    }

    /**
     * @param int $to_user_id
     */
    public function setToUserId(int $to_user_id): void
    {
        $this->to_user_id = $to_user_id;
    }

    /**
     * @return int
     */
    public function getFromUserId(): int
    {
        return $this->from_user_id;
    }

    /**
     * @param int $from_user_id
     */
    public function setFromUserId(int $from_user_id): void
    {
        $this->from_user_id = $from_user_id;
    }

    /**
     * @return bool
     */
    public function getDelivered(): bool
    {
        return $this->delivered;
    }

    /**
     * @param bool $delivered
     */
    public function setDelivered(bool $delivered): void
    {
        $this->delivered = $delivered;
    }

    /**
     * @return DateTimeInterface
     */
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface $createdAt
     */
    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

//    /**
//     * @return User|null
//     */
//    public function getToUser(): ?User
//    {
//        return $this->toUser;
//    }
//
//    /**
//     * @param User|null $toUser
//     */
//    public function setToUser(?User $toUser): void
//    {
//        $this->toUser = $toUser;
//    }
//
//    /**
//     * @return User|null
//     */
//    public function getFromUser(): ?User
//    {
//        return $this->fromUser;
//    }
//
//    /**
//     * @param User|null $fromUser
//     */
//    public function setFromUser(?User $fromUser): void
//    {
//        $this->fromUser = $fromUser;
//    }
}
