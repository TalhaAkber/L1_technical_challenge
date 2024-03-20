<?php

namespace App\Provider;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class ObjectSerializer
{
    private Serializer $serializer;
    public function __construct()
    {
        $normalizers = [new ObjectNormalizer(), new DateTimeNormalizer()];
        $encoders = [new JsonEncoder()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    final public function deserialize(mixed $data, string $type, string $format = 'json', array $context = []): mixed
    {
        return $this->serializer->deserialize(json_encode($data), $type, $format, $context);
    }
}