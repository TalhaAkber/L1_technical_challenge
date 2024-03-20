<?php

namespace App\Entity;

use App\Repository\FileReadHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: FileReadHistoryRepository::class)]
class FileReadHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "SEQUENCE")]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $readFrom = null;

    #[ORM\Column]
    private ?int $readTill = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $timestamp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReadFrom(): ?int
    {
        return $this->readFrom;
    }

    public function setReadFrom(int $readFrom): void
    {
        $this->readFrom = $readFrom;
    }

    public function getReadTill(): ?int
    {
        return $this->readTill;
    }

    public function setReadTill(int $readTill): void
    {
        $this->readTill = $readTill;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
