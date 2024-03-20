<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class SerializerProvider
{
    private static Serializer $serializer;
    public static function getSerializer(): Serializer
    {
        $normalizers = [new ObjectNormalizer(), new DateTimeNormalizer()];
        $encoders = [new JsonEncoder()];

        if (empty(self::$serializer)) {
            self::$serializer = new Serializer($normalizers, $encoders);
        }
        return self::$serializer;
    }
}