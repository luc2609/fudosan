<?php

namespace App\Time;

use DateTimeInterface;

trait UnixTimestampSerializable
{
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
