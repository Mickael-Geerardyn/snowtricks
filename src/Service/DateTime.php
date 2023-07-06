<?php

namespace App\Service;

use DateTimeImmutable;
use DateTimeZone;
use Exception;

class DateTime
{
    private DateTimeImmutable|string $dateTimeImmutable;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->dateTimeImmutable = new DateTimeImmutable("now", new DateTimeZone("America/Toronto"));
    }

    public function getDateTime(): string
    {
        return $this->dateTimeImmutable->format("d-m-Y");
    }
}