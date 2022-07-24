<?php

namespace App\DTO;

use JMS\Serializer\Annotation as Serializer;

class CourseDTO
{
    #[Serializer\Type("string")]
    public string $character_code;

    #[Serializer\Type("int")]
    public int $type;

    #[Serializer\Type("float")]
    public float $price;
}