<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Table(name: '`user`')]
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
}
