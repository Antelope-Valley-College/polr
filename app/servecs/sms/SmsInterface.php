<?php

namespace App\servecs\sms;


interface SmsInterface
{
    /**
     * @param mixed $number
     * @param string $text
     * @return mixed
     */
    public function send(mixed $number, string $text): mixed;
}
