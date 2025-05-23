<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

class UserRequest
{
    #[Blank]
    private string $username;

    #[Email]
    #[Blank]
    private ?string $email = null;

    #[Url]
    private ?string $telegramAccountLink;

    #[Blank]
    private ?string $phoneNumber = null;

    #[NotBlank]
    private string $preferredNotificationMethod;

    #[Blank]
    #[Length(min: 8)]
    private string $password;

    #[Blank]
    #[Length(min: 8)]
    #[EqualTo(propertyPath: 'password', message: 'This value should be equal to password field')]
    private string $confirmPassword;

    public function getUsername(): string
    {
        return $this->username;
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

    public function setConfirmPassword(string $confirmPassword): void
    {
        $this->confirmPassword = $confirmPassword;
    }

    public function getConfirmPassword(): string
    {
        return $this->confirmPassword;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getTelegramAccountLink(): ?string
    {
        return $this->telegramAccountLink;
    }

    public function setTelegramAccountLink(?string $telegramAccountLink): void
    {
        $this->telegramAccountLink = $telegramAccountLink;
    }

    public function getPreferredNotificationMethod(): string
    {
        return $this->preferredNotificationMethod;
    }

    public function setPreferredNotificationMethod(string $preferredNotificationMethod): void
    {
        $this->preferredNotificationMethod = $preferredNotificationMethod;
    }
}
