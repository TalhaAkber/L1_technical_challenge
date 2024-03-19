<?php

namespace App\Response;

class CountResponse
{
    public int $counter;

    public function getCounter(): int
    {
        return $this->counter;
    }

    public function setCounter(int $counter): void
    {
        $this->counter = $counter;
    }
}
