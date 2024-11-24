<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserRequest
{
    #[NotBlank]
    private string $username;

    #[Email]
    #[Blank]
    private string $email;

    #[NotBlank]
    #[Length(min: 8)]
    private string $password;

    #[NotBlank]
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

    /**
     * @param string $confirmPassword
     */
    public function setConfirmPassword(string $confirmPassword): void
    {
        $this->confirmPassword = $confirmPassword;
    }

    public function getConfirmPassword(): string
    {
        return $this->confirmPassword;
    }
}
