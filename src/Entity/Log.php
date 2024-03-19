<?php

namespace App\Entity;

use App\Repository\LogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogRepository::class)]
class Log
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $serviceName = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $timestamp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $httpMethod = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $endpoint = null;

    #[ORM\Column(nullable: true)]
    private ?int $statusCode = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $rawData = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServiceName(): ?string
    {
        return $this->serviceName;
    }

    public function setServiceName(string $serviceName): void
    {
        $this->serviceName = $serviceName;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(string $timestamp): void
    {
        $this->timestamp = new \Datetime($timestamp);
    }

    public function getHttpMethod(): ?string
    {
        return $this->httpMethod;
    }

    public function setHttpMethod(?string $httpMethod): void
    {
        $this->httpMethod = $httpMethod;
    }

    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }

    public function setEndpoint(?string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function setStatusCode(?int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function getRawData(): ?string
    {
        return $this->rawData;
    }

    public function setRawData(?string $rawData): void
    {
        $this->rawData = $rawData;
    }
}
