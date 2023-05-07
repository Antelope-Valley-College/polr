<?php

namespace App\servecs\sms;


interface SmsInterface
{
    /**
     * @param array $number
     * @param string $text
     * @return mixed
     */
    public function send( array $number, string $text);
}
