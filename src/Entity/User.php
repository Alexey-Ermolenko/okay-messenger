<?php

declare(strict_types=1);

namespace App\Entity;

use AllowDynamicProperties;
use App\Enum\NotificationPreference;
use App\Event\NotificationMethodChangedEvent;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[AllowDynamicProperties] #[ORM\Table(name: '`user`')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private string $username;

    #[ORM\Column(type: 'string', unique: true)]
    private string $email;

    #[ORM\Column(type: 'string', unique: true, nullable: true)]
    private ?string $telegramAccountLink = null;

    #[ORM\Column(type: 'string', unique: true, nullable: true)]
    private ?string $phoneNumber;

    #[ORM\Column(type: 'string', unique: true)]
    private string $preferredNotificationMethod;

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(type: 'json')]
    private array $roles;

    /**
     * Many Users have Many Users.
     *
     * @var ArrayCollection
     */
    #[MaxDepth(1)]
    #[ORM\ManyToMany(
        targetEntity: User::class,
        mappedBy: 'myFriends'
    )]
    public $friendsWithMe;

    /**
     * Many Users have many Users.
     *
     * @var ArrayCollection
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'friendsWithMe')]
    #[ORM\JoinTable(name: 'user_friends')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'friend_id', referencedColumnName: 'id')]
    #[MaxDepth(1)]
    public $myFriends;

    #[MaxDepth(1)]
    #[ORM\OneToMany(targetEntity: UserFriendsRequest::class, mappedBy: 'user', cascade: ['remove'])]
    public $sentRequests;

    #[MaxDepth(1)]
    #[ORM\OneToMany(targetEntity: UserFriendsRequest::class, mappedBy: 'friend', cascade: ['remove'])]
    public $receivedRequests;

    private ?EventDispatcherInterface $eventDispatcher = null;

    /**
     * SELECT u.id, u.username, f.*
     * FROM public.user u
     * JOIN public.user_friends uf ON u.id = uf.user_id
     * JOIN public.user f ON uf.friend_id = f.id
     * WHERE u.username = 'user1'.
     */
    public function __construct()
    {
        $this->friendsWithMe = new ArrayCollection();
        $this->myFriends = new ArrayCollection();
        $this->sentRequests = new ArrayCollection();
        $this->receivedRequests = new ArrayCollection();
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function addFriend(User $friend): self
    {
        if (!$this->myFriends->contains($friend)) {
            $this->myFriends[] = $friend;
        }

        return $this;
    }

    public function removeFriend(User $friend): self
    {
        if ($this->myFriends->contains($friend)) {
            $this->myFriends->removeElement($friend);
        }

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getTelegramAccountLink(): ?string
    {
        return $this->telegramAccountLink;
    }

    public function setTelegramAccountLink(?string $telegramAccountLink): self
    {
        $this->telegramAccountLink = $telegramAccountLink;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getPreferredNotificationMethod(): string
    {
        return $this->preferredNotificationMethod;
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function setPreferredNotificationMethod(string $preferredNotificationMethod): self
    {
        if (
            $preferredNotificationMethod === NotificationPreference::Telegram->value
            && $this->telegramAccountLink === null
        ) {
            throw new \InvalidArgumentException("Telegram link cannot be null when using Telegram.");
        }

        $this->preferredNotificationMethod = $preferredNotificationMethod;

        $event = new NotificationMethodChangedEvent(
            $preferredNotificationMethod,
            $this->getTelegramAccountLink(),
            $this->getPhoneNumber(),
            $this->getEmail(),
        );

        $this->eventDispatcher->dispatch($event);

        return $this;
    }
}
