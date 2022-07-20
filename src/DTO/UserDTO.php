<?php

namespace App\DTO;

use JMS\Serializer\Annotation as Serializer;

class UserDTO
{
    #[Serializer\Type("string")]
    public string $username;

    #[Serializer\Type("string")]
    public string $password;

    #[Serializer\Type("array")]
    public array $roles;

    #[Serializer\Type("float")]
    public float $balance;

    #[Serializer\Type("string")]
    public string $token;
}