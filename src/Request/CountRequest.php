<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CountRequest
{
    #[Assert\Type('array')]
    private ?array $serviceNames = null;

    /**
     * @var string A "Y-m-d H:i:s" formatted value
     */
    #[Assert\DateTime]
    private ?string $startDate = null;

    /**
     * @var string A "Y-m-d H:i:s" formatted value
     */
    #[Assert\DateTime]
    private ?string $endDate = null;

    #[Assert\Type('integer')]
    #[Assert\GreaterThanOrEqual(100)]
    private ?int $statusCode = null;

    public function getServiceNames(): ?array
    {
        return $this->serviceNames;
    }

    public function setServiceNames(array $serviceNames): void
    {
        $this->serviceNames = $serviceNames;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate ? new \DateTime($this->startDate) : null;
    }

    public function setStartDate(string $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate ? new \DateTime($this->endDate) : null;
    }

    public function setEndDate(string $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }
}