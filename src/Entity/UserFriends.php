<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserFriendsRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: UserFriendsRepository::class)]
#[ORM\Table(name: 'user_friends')]
class UserFriends
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    /** Many Users have one userFriend. This is the owning side. */
    #[MaxDepth(1)]
    #[ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    //    #[ORM\ManyToOne(
    //        targetEntity: User::class,
    //        inversedBy: 'users'
    //    )]
    //    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    //    protected ArrayCollection $myFriends;
    //
    //    #[ORM\ManyToOne(
    //        targetEntity: User::class,
    //        inversedBy: 'friends'
    //    )]
    //    #[ORM\JoinColumn(name: 'friend_id', referencedColumnName: 'id')]
    //    protected ArrayCollection $friendsWithMe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }
}
